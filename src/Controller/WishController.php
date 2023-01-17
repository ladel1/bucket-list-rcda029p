<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Service\Censurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wish",name="app_wish_")
 */
class WishController extends AbstractController
{

    private $wishRepository;

    public function __construct(WishRepository $wishRepository)
    {
        $this->wishRepository = $wishRepository;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ajouter", name="ajouter")
     */
    public function add(Request $request,Censurator $censurator): Response
    {
        $wish = new Wish();
        if($this->getUser()) $wish->setAuthor($this->getUser()->getUserIdentifier());        
        $wishForm = $this->createForm(WishType::class,$wish);
        $wishForm->handleRequest($request);


        if($wishForm->isSubmitted() && $wishForm->isValid()){
            $wish->setDescription( $censurator->purify2($wish->getDescription()) );
            $this->wishRepository->add($wish,true);
            $this->addFlash("success","Le wish a bien été ajouté");
            return $this->redirectToRoute("app_wish_detail",["id"=>$wish->getId()]);
        }

        return $this->render('wish/add.html.twig',["form"=>$wishForm->createView()]);
    }

    /**
     * @Route("/{id}", name="detail", requirements={"id"="\d+"})
     */
    public function detail(Wish $wish): Response
    {
        return $this->render('wish/detail.html.twig',compact("wish"));
    }

    /**
     * @Route("/modifier/{id}", name="edit", requirements={"id"="\d+"})
     */
    public function edit(Wish $wish,Request $request): Response
    {
  
        $wishForm = $this->createForm(WishType::class,$wish);
        $wishForm->handleRequest($request);

        if($wishForm->isSubmitted() && $wishForm->isValid()){

            $this->wishRepository->update();
            $this->addFlash("success","Le wish a bien été modifié");
            return $this->redirectToRoute("app_wish_detail",["id"=>$wish->getId()]);
        }

        return $this->render('wish/update.html.twig',["form"=>$wishForm->createView()]);
    }

    /**
     * @Route("/supprimer", name="delete")
     */
    public function delete(Request $request): Response
    {
        $id = $request->get("_id");
        if($this->isCsrfTokenValid("supp-".$id,$request->get("_csrf_token"))){
            $this->wishRepository->remove($this->wishRepository->find($id),true);
            $this->addFlash("success","Le wish a bien été supprimé!");
            return $this->redirectToRoute("app_wish_list");
        }
        $this->addFlash("danger","Erreur sur le CSRF Token!");
        return $this->render('wish/detail.html.twig');
    }

    /**
     * @Route("/", name="list")
     */
    public function list( ): Response
    {
        return $this->render('wish/list.html.twig',[
            "wishes"=> $this->wishRepository->findBy(["isPublished"=>true],["dateCreated"=>"DESC"])
        ]);
    }
    
    
}
