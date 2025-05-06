<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class RegistrationController extends AbstractController
{
    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->addRole("ROLE_USER");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            return $this->redirectToRoute('NewDashboardAdmin');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/Admin/Ajouteruser", name="Ajouteruser")
     */
    public function Ajouteruser(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $identifiant = $request->get('identifiant');
        $adresse = $request->get("adresse");
        $nom = $request->get("nom");
        $prenom = $request->get("prenom");
        $site = $request->get("site");
        $role = $request->get("role");
        $password = $request->get("password");
        $user = new User();
        $user->setUsername($identifiant);
        $user->setEmail(trim($adresse));
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );
        $user->addRole($role);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $userservc = $this->userService;
        $us =   $userservc->EnvoyerEmailNewUserediteuroradmin($user, $password);
        return $this->render('Admin/Ajouterutilisateur.html.twig', []);
    }
    /**
     * @Route("/Admin/InterfaceAjouteruser", name="InterfaceAjouteruser")
     */
    public function InterfaceUser()
    {
        return $this->render('Admin/Ajouterutilisateur.html.twig', []);
    }
    /**
     * @Route("/Admin/profil", name="editprofil")
     */
    public function editProfil(Request $request)
    {
        $password = $request->get('password');
        $userId = $request->get('userID');
        $nom = $request->get('nom');
        $prenom = $request->get('prenom');
        $phone = $request->get('phone');
        $adresse = $request->get('adresse');
        $serviceuser = $this->userService;
        $result = $serviceuser->updatUser($userId, $nom, $prenom, $adresse, $phone, $password);
        return new response($result);
    }
}
