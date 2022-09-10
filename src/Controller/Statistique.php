<?php


namespace App\Controller;


use App\Repository\ScolariteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin")
 */
class Statistique extends AbstractController
{
    private $repo;

    public function  __construct(ScolariteRepository $scolariteRepository)
    {
        $this->repo = $scolariteRepository;
    }

    /**
     * @Route("/statistique", name="statistique")
     */
    public function index(){


        return $this->render("statistique/index.html.twig",[
            'nombreInscritParJour'=>$this->repo->getEventDateValide()
        ]);
    }

}