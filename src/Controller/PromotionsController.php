<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Sejour;
use App\Entity\Promotions;
use App\Entity\PromoSejour;
use App\Entity\PromoParents;
use App\Form\PromotionsType;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use App\Service\SejourService;
use App\Repository\PromotionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use SebastianBergmann\Environment\Console;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/promotions")
 */
class PromotionsController extends AbstractController
{

    private $entityManager;
    private $sejourService;
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        SejourService $sejourService,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->sejourService = $sejourService;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="promotions_index", methods={"GET"})
     */

    public function index(PromotionsRepository $promotionsRepository, Request $request): Response
    {


        $promotions = $promotionsRepository->findAll();

        return $this->render('promotions/index.html.twig', [
            'promotions' => $promotions,
        ]);
    }

    /**
     * @Route("/get", name="promotions_get", methods={"GET"})
     */
    public function getPromotions(Request $request, PromotionsRepository $promotionsRepository): JsonResponse
    {
        // Get parameters from the request
        $start = $request->query->getInt('start', 0);
        $length = $request->query->getInt('length', 10);
        $searchValue = $request->query->get('search')['value'] ?? '';
        $orderColumnIndex = $request->query->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->query->get('order')[0]['dir'] ?? 'asc';

        $columns = ['code', 'dateDebut', 'dateFin', 'pourcentage', 'nbreMaxParUser', 'etat'];

        $orderColumn = $columns[$orderColumnIndex] ?? 'code';

        // Build query
        $queryBuilder = $promotionsRepository->createQueryBuilder('p')
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->orderBy('p.' . $orderColumn, $orderDirection);

        if (!empty($searchValue)) {
            $queryBuilder->where('p.code LIKE :search')
                ->orWhere('p.dateDebut LIKE :search')
                ->orWhere('p.dateFin LIKE :search')
                ->orWhere('p.pourcentage LIKE :search')
                ->orWhere('p.nbreMaxParUser LIKE :search')
                ->orWhere('p.etat LIKE :search')
                ->setParameter('search', '%' . $searchValue . '%');
        }

        $promotions = $queryBuilder->getQuery()->getResult();

        // Get total records
        $totalPromotions = $promotionsRepository->count([]);

        // Get total records after filtering
        $filteredTotal = $queryBuilder->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();

        $data = [];
        foreach ($promotions as $promotion) {
            $data[] = [
                'code' => $promotion->getCode(),
                'dateDebut' => $promotion->getDateDebut() ? $promotion->getDateDebut()->format('Y-m-d H:i:s') : '',
                'dateFin' => $promotion->getDateFin() ? $promotion->getDateFin()->format('Y-m-d H:i:s') : '',
                'pourcentage' => $promotion->getPourcentage(),
                'nbreMaxParUser' => $promotion->getNbreMaxParUser(),
                'etat' => $promotion->getEtat() ? 'active' : 'désactivé',
                'id' => $promotion->getId()
            ];
        }

        return new JsonResponse([
            'recordsTotal' => $totalPromotions,
            'recordsFiltered' => $filteredTotal,
            'data' => $data,
        ]);
    }




    /**
     * @Route("/new", name="promotions_new", methods={"GET","POST"})
     */
    public function new(Request $request, LoggerInterface $logger, EntityManagerInterface $entityManager): Response
    {
        // Check if the request method is POST
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            $code = $data['code'];
            $type = $data['type'];
            $nbreMaxGeneral = $data['nbreMaxGeneral'] ?? null;
            $nbreMaxParUser = $data['nbreMaxParUser'];
            $dateDebut = $data['dateDebut'];
            $dateFin = $data['dateFin'];
            $etat = $data['etat'];
            $pourcentage = $data['pourcentage'];
            $nbreApplicable = $data['nbreApplicable'];
            $strategie = "";
            $tabCodeSejour = $data['tabCodeSejour'];
            $tabParents = $data['tabParents'];

            // Log the retrieved information
            $logger->info('Retrieved data:', [
                'code' => $code,
                'type' => $type,
                'nbreMaxGeneral' => $nbreMaxGeneral,
                'nbreMaxParUser' => $nbreMaxParUser,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'etat' => $etat,
                'pourcentage' => $pourcentage,
                'nbreApplicable' => $nbreApplicable,
                'tabCodeSejour' => json_encode($tabCodeSejour), // Convert array to string
                'tabParents' => json_encode($tabParents), // Convert array to string
            ]);
            // Get the EntityManager from the ManagerRegistry
            $entityManager = $this->entityManager;

            $codePromo = $this->sejourService->checkCodePromoForCreation($code);
            if ($codePromo['test'] == false) {
                $promotion = $this->sejourService->creationPromotion($code, $type, $nbreMaxGeneral, $nbreMaxParUser, $dateDebut, $dateFin, $etat, $pourcentage, $nbreApplicable, $strategie);
                    if ($type == "codeSejour") {
                        foreach ($tabCodeSejour as $idsejour) {
                            $sejour = $this->entityManager->getRepository(Sejour::class)->find($idsejour);
                            $promoSejour = new PromoSejour();
                            $promoSejour->setSejour($sejour);
                            $promoSejour->setPromotion($promotion);
                            $promoSejour->setDateCreation(new \DateTime());
                            $entityManager->persist($promoSejour);
                        }
                        $entityManager->flush();
                    }
                    if ($type == "parents") {
                        foreach ($tabParents as $idparent) {
                            $user = $this->entityManager->getRepository(User::class)->find($idparent);
                            $promoParent = new PromoParents();
                            $promoParent->setParent($user);
                            $promoParent->setPromotion($promotion);
                            $promoParent->setDateCreation(new \DateTime());
                            $entityManager->persist($promoParent);
                        }
                        $entityManager->flush();
                    }
                return new JsonResponse(['message' => 'create', 'id' => $promotion->getId()], 200);
            } else {
                return new JsonResponse(['message' => 'code promo deja utilise'], 200);
            }
        } else {
            // If the request method is GET, render the form
            return $this->render('promotions/new.html.twig');
        }
    }

    /**
     * @Route("/getCodeSejours", name="getCodeSejours", methods={"GET"})
     */
    public function getCodeSejours()
    {
        $sejours = $this->entityManager->getRepository(Sejour::class)->findCodeSejourForCodePromo();
        //dd($sejours);
        return new JsonResponse(array('sejours' => $sejours), 200);
    }
    /**
     * @Route("/getListeParents", name="getListeParents", methods={"GET"})
     */
    public function getListeParents()
    {
        $parents = $this->entityManager->getRepository(User::class)->findUtilisatursForCodeSejour();
        return new JsonResponse(array('parents' => $parents), 200);
    }


    /**
     * @Route("/{id}", name="promotions_show", methods={"GET"})
     */
    public function show(Promotions $promotion): Response
    {
        return $this->render('promotions/show.html.twig', [
            'promotion' => $promotion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="promotions_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, $id): Response
    {
        $promotion = $this->entityManager->getRepository(Promotions::class)->find($id);

        // return new JsonResponse(['message' => $id], 200);
        if (!$promotion) {
            return new JsonResponse(['message' => 'Promotion not found'], 404);
        }

        if ($request->isMethod('POST')) {
            try {
                $data = json_decode($request->getContent(), true);

                $code = $data['code'];
                $type = $data['type'];
                $nbreMaxGeneral = $data['nbreMaxGeneral'] ?? null;
                $nbreMaxParUser = $data['nbreMaxParUser'];
                $dateDebut = $data['dateDebut'];
                $dateFin = $data['dateFin'];
                $etat = $data['etat'];
                $pourcentage = $data['pourcentage'];
                $nbreApplicable = $data['nbreApplicable'];
                $strategie = "";
                $tabCodeSejour = $data['tabCodeSejour'];
                $tabParents = $data['tabParents'];

                $this->logger->info('Retrieved data:', [
                    'code' => $code,
                    'type' => $type,
                    'nbreMaxGeneral' => $nbreMaxGeneral,
                    'nbreMaxParUser' => $nbreMaxParUser,
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin,
                    'etat' => $etat,
                    'pourcentage' => $pourcentage,
                    'nbreApplicable' => $nbreApplicable,
                    'tabCodeSejour' => $tabCodeSejour,
                    'tabParents' => $tabParents,
                ]);

                $promotion->setCode($code);
                $promotion->setType($type);
                $promotion->setNbreMaxGeneral($nbreMaxGeneral);
                $promotion->setNbreMaxParUser($nbreMaxParUser);

                $dateDebutObj = \DateTime::createFromFormat('Y-m-d', $dateDebut);
                $dateFinObj = \DateTime::createFromFormat('Y-m-d', $dateFin);

                if ($dateDebutObj === false || $dateFinObj === false) {
                    throw new \Exception('Invalid date format.');
                }

                $promotion->setDateDebut($dateDebutObj);
                $promotion->setDateFin($dateFinObj);
                $promotion->setEtat($etat);
                $promotion->setPourcentage($pourcentage);
                $promotion->setNbreApplicable($nbreApplicable);
                $promotion->setStrategie($strategie);

                $promotion = $this->sejourService->updatePromotion
                ($id, $type, $nbreMaxGeneral, $nbreMaxParUser,
                 $dateDebut, $dateFin, $etat, 
                $pourcentage, $nbreApplicable);
                if ($type == "codeSejour") {
                    // foreach ($promotion->getPromoSejours() as $promoSejour) {
                    //     $this->entityManager->remove($promoSejour);
                    // }
                    // $this->entityManager->flush();

                    foreach ($tabCodeSejour as $idsejour) {
                        $sejour = $this->entityManager->getRepository(Sejour::class)->find($idsejour);
                        $promoSejour = new PromoSejour();
                        $promoSejour->setSejour($sejour);
                        $promoSejour->setPromotion($promotion);
                        $promoSejour->setDateCreation(new \DateTime());
                        $this->entityManager->persist($promoSejour);
                    }
                    $this->entityManager->flush();
                }

                if ($type == "parents") {
                    // foreach ($promotion->getPromoParents() as $promoParent) {
                    //     $this->entityManager->remove($promoParent);
                    // }
                    // $this->entityManager->flush();

                    foreach ($tabParents as $idparent) {
                        $user = $this->entityManager->getRepository(User::class)->find($idparent);
                        $promoParent = new PromoParents();
                        $promoParent->setParent($user);
                        $promoParent->setPromotion($promotion);
                        $promoParent->setDateCreation(new \DateTime());
                        $this->entityManager->persist($promoParent);
                    }
                    $this->entityManager->flush();
                }

                return new JsonResponse(['message' => 'Update successful', 'id' => $promotion->getId()], 200);
            } catch (\Exception $e) {
                $this->logger->error('Update failed: ' . $e->getMessage());

                return new JsonResponse(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
            }
        } else {
            $sejours = $promotion->getPromoSejours();
            $parents = $promotion->getPromoParents();
            return $this->render('promotions/edit.html.twig', [
                'promotion' => $promotion,
                'sejours' => $sejours,
                'parents' => $parents,
            ]);
        }
    }

    /**
     * @Route("/updatePromotion", name="updatePromotion")
     */
    // public function updatePromotion(Request $request)
    // {

    //     $SejourService = $this->sejourService;
    //     $idPromo = $request->get('idPromo');
    //     $type = $request->get('type');
    //     $nbreMaxGeneral = $request->get('nbreMaxGeneral');
    //     $nbreMaxParUser = $request->get('nbreMaxParUser');
    //     $dateDebut = $request->get('dateDebut');
    //     $dateFin = $request->get('dateFin');
    //     $pourcentage = $request->get('pourcentage');
    //     $tabCodeSejour = $request->get('tabCodeSejour');
    //     $tabParents = $request->get('tabParents');
    //     $nbreGroupeApplique = $request->get('nbreGroupeApplique');
    //     $etat = $request->get('etat');
    //     $promotion = $SejourService->updatePromotion($idPromo, $type, $nbreMaxGeneral, $nbreMaxParUser, $dateDebut, $dateFin, $etat, $pourcentage, $nbreGroupeApplique);
    //     if ($type == "codeSejour") {
    //         foreach ($tabCodeSejour as $idsejour) {
    //             $sejour = $this->entityManager->getRepository(Sejour::class)->find($idsejour);
    //             $promoSejour = new PromoSejour();
    //             $promoSejour->setSejour($sejour);
    //             $promoSejour->setPromotion($promotion);
    //             $promoSejour->setDateCreation(new \DateTime());
    //             $this->entityManager->getManager()->persist($promoSejour);
    //         }
    //         $this->entityManager->getManager()->flush();
    //     }
    //     if ($type == "parents") {
    //         foreach ($tabParents as $idparent) {
    //             $user = $this->entityManager->getRepository(User::class)->find($idparent);
    //             $promoParent = new PromoParents();
    //             $promoParent->setParent($user);
    //             $promoParent->setPromotion($promotion);
    //             $promoParent->setDateCreation(new \DateTime());
    //             $this->entityManager->getManager()->persist($promoParent);
    //         }
    //         $this->entityManager->getManager()->flush();
    //     }
    //     return new JsonResponse(array('message' => true, 'id' => $promotion->getId()), 200);
    // }


    /**
     * @Route("/{id}", name="promotions_delete", methods={"POST"})
     */
    public function delete(Request $request, Promotions $promotion, EntityManagerInterface $entityManager): Response
    {
        // if ($this->isCsrfTokenValid('delete' . $promotion->getId(), $request->request->get('_token'))) {
        try {
            $entityManager->remove($promotion);
            $entityManager->flush();
            $this->addFlash('success', 'Promotion deleted successfully');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error while deleting promotion: ' . $e->getMessage());
        }
        // }

        return $this->redirectToRoute('promotions_index');
    }


    /**
     * @Route("/promotions/activate/{id}", name="promotions_activate", methods={"GET"})
     */
    public function activate(EntityManagerInterface $entityManager, Promotions $promotion): RedirectResponse
    {
        $promotion->setEtat(true);
        $entityManager->flush();

        return $this->redirectToRoute('promotions_index');
    }

    /**
     * @Route("/promotions/deactivate/{id}", name="promotions_deactivate", methods={"GET"})
     */
    public function deactivate(EntityManagerInterface $entityManager, Promotions $promotion): RedirectResponse
    {
        $promotion->setEtat(false);
        $entityManager->flush();

        return $this->redirectToRoute('promotions_index');
    }
}
