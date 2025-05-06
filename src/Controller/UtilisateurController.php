<?php

namespace App\Controller;

use App\Entity\Ref;
use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



/**
 * @Route("/users")
 */
class UtilisateurController extends AbstractController
{

    private $em;
    private $userService;
    private $validator;
    private $serializer;

    public function __construct(ManagerRegistry $em, UserService $userService, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->userService = $userService;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    #[Route('/accompagneteurs', name: 'accompagneteurs', methods: ['GET'])]
    public function accompagneteurs(): Response
    {

        $role = 'ROLE_ACC';
        $users = $this->userService->getUsersByRole($role);
        return $this->render('users/accompagneteurs.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/parents', name: 'parents')]
    public function parents(): Response
    {

        $role = 'ROLE_PARENT';
        $users = $this->userService->getUsersByRole($role);
        return $this->render('users/parents.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/partenaires', name: 'partenaires')]
    public function partenaires(): Response
    {

        $role = 'ROLE_PARTENAIRE';
        $users = $this->userService->getUsersByRole($role);
        
        return $this->render('users/partenaires.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admins', name: 'admins', methods: ['GET'])]
    public function admins(): Response
    {
        $role = 'ROLE_ADMIN';
        $supports = $this->userService->getUsersByRole($role);

        return $this->render('users/admins.html.twig', [
            'supports' => $supports,
        ]);
    }

    #[Route('/supports', name: 'supports', methods: ['GET'])]
    public function supports(): Response
    {
        $role = 'ROLE_SUPPORT';
        $supports = $this->userService->getUsersByRole($role);

        return $this->render('users/supports.html.twig', [
            'supports' => $supports,
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {

        if ($request->isMethod('POST')) {
            
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $etablissement = $request->request->get('phone');
            $roles = $request->request->get('roles');
            if(!is_array($roles)){
                $roles = [$roles];
            } 
            // dd($roles);
            $this->userService->createUser($nom, $prenom, $email, $password, $etablissement, $roles);
            
            if (is_array($roles)) {
                if (in_array('ROLE_ADMIN', $roles)) {
                    return $this->redirectToRoute('admins');
                } else if (in_array('ROLE_SUPPORT', $roles)) {
                    return $this->redirectToRoute('supports');
                } else if (in_array('ROLE_PARTENAIRE', $roles)) {
                    return $this->redirectToRoute('partenaires');
                } else {
                    return $this->redirectToRoute('parents');
                }
            } else {
                if ($roles == 'ROLE_ADMIN') {
                    return $this->redirectToRoute('admins');
                } else if ($roles == 'ROLE_SUPPORT') {
                    return $this->redirectToRoute('supports');
                } else if ($roles == 'ROLE_PARTENAIRE') {
                    return $this->redirectToRoute('partenaires');
                } else {
                    return $this->redirectToRoute('parents');
                }
            }
        }
        return $this->render('users/new.html.twig');
    }


    #[Route('/user/{id}', name: 'user_show', methods: ['GET'])]
    public function show($id): Response
    {
        $editeur = $this->userService->getUser($id);
        if (!$editeur) {
            throw $this->createNotFoundException('User not found');
        }
        return $this->render('users/show.html.twig', [
            'editeur' => $editeur,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit($id, Request $request): Response
    {
        $editeur = $this->userService->getUser($id);

        if (!$editeur) {
            throw new NotFoundHttpException('Editeur not found');
        }
        $form = $this->createForm(UserType::class, $editeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->createUser($editeur);
            if ($request->request->get('roles') == 'ROLE_ADMIN') {
                return $this->redirectToRoute('admins');
            } else if ($request->request->get('roles') == 'ROLE_SUPPORT') {
                return $this->redirectToRoute('supports');
            } else if ($request->request->get('roles') == 'ROLE_PARTENAIRE') {
                return $this->redirectToRoute('partenaires');
            } else {
                return $this->redirectToRoute('parents');
            }
        }


        return $this->render('users/edit.html.twig', [
            'editeur' => $editeur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supports/{id}', name: 'editeur_delete', methods: ['POST'])]
    public function delete($id, Request $request): Response
    {
        $editeur = $this->userService->getUser($id);
        if (!$editeur) {
            throw new NotFoundHttpException('Editeur not found');
        }
        $this->userService->deleteUser($editeur);
        // $this->entityManager->remove($editeur);
        // $this->entityManager->flush();
        if ($request->request->get('roles') == 'ROLE_ADMIN') {
            return $this->redirectToRoute('admins');
        }  else {
            return $this->redirectToRoute('supports');
        }  
    }


    /**
     * @Route("/profile", name="profile", methods={"GET"})
     */
    public function profil(Request $request)
    {
        // dd($this->getUser());
        $iduser = $this->getUser()->getId();
        $profil = $this->userService->getUser($iduser);
        return $this->render('users/profile.html.twig', ['profil' => $profil]);
    }

    /**
     * @Route("/updateprofil", name="updateprofil", methods={"GET"})
     */
    public function profileupadte(Request $request)
    {
        $iduser = $this->getUser()->getId();
        $profil = $this->userService->getUser($iduser);
        return $this->render('users/profileupdate.html.twig', ['profil' => $profil]);
    }

    /**
     * @Route("/modifierprofile", name="modifierprofile")
     */
    public function modifprofil(Request $request)
    {
        $iduser = $this->getUser()->getId();
        $currentUser = $this->getUser(); // Retrieve the current user

        $password = $request->get('password');
        $nom = $request->get('nom');
        $prenom = $request->get('prenom');
        $phone = $request->get('phone');
        $adresse = $request->get('adresse');
        $statutid = $request->get('statut');

        
        $statut = $this->em->getRepository(Ref::class)->find($statutid);
        // dd($request);
        $this->userService->updateUser($iduser, $nom, $prenom, $adresse, $phone, $statut);
        $this->addFlash('success', 'Profile updated successfully!');

        return $this->redirectToRoute('profile');
    }

    #[Route('/activation/{id}', name: 'activations', methods: ['GET', 'POST'])]
    public function envoyerEmailActivation($id): Response
    {
        $user = $this->userService->getUser($id);
        $this->userService->ActivationCompte($id);
        return $this->redirectToRoute('parents');
    }

    #[Route('/confirmactivation/{id}', name: 'confirmactivation', methods: ['GET', 'POST'])]
    public function ActivationCompte($id): Response
    {
        $email = $this->userService->activationmail($id);
        return $this->redirectToRoute('parents');
    }

}
