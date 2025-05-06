<?php

namespace App\Service;
use App\Entity\Ref;
use App\Entity\Blog;
use App\Entity\bolt\BoltPages;
use App\Entity\Attachment;
use App\Entity\BlogAttachement;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class BlogService
{
  private $em;
  private $formFactory;
  private $templating;
  
  public function __construct(ManagerRegistry $em, FormFactoryInterface $formFactory, Environment  $templating)
  {
    $this->em = $em;
    $this->formFactory = $formFactory;
    $this->templating = $templating;
  }
  public function new($title, $iduser, $description, $categorie, $path)
  {
    $em = $this->em->getManager();
    $attachment = new Attachment();
    $attachment->setDate(new \DateTime());
    $attachment->setPath($path);
    $em->persist($attachment);
    $em->flush();
    $stat = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "crée"));
    $blog = new Blog();
    $blog->setTitle($title);
    $blog->setDescription($description);
    $blog->setCategorie($categorie);
    $blog->setStatut($stat);
    $user = $this->em->getRepository(User::class)->find($iduser);
    $blog->setIduser($user);
    $blog->setDate(new \DateTime());
    $em->persist($blog);
    $em->flush();
    $BlogAttachement = new BlogAttachement();
    $BlogAttachement->setIdattachement($attachment);
    $BlogAttachement->setIdblog($blog);
    $em->persist($BlogAttachement);
    $em->flush();
    /* $Att=$this->em->getRepository(Attachment::class)->findBy($idAttachement);
        $BlogAttachement = new BlogAttachement();
        $BlogAttachement->setIdattachement($Att);
        $BlogAttachement->setIdblog($blog);
        $em->persist($BlogAttachement);
        $em->flush();*/
    //dd($blog);
    return ($blog);
  }
  public function allblog()
  {
    $blogs = $this->em->getRepository(Blog::class)->searshDcoisentlist();
    return $blogs;
  }
  public function allblogbolt()
  {
    $blogsbolt = $this->em->getRepository(BoltPages::class)->findAll();
    return $blogsbolt;
  }
  public function myblog($id)
  {
    $blogs = $this->em->getRepository(Blog::class)->find($id);
    return $blogs;
  }
  public function updatBlog($id)
  {
    $em = $this->em->getManager();
    $this->em->getRepository(Blog::class)->UpdatestatutAllBlog();
    $Blog = $this->em->getRepository(Blog::class)->find($id);
    $statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "publiée"));
    $Blog->setStatut($statut);
    $em->persist($Blog);
    $em->flush();
    return $Blog;
  }
  public function archivBlog($id)
  {
    $em = $this->em->getManager();
    $Blog = $this->em->getRepository(Blog::class)->find($id);
    $statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "archivé"));
    $Blog->setStatut($statut);
    $em->persist($Blog);
    $em->flush();
    return $Blog;
  }
  public function findbymotcle($word)
  {
    $Blog = $this->em->getRepository(Blog::class)->searshParmotcle($word);
    return $Blog;
  }
}
