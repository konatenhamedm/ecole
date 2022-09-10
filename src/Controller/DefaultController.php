<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DefaultController extends AbstractController
{



    /**
     * @Route("admin/agenda",name="agenda")
     * @param CalendarRepository $repository
     * @return Response
     */
    public function calendar(CalendarRepository $repository,NormalizerInterface $normalizer)
    {
        $listes = $repository->getEvenement();
      $ligne = $repository->findAll();
      $rdvs = [];

      foreach ($ligne as $data){
          $rdvs [] = [
              'id'=>$data->getId(),
              'start'=>$data->getStart()->format('Y-m-d H:i:s'),
              'end'=>$data->getEnd()->format('Y-m-d H:i:s'),
              'description'=>$data->getDescription(),
              'title'=>$data->getTitle(),
              'allDay'=>$data->getAllDay(),
              'backgroundColor'=>$data->getBackgroundColor(),
              'borderColor'=>$data->getBorderColor(),
              'textColor'=>$data->getTextColor(),
          ];
      }

      $data =  json_encode($rdvs);
      //dd($data);

        return $this->render("calendar/calendar.html.twig",compact('data','listes'));
    }

    /**
     * @Route("/admin/dashboard", name="dashboard", methods={"GET", "POST"})
     * @return Response
     */
    public function dashboard(CalendarRepository $repository)
    {

        $listes = $repository->getEvenement();
        $lignes = $repository->findAll();
        $rdvs = [];

        foreach ($lignes as $data){
            $rdvs [] = [
                'id'=>$data->getId(),
                'start'=>$data->getStart()->format('Y-m-d H:i:s'),
                'end'=>$data->getEnd()->format('Y-m-d H:i:s'),
                'description'=>$data->getDescription(),
                'title'=>$data->getTitle(),
                'allDay'=>$data->getAllDay(),
                'backgroundColor'=>$data->getBackgroundColor(),
                'borderColor'=>$data->getBorderColor(),
                'textColor'=>$data->getTextColor(),
            ];
        }

        $data =  json_encode($rdvs);
        return $this->render('_admin/dashboard/index.html.twig',compact('data','listes'));
    }
    /**
     * @Route("/admin/{id}/event", name="event_detaiils", methods={"GET", "POST"})
     * @return Response
     */
    public function detailsEvent($id,CalendarRepository $repository)
    {
        return $this->render('_admin/dashboard/info.html.twig',[
            'titre'=>'EVENEMENT',
            'data'=>$repository->findOneBy(['id'=>$id])
        ]);
    }

    /**
     * @Route("/site", name="home_enig", methods={"GET", "POST"})
     * @return Response
     */
    public function homeEnfg()
    {
        return $this->render('fils/index.html.twig');
    }

    /**
     * @Route("/epi", name="epi", methods={"GET", "POST"})
     * @return Response
     */
    public function homeEPI()
    {
        return $this->render('fils/epi.html.twig');
    }

    /**
     * @Route("/etan", name="etan", methods={"GET", "POST"})
     * @return Response
     */
    public function homeEtan()
    {
        return $this->render('fils/etan.html.twig');
    }


}
