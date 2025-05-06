<?php

namespace App\Controller;

use App\Entity\Sejour;
use App\Form\SejourType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/sejours")
 */
class SejoursController extends AbstractController
{
    /**
     * @Route("/", name="sejour_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        try {
            $sejours = $entityManager->getRepository(Sejour::class)->findAll();
        } catch (\Exception $e) {
            throw new \Exception('Error fetching data.');
        }

        return $this->render('sejours/index.html.twig', [
            'sejours' => $sejours,
        ]);
    }

    /**
     * @Route("/new", name="sejour_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sejours = new Sejour();
        $form = $this->createForm(SejourType::class, $sejours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($sejours);
                $entityManager->flush();
            } catch (\Exception $e) {
                throw new \Exception('Error creating Sejour.');
            }

            return $this->redirectToRoute('sejour_index');
        }

        return $this->render('sejours/new.html.twig', [
            'sejours' => $sejours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sejour_show", methods={"GET"})
     */
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        try {
            $sejours = $entityManager->getRepository(Sejour::class)->find($id);
            if (!$sejours) {
                throw new NotFoundHttpException('Sejour not found.');
            }
        } catch (\Exception $e) {
            throw new \Exception('Error fetching Sejour.');
        }

        return $this->render('sejours/show.html.twig', [
            'sejours' => $sejours,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sejour_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        try {
            $sejours = $entityManager->getRepository(Sejour::class)->find($id);
            if (!$sejours) {
                throw new NotFoundHttpException('Sejour not found.');
            }
        } catch (\Exception $e) {
            throw new \Exception('Error fetching Sejour.');
        }

        $form = $this->createForm(SejourType::class, $sejours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
            } catch (\Exception $e) {
                throw new \Exception('Error updating Sejour.');
            }

            return $this->redirectToRoute('sejour_index');
        }

        return $this->render('sejours/edit.html.twig', [
            'sejours' => $sejours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sejour_delete", methods={"POST"})
     */
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token.');
        }

        try {
            $sejours = $entityManager->getRepository(Sejour::class)->find($id);
            if (!$sejours) {
                throw new NotFoundHttpException('Sejour not found.');
            }

            $entityManager->remove($sejours);
            $entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('Error deleting Sejour.');
        }

        return $this->redirectToRoute('sejour_index');
    }
}
