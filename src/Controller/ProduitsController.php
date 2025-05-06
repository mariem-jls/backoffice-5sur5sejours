<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Attachment;
use App\Entity\Typeproduit;
use Psr\Log\LoggerInterface;
use App\Form\TypeproduitType;
use App\Entity\TypeProduitPhoto;
use App\Service\TypeProduiteService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeproduitRepository;
use App\Entity\TypeProduitConditionnement;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProduitsController extends AbstractController
{
    private TypeProduiteService $produitService;

    public function __construct(TypeProduiteService $produitService)
    {
        $this->produitService = $produitService;
    }

    /**
     * @Route("/produits", name="produits_index", methods={"GET"})
     */
    public function index(TypeproduitRepository $produitsRepository, Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('q', '');
        $produits = $this->produitService->produitlistTypeAll();

        if ($searchQuery) {
            $produits = $this->produitService->findBySearchQuery($searchQuery);
        }

        $pagination = $paginator->paginate(
            $produits, // Query or array of items
            $request->query->getInt('page', 1), // Current page number, defaults to 1
            10 // Items per page
        );

        // Check if the request is an AJAX request
        if ($request->isXmlHttpRequest()) {
            return $this->render('produits/_list.html.twig', [
                'pagination' => $pagination
            ]);
        }

        return $this->render('produits/index.html.twig', [
            'pagination' => $pagination,
            'searchQuery' => $searchQuery
        ]);
    }



    /**
     * @Route("/PublierTypeProduit", name="PublierTypeProduit", methods={"GET", "POST"})
     */
    public function publierTypeProduit(Request $request, EntityManagerInterface $em): Response
    {
        $id = $request->request->get('id');
        $statut = $request->request->get('statut');

        // Check if the required data is available
        if (isset($id) && isset($statut)) {
            $typeProduit = $em->getRepository(Typeproduit::class)->find($id);

            if ($typeProduit) {
                $typeProduit->setStatut($statut);
                $em->persist($typeProduit);
                $em->flush();

                return new JsonResponse(['status' => 'TypeProduit updated!', 'statut' => $typeProduit->getStatut()], Response::HTTP_OK);
            } else {
                return new JsonResponse(['error' => 'TypeProduit not found.'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return new JsonResponse(['error' => 'Invalid data.' . $statut], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/produits/new", name="produits_new", methods={"GET","POST"})
     */
    public function new(TypeproduitRepository $produitsRepository, LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            $labeletype = $request->request->get('labeletype');
            $caracteristiques = $request->request->get('caracteristiques');
            $delais = $request->request->get('delais');
            $tarifs = $request->request->get('tarifs');
            $statut = $request->request->get('statut');
            $reversement = $request->request->get('reversement');

            // Create a new Typeproduit entity
            $typeproduit = new Typeproduit();
            $typeproduit->setLabeletype($labeletype);
            $typeproduit->setCaracteristiques($caracteristiques);
            $typeproduit->setDelais($delais);
            $typeproduit->setTarifs($tarifs);
            $typeproduit->setStatut($statut);
            $typeproduit->setReversement($reversement);

            // Handle attachments
            $attachments = $request->files->get('attachments');
            $logger->info('Request data', [
                'files' => $attachments,
            ]);
            if (is_array($attachments)) {
                foreach ($attachments as $file) {
                    if ($file) {
                        try {
                            $attachment = new Attachment();
                            $mimeType = $file->getMimeType();
                            $extension = $file->guessExtension();

                            if ($extension === null) {
                                throw new \Exception("Unable to guess the extension for file: " . $file->getClientOriginalName() . " with MIME type: " . $mimeType);
                            }

                            $fileName = uniqid() . '.' . $extension;
                            $file->move($this->getParameter('produit_directory'), $fileName);
                            $attachment->setPath($fileName);
                            $entityManager->persist($attachment);

                            $typeProduitPhoto = new TypeProduitPhoto();
                            $typeProduitPhoto->setIdAttachement($attachment);
                            $typeProduitPhoto->setIdTypep($typeproduit);
                            $entityManager->persist($typeProduitPhoto);
                        } catch (\Exception $e) {
                            // Log the error message
                            $logger->error('File upload error: ' . $e->getMessage());
                            // Optionally, add flash message or return an error response
                            $this->addFlash('error', 'There was an error uploading the file: ' . $e->getMessage());
                        }
                    } else {
                        // Log the error
                        $logger->error('Invalid file in attachments');
                        $this->addFlash('error', 'Invalid file in attachments');
                    }
                }
            } else {
                // Log the error
                $logger->error('No attachments found');
                $this->addFlash('error', 'No attachments found');
                return $this->redirectToRoute('your_route_name');
            }

            // Handle TypeProduitConditionnements
            $conditionnementsJson = $request->request->get('conditionnements');
            $logger->info('Request data', [
                'conditionnements' => $conditionnementsJson,
            ]);
            $conditionnements = json_decode($conditionnementsJson, true);
            if (is_array($conditionnements)) {
                foreach ($conditionnements as $conditionnementData) {
                    $conditionnement = new TypeProduitConditionnement();
                    $conditionnement->setType($conditionnementData['type']);
                    $conditionnement->setDescriptionCommande($conditionnementData['descriptionCommande']);
                    $conditionnement->setMontantHT(floatval($conditionnementData['montantHT']));
                    $conditionnement->setMontantTTC(floatval($conditionnementData['montantTTC']));
                    $conditionnement->setPoidsContenant(floatval($conditionnementData['poidsContenant']));
                    $conditionnement->setPoidsProduit(floatval($conditionnementData['poidsProduit']));
                    $conditionnement->setPochetteEnvoi(floatval($conditionnementData['pochetteEnvoi']));
                    
                    $typeproduit->addTypeProduitConditionnement($conditionnement);
                    $entityManager->persist($conditionnement);
                }
            } else {
                return new Response('Invalid conditionnements data', Response::HTTP_BAD_REQUEST);
            }

            // Save the Product
            $entityManager->persist($typeproduit);
            $entityManager->flush();

            return new Response('Product created successfully!', Response::HTTP_CREATED);
        }
        return $this->render('produits/new.html.twig');
    }


    /**
     * @Route("/produits/{id}", name="produits_show", methods={"GET"})
     */
    public function show(TypeproduitRepository $produitsRepository, int $id): Response
    {
        $produit = $produitsRepository->find($id);
        // dd($produit);
        return $this->render('produits/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/produits/{id}/edit", name="produits_edit", methods={"GET","POST"})
     */
    public function edit(TypeproduitRepository $produitsRepository, int $id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Typeproduit $produits): Response
    {


        return $this->render('produits/edit.html.twig', [
            'produit' => $produits,
        ]);
    }

    /**
     * @Route("/produit/modifier/{id}", name="modifier_produit", methods={"POST"})
     */
    public function modifierProduit(TypeproduitRepository $produitsRepository, int $id, Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {
        $logger->info('Starting product modification.');

        $produit = $produitsRepository->find($id);

        if (!$produit) {
            return new JsonResponse(['success' => false, 'message' => 'Produit non trouvé'], 404);
        }

        $formData = $request->request->all();

        $nomProduit = $request->get('nomProduit');
        $caractProduit = $request->get('caractProduit');
        $plusProduit = $request->get('plusProduit');
        $tarifProduit = $request->get('tarifProduit');
        $reversement = $request->get('reversement');
        $statut = $request->get('statut');

        $logger->info('Updating product information.');

        $produit->setLabeletype($nomProduit);
        $produit->setDescription($caractProduit);
        $produit->setPlusDescription($plusProduit);
        $produit->setTarifs($tarifProduit);
        $produit->setReversement($reversement);
        $produit->setStatut($statut);

        $files = $request->files->get('photos');

        if ($files) {
            $logger->info('Handling file uploads.');
            foreach ($files as $file) {
                $attachment = new Attachment();
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('produit_directory'), $fileName);
                $attachment->setPath($fileName);
                $entityManager->persist($attachment);

                $typeProduitPhoto = new TypeProduitPhoto();
                $typeProduitPhoto->setIdAttachement($attachment);
                $typeProduitPhoto->setIdTypep($produit);
                $entityManager->persist($typeProduitPhoto);
            }
        }

        $logger->info('Flushing changes to the database.');
        $entityManager->flush();

        $logger->info('Redirecting to product index.');
        // return $this->redirectToRoute('produits_index');
        return new JsonResponse(['produit' => $produit, 'logs' => $logger]);
    }


    /**
     * @Route("/produits/{id}", name="produits_delete", methods={"POST"})
     */
    public function delete(Request $request, Typeproduit $produits, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produits->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($produits);
                $entityManager->flush();
                $this->addFlash('success', 'Typeproduit deleted successfully.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while deleting the product.');
            }
        }

        return $this->redirectToRoute('produits_index');
    }

    /**
     * @Route("/suprimerphoto", name="suprimerphoto")
     */
    public function suprimerphoto(Request $request)
    {
        $id = $request->get('id');
        $IdAttachement =  $request->get('IdAttachement');
        $produitService = $this->produitService;
        $result = $produitService->suprimerphoto($id, $IdAttachement);
        return new response($result);
    }



    #[Route('/photo/{id}/delete', name: 'photo_delete', methods: ['DELETE'])]
    public function deletePhoto(Photo $photo, EntityManagerInterface $em): Response
    {
        //return new Response($photo);
        if ($photo) {
            $em->remove($photo);
            $em->flush();
            return new Response(null, 204); // 204 No Content
        }

        return new Response('Photo not found', 404);
    }

    /**
     * @Route("/upload", name="fileuploadhandler")
     */
    public function fileUploadHandler(Request $request, EntityManagerInterface $em)
    {
        $output = array('uploaded' => false);
        $port = $request->getPort();
        $host = $request->getHost();
        $file = $request->files->get('file');
        $originalName = $file->getClientOriginalName();
        $uploadDir = 'sejour';
        $fileName = 'http://' . $host . ':' . $port . '/' . $uploadDir . '/' . $originalName;
        if ($file->move($uploadDir, $fileName)) {
            // create and set this mediaEntity
            // $em = $this->getDoctrine()->getManager();
            // create and set this mediaEntity
            $mediaEntity = new Attachment();
            $mediaEntity->setPath('http://' . $host . ':' . $port . '/' . $uploadDir . '/' . $originalName);
            $mediaEntity->setDate(new \DateTime());
            // save the uploaded filename to database
            $em->persist($mediaEntity);
            $em->flush();
            $output['uploaded'] = true;
            $output['fileName'] = $fileName;
            $output['mediaEntityId'] = $mediaEntity->getId();
            $output['originalFileName'] = $file->getClientOriginalName();
        };
        return new JsonResponse($output);
    }

    /**
     * @Route("/produits/{id}/api", name="produits_show_api", methods={"GET"})
     */
    public function showApi(Typeproduit $produits): JsonResponse
    {
        try {
            return $this->json($produits);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while fetching the product.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/produits/api", name="produits_index_api", methods={"GET"})
     */
    public function indexApi(TypeproduitRepository $produitsRepository): JsonResponse
    {
        try {
            $produits = $produitsRepository->findAll();
            return $this->json($produits);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while fetching the products.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
