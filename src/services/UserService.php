<?php

namespace App\Service;


use Swift_Image;
use App\Entity\Ref;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Adress;
use App\Entity\Sejour;
use App\Entity\Produit;
use App\Entity\Emailing;
use App\Entity\Attachment;
use App\Entity\Etablisment;
use App\Entity\ParentSejour;
use App\Entity\Comptebancaire;
use App\Entity\Jourdescripdate;
use App\Entity\SejourAttachment;
use App\Service\EmailsCmdService;
use App\Entity\Documentpartenaire;
use App\Repository\UserRepository;
use App\Repository\EtablismentRepository;
use App\Service\ResendEmailService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private $em;
    private $logger;
    private $router;
    private $second_mailer;
    private $passwordEncoder;
    private $templating;
    private $session;
    private $params;
    private ResendEmailService $resendEmailService;
    private $userRepository;
    private $etablissementRepository;
    // private $passwordEncoder;
    private $passwordHasher;

    public function __construct(ManagerRegistry $em,
    UserPasswordHasherInterface $passwordHasher,
     UserRepository $userRepository, 
     UrlGeneratorInterface $router,
     UserPasswordHasherInterface $passwordEncoder,
     Environment $templating, 
     SessionInterface $session,
      ParameterBagInterface $params ,
      EtablismentRepository $etablissementRepository,
      ResendEmailService $resendEmailService,
        LoggerInterface $logger,
        // UserPasswordEncoderInterface $passwordEncoder
        
       )
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->passwordEncoder = $passwordEncoder;
        // $this->second_mailer = $second_mailer;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->templating = $templating;
        $this->session = $session;
        $this->params = $params;
        $this->resendEmailService = $resendEmailService;
        $this->userRepository = $userRepository;
        $this->etablissementRepository = $etablissementRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function updateUser($id, $nom, $prenom, $adresse, $phone, $statut)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new \Exception('User not found');
        }
    
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setAdresse($adresse);
        $user->setNummobile($phone);
    
        // Check if statut is an array
        if (is_array($statut)) {
            throw new \Exception('Statut should not be an array');
        }
    
        $user->setStatut($statut);
    
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }

    function creationNewUser($nom, $prenom, $etablisment, $fonction, $adressetablisment, $phone, $email, $password, $role)
    {
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setNummobile($phone);
        $user->setFonction($fonction);
        $user->setAdresse($adressetablisment);
        $user->setNometablisment($etablisment);
        $user->setEmail(trim($email));
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $user->setPasswordNonCripted($password);
        $user->setDateCreation(new \DateTime());
        $user->addRole($role);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function creationNewAcommpa($nom, $prenom, $etablisment, $fonction, $adressetablisment, $phoneacc, $mail, $role, $password, $reponseemail)
    {
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setUsername($mail);
        $user->setReponseemail(trim($reponseemail));
        // $user->setUsername($identifiant);
        $user->setFonction($fonction);
        $user->setNummobile($phoneacc);
        $user->setEmail(trim($mail));
        $user->setAdresse($adressetablisment);
        $user->setNometablisment($etablisment);
        //$user->setnometablisment();
        $user->setDateCreation(new \DateTime());
        $user->addRole($role);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $this->em->getManager()->persist($user);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function creationNewEtablisment($par, $nom, $prenom, $etablisment, $fonction, $adressetablisment, $phoneacc, $mail, $role, $password, $prixcnxparent, $prixcnxpartenaire, $reversecnxpart, $reverseventepart)
    {
        $type = "ECOLES/AUTRES";
        $EtabL = new Etablisment();
        $EtabL->setNometab($etablisment);
        $EtabL->setTypeetablisment($type);
        // $user->setUsername($identifiant);
        $EtabL->setFonctioncontact($fonction);
        $EtabL->setNumerotelp($phoneacc);
        $EtabL->setEmail(trim($mail));
        $EtabL->setAdresseetab($adressetablisment);
        $EtabL->setUser($par); //$user->setnometablisment();
        $EtabL->setPrixcnxparent($prixcnxparent);
        $EtabL->setPrixcnxpartenaire($prixcnxpartenaire);
        $EtabL->setReversecnxpart($reversecnxpart);
        $EtabL->setReverseventepart($reverseventepart);
        $this->em->getManager()->persist($EtabL);
        $this->em->getManager()->flush();
        return $EtabL;
    }
    public function updatUSER($id, $nom, $prenom, $adresse, $phone, $password, $statut = null)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $refstatut = $this->em->getRepository(Ref::class)->find($statut);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setAdresse($adresse);
        $user->setNummobile($phone);
        $user->setStatut($refstatut);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    public function updatPassw($id, $password)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $user->setPasswordNonCripted($password);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function GenerateTokken($user)
    {
        $userHash = hash("sha256", $user->getUsername() . $user->getId());
        return $userHash;
    }
    function generateUrlNewPassword($user)
    {
        $directUrl = $this->router->generate('directloginOM_token', array('token' => $user->getUsername(), 'userHash' => $this->GenerateTokken($user)), UrlGeneratorInterface::ABSOLUTE_URL);
        return $directUrl;
    }

    public function createUser($nom, $prenom, $email, $password, $etablissement, $roles)
    {
        // Encode the password before saving the user
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $user->setEtablisment($etablissement);
        // dd($roles);
        $user->setRoles($roles);

        // Save the user to the database
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
    }

    
    function getUser($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        return $user;
    }
    function getUserAccALL()
    {
        return $this->userRepository->findByRole('ROLE_ACC');
    }
    function getEtablissementPartenaireFiltre()
    {
        return $this->etablissementRepository->getEtablissementPartenaireFiltre();
    }
    function getUsersByRole($role)
    {
        return $this->userRepository->findByRole($role);
    }
    function affectationRole($userId, $role)
    {
        $roles = "[$role]";
        $user = $this->em->getRepository(User::class)->find($userId);
        $user->setRoles($roles);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function affectationStatut($userId, $statutref)
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        $statut = $this->em->getRepository(Ref::class)->find($statutref);
        $user->setStatut($statut);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }

    
    function ajouterdatesejourdescription($id, $description, $datedescription)
    {
        $sejour = $this->em->getRepository(Sejour::class)->find($id);
        $Jourdescripdate = new Jourdescripdate;
        $dat = date_create_from_format('m/d/Y', $datedescription);
        $Jourdescripdate->setDatejourphoto($dat);
        $Jourdescripdate->setDescription($description);
        $Jourdescripdate->setIdIdsejour($sejour);
        $this->em->getManager()->persist($Jourdescripdate);
        $this->em->getManager()->flush();
        return $Jourdescripdate;
    }
    function supprimdescription($iddescription)
    {
        $Jourdescripdate = $this->em->getRepository(Jourdescripdate::class)->find($iddescription);
        $this->em->getManager()->remove($Jourdescripdate);
        $this->em->getManager()->flush();
        return $Jourdescripdate;
    }
    function activationmail($idparent)
    {
        $user = $this->em->getRepository(User::class)->find($idparent);
        $user->setActivatemail(1);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function modificationdescription($iddescription, $description)
    {
        $Jourdescripdate = $this->em->getRepository(Jourdescripdate::class)->find($iddescription);
        //dd($iddescription);
        $Jourdescripdate->setDescription($description);
        $this->em->getManager()->persist($Jourdescripdate);
        $this->em->getManager()->flush();
        return $Jourdescripdate;
    }

    

    

    function listeEtablissement()
    {
        $liste = $this->em->getRepository(Etablisment::class)->findByRole('ROLE_PARTENAIRE');
        return $liste;
    }
    function listePartenaire()
    {
        $liste = $this->em->getRepository(User::class)->findByRole('ROLE_PARTENAIRE');
        return $liste;
    }
    function addtoPanier($id)
    {
        $panier = $this->session->get('Panier');
        if ($panier == null) {
            $panier = [];
        }
        $panier[] = $id;
        $this->session->set('Panier', $panier);
        return $panier;
    }
    function GetListePanier($id)
    {
        $panier = $this->session->get('Panier');
        $MonPanier = $this->em->getRepository(Produit::class)->findBy(array('id' => $panier), array('id' => 'DESC'));
        return $MonPanier;
    }
    function condition($id)
    {
        $sejour = $this->em->getRepository(Sejour::class)->find($id);
        $sejour->setCd(1);
        $this->em->getManager()->persist($sejour);
        $this->em->getManager()->flush();
        return $sejour;
    }
    function getidlistePartenaire($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        return $user;
    }
    function creationCompteP($nomP, $prenomP, $identifiantP, $phoneP, $emailP, $roleP, $infoComplementaireP, $CompteBancaire, $userS, $password)
    {
        $user = new User();
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setRoles($roleP);
        $user->setInfocomple($infoComplementaireP);
        $user->setComptebanque($CompteBancaire);
        $user->setUsersecondaire($userS);
        $password = $this->genererPassword(10);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function creationComptePrincipale($nomP, $prenomP, $identifiantP, $phoneP, $emailP, $roleP, $infoComplementaireP, $CompteBancaire, $password)
    {
        // Compte Principale
        $user = new User();
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setRoles($roleP);
        $user->setInfocomple($infoComplementaireP);
        $user->setComptebanque($CompteBancaire);
        $password = $this->genererPassword(10);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function creationComptePrinc($nomP, $prenomP, $identifiantP, $phoneP, $emailP, $roleP, $infoComplementaireP, $password)
    {
        // Compte Principale
        $user = new User();
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setRoles($roleP);
        $user->setInfocomple($infoComplementaireP);
        $password = $this->genererPassword(10);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function creationCompteUSERP($nomP, $prenomP, $identifiantP, $phoneP, $emailP, $roleP, $userS, $infoComplementaireP, $password)
    {
        // Compte Principale
        $user = new User();
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setRoles($roleP);
        $user->setUsersecondaire($userS);
        $user->setInfocomple($infoComplementaireP);
        $password = $this->genererPassword(10);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function USERP($id, $userS)
    {
        // Compte Principale
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setUsersecondaire($userS);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function creationCompteS($nomS, $prenomS, $identifiantS, $phoneS, $emailS, $infoComplementaireS, $roleS, $password)
    {
        $user = new User();
        $user->setNom($nomS);
        $user->setPrenom($prenomS);
        $user->setUsername($identifiantS);
        $user->setNummobile($phoneS);
        $user->setEmail(trim($emailS));
        $user->setInfocomple($infoComplementaireS);
        $user->setRoles($roleS);
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function creationCompteBancaire($codebnaque, $codeguichet, $numcompt, $clerib, $iban, $codebic, $nom, $domicilation)
    {
        // Compte Bancaire
        $comptebancaire = new Comptebancaire();
        $comptebancaire->setCodebnaque($codebnaque);
        $comptebancaire->setCodeguichet($codeguichet);
        $comptebancaire->setNumcompt($numcompt);
        $comptebancaire->setClerib($clerib);
        $comptebancaire->setIban($iban);
        $comptebancaire->setCodebic($codebic);
        $comptebancaire->setNom($nom);
        $comptebancaire->setDomicilation($domicilation);
        $this->em->getManager()->persist($comptebancaire);
        $this->em->getManager()->flush();
        return $comptebancaire;
    }
    function USERPP($id, $comptebancaire)
    {
        // Compte Principale
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setComptebanque($comptebancaire);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    public function ModifcompteBancaire($id, $codebnaque, $codeguichet, $numcompt, $clerib, $iban, $codebic, $nom, $domicilation)
    {
        $comptebancaire = $this->em->getRepository(Comptebancaire::class)->find($id);
        if ($comptebancaire == null) {
            $comptebancaire = new Comptebancaire();
        }
        $comptebancaire->setCodebnaque($codebnaque);
        $comptebancaire->setCodeguichet($codeguichet);
        $comptebancaire->setNumcompt($numcompt);
        $comptebancaire->setClerib($clerib);
        $comptebancaire->setIban($iban);
        $comptebancaire->setCodebic($codebic);
        $comptebancaire->setNom($nom);
        $comptebancaire->setDomicilation($domicilation);
        $this->em->getManager()->persist($comptebancaire);
        $this->em->getManager()->flush();
        return $comptebancaire;
    }

    

   
    function codesecuriter($code, $id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $comptebancaire =  new Comptebancaire;
        $comptebancaire->setNumcompt($code);
        $this->em->getManager()->persist($comptebancaire);
        $this->em->getManager()->flush();
        $user->setComptebanque($comptebancaire);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
    }
    function genererPassword($longueur)
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < $longueur; $i++) {
            $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
        }
        return $chaineAleatoire;
    }
    function modifierDetailsAcco($idAcco, $nom, $prenom, $email, $tel)
    {
        $Acco = $this->em->getRepository(User::class)->find($idAcco);
        $Acco->setPrenom($prenom);
        $Acco->setNom($nom);
        $Acco->setNummobile($tel);
        $Acco->setReponseemail(trim($email));
        $this->em->getManager()->persist($Acco);
        $this->em->getManager()->flush();
    }
    function AjouterDocument_partenaitre($cloudImagescouleur, $nomdocument, $Etablisment_Find)
    {
        $Documentpartenaire = new Documentpartenaire();
        $Documentpartenaire->setIdetablisment($Etablisment_Find);
        $Documentpartenaire->setNomdocument($nomdocument);
        $Documentpartenaire->setPath($cloudImagescouleur[0]["path"]);
        $this->em->getManager()->persist($Documentpartenaire);
        $this->em->getManager()->flush();
    }
    function conditioncnx($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setCnxpartenaire(1);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function conditioncnxparent($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setCnxparent(1);
        $this->em->getManager()->persist($user);
        $this->em->getManager()->flush();
        return $user;
    }
    function findsejParent($id_parent)
    {
        $ParentSejour = $this->em->getRepository(ParentSejour::class)->find($id_parent);
        return $ParentSejour;
    }

    

    function ActivationCompte($id)
    {
        $from = 'onboarding@resend.dev';
        $to = "mohamedyaakoubiweb@gmail.com";
        $subject = 'confirmation du panier';
        $html = "<p>veuillez confirmer votre compte</p><a href='http://localhost:8000/users/confirmactivation/$id'>Confirmer</a>";
        
        try {
            $this->resendEmailService->sendEmail($to, $subject, $html, ['verify' => false]);
            $responseContent = ['status' => 'Email sent successfully'];
            return $responseContent;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email: ' . $e->getMessage());
            $responseContent = ['error' => 'Failed to send email: ' . $e->getMessage()];
            return $responseContent;
        }
    }

    public function deleteUser(User $user): void
    {
               
        // Remove the user from the database
        $this->em->getManager()->remove($user);
        $this->em->getManager()->flush();
    }
    
}
