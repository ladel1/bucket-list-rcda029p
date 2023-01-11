<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/ajouter", name="ajouter")
     */
    public function add( ): Response
    {
        $wish = new Wish();
        $wish->setTitle("Test 3")
             ->setDescription("blablalblablalb")
             ->setAuthor("Pierre");
        $this->wishRepository->add($wish,true);
        return $this->render('wish/add.html.twig');
    }

    /**
     * @Route("/{id}", name="detail", requirements={"id"="\d+"})
     */
    public function detail(Wish $wish): Response
    {
        return $this->render('wish/detail.html.twig',compact("wish"));
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
