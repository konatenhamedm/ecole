<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\ModuleParent;
use App\Service\PaginationService;
use App\Service\UploaderHelper;
use App\Entity\Parametre ;
use App\Form\ParametreType;
use App\Repository\ParametreRepository ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/admin")
 */
class ParametreController extends AbstractController
{

    /**
     * @Route("/parametre/{id}/confirmation", name="parametre_confirmation", methods={"GET"})
     * @param $id
     * @param Parametre $parent
     * @return Response
     */
    public function confirmation($id,Parametre $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'parametre',
        ]);
    }

    /**
     * @Route("/parametre", name="parametre")
     * @param ParametreRepository $repository
     * @return Response
     */
    public function index(ParametreRepository $repository): Response
    {
        $pagination = $repository->findBy(['active'=>1]);

        return $this->render('_admin/parametre/index.html.twig.twig', [
            'pagination'=>$pagination,
            'tableau' => ['logo'=> 'logo','titre'=> 'titre','couleur header'=> 'couleur header'],
            'modal' => 'modal',
            'titre' => 'Liste des parametres',
            'critereTitre'=>'',

        ]);
    }

    /**
     * @Route("/parametre/new", name="parametre_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface  $em,UploaderHelper  $uploaderHelper): Response
    {
        $parametre = new Parametre ();
        $form = $this->createForm(ParametreType::class,$parametre, [
            'method' => 'POST',
            'action' => $this->generateUrl('parametre_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $response = [];
            $redirect = $this->generateUrl('parametre');

            if($form->isValid()){

                $brochureFile = $form->get('logo')->getData();
                $uploadedFile = $form['logo']->getData();

                if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $parametre->setLogo($newFilename);
                }
                $parametre->setActive(1);
                $em->persist($parametre);
                $em->flush();

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);

            }
            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('_admin/parametre/new.html.twig', [
            'parametre' => $parametre,
            'form' => $form->createView(),
            'titre' => 'Parametre',
        ]);
    }

    /**
     * @Route("/parametre/{id}/edit", name="parametre_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,Parametre $parametre, EntityManagerInterface  $em,UploaderHelper  $uploaderHelper): Response
    {

        $form = $this->createForm(ParametreType::class,$parametre, [
            'method' => 'POST',
            'action' => $this->generateUrl('parametre_new',[
                'id'=>$parametre->getId(),
            ])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {

            $response = [];
            $redirect = $this->generateUrl('parametre');

            if($form->isValid()){

                $brochureFile = $form->get('logo')->getData();
                $uploadedFile = $form['logo']->getData();

                if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $parametre->setLogo($newFilename);
                }

                $em->persist($parametre);
                $em->flush();

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);

            }

            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('_admin/parametre/edit.html.twig', [
            'parametre' => $parametre,
            'form' => $form->createView(),
            'titre' => 'Parametre',
        ]);
    }

    /**
     * @Route("/parametre/{id}/show", name="parametre_show", methods={"GET"})
     */
    public function show(Parametre $parametre): Response
    {
        $form = $this->createForm(ParametreType::class,$parametre, [
            'method' => 'POST',
            'action' => $this->generateUrl('parametre_show',[
                'id'=>$parametre->getId(),
            ])
        ]);

        return $this->render('_admin/parametre/voir.html.twig', [
            'parametre' => $parametre,
            'form' => $form->createView(),
            'titre' => 'Parametre',
        ]);
    }

    /**
     * @Route("/parametre/{id}/active", name="parametre_active", methods={"GET"})
     */
    public function active($id,Parametre $parametre, SerializerInterface $serializer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();


        if ($parametre->getActive() == 1){

            $parametre->setActive(0);

        }else{

            $parametre->setActive(1);

        }
        $json = $serializer->serialize($parametre, 'json', ['groups' => ['normal']]);
        $entityManager->persist($parametre);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$parametre->getActive(),
        ],200);


    }


    /**
     * @Route("/parametre/delete/{id}", name="parametre_delete", methods={"POST","GET","DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em,Parametre $parametre): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'parametre_delete'
                    ,   [
                        'id' => $parametre->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($parametre);
            $em->flush();

            $redirect = $this->generateUrl('parametre');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }



        }
        return $this->render('_admin/parametre/delete.html.twig', [
            'parametre' => $parametre,
            'form' => $form->createView(),
        ]);
    }

}
