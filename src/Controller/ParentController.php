<?php

namespace App\Controller;

use App\Repository\ModuleParentRepository;
use App\Service\PaginationService;
use App\Service\Services;
use App\Entity\ModuleParent;
use App\Form\ModuleParentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin")
 * il s'agit du parent des module
 */
class ParentController extends AbstractController
{
    /**
     * @Route("/parent/{id}/confirmation", name="parent_confirmation", methods={"GET"})
     * @param $id
     * @param ModuleParent $parent
     * @return Response
     */
    public function confirmation($id,ModuleParent $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'parent',
        ]);
    }

    /**
     * @Route("/parent", name="parent")
     * @param ModuleParentRepository $repository
     * @return Response
     */
    public function index(ModuleParentRepository $repository): Response
    {

        $pagination = $repository->findBy(['active'=>1]);

        return $this->render('_admin/parent/index.html.twig.twig', [
           'pagination'=>$pagination,
            'tableau'=>['titre'=>'titre','ordre'=>'ordre'],
            'modal' => 'modal',

            'titre' => 'Liste des parents',
            'critereTitre'=>'',
        ]);
    }

    /**
     * @Route("/parent/new", name="parent_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface  $em): Response
    {
        $parent = new ModuleParent();
        $form = $this->createForm(ModuleParentType::class,$parent, [
            'method' => 'POST',
            'action' => $this->generateUrl('parent_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $response = [];
            $redirect = $this->generateUrl('parent');

           if($form->isValid()){
               $parent->setActive(1);
               $em->persist($parent);
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

        return $this->render('_admin/parent/new.html.twig', [
            'parent' => $parent,
            'form' => $form->createView(),
            'titre' => 'Parent',
        ]);
    }

    /**
     * @Route("/parent/{id}/edit", name="parent_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,ModuleParent $parent, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(ModuleParentType::class,$parent, [
            'method' => 'POST',
            'action' => $this->generateUrl('parent_edit',[
                'id'=>$parent->getId(),
            ])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {

            $response = [];
            $redirect = $this->generateUrl('parent');

            if($form->isValid()){
                $em->persist($parent);
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

        return $this->render('_admin/parent/edit.html.twig', [
            'parent' => $parent,
            'form' => $form->createView(),
            'titre' => 'Parent',
        ]);
    }

    /**
     * @Route("/parent/{id}/show", name="parent_show", methods={"GET"})
     */
    public function show(ModuleParent $parent): Response
    {
        $form = $this->createForm(ModuleParentType::class,$parent, [
            'method' => 'POST',
            'action' => $this->generateUrl('parent_show',[
                'id'=>$parent->getId(),
            ])
        ]);

        return $this->render('_admin/parent/voir.html.twig', [
            'parent' => $parent,
            'titre' => 'Parent',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/parent/{id}/active", name="parent_active", methods={"GET"})
     */
    public function active($id,ModuleParent $parent, SerializerInterface $serializer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();


        if ($parent->getActive() == 1){

            $parent->setActive(0);

        }else{

            $parent->setActive(1);

        }
        $json = $serializer->serialize($parent, 'json', ['groups' => ['normal']]);
        $entityManager->persist($parent);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$parent->getActive(),
        ],200);


    }


    /**
     * @Route("/parent/delete/{id}", name="parent_delete", methods={"POST","GET","DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em,ModuleParent $parent): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'parent_delete'
                    ,   [
                        'id' => $parent->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($parent);
            $em->flush();

            $redirect = $this->generateUrl('parent');

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
        return $this->render('_admin/parent/delete.html.twig', [
            'parent' => $parent,
            'form' => $form->createView(),
        ]);
    }

}
