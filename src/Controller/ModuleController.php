<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Repository\ModuleRepository;
use App\Service\PaginationService;
use App\Entity\Module;
use App\Form\ModuleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/admin")
 */
class ModuleController extends AbstractController
{
    /**
     * @Route("/module", name="module")
     * @param ModuleRepository $repository
     * @return Response
     */
    public function index(ModuleRepository $repository): Response
    {

        $pagination = $repository->findBy(['active'=>1]);

        return $this->render('_admin/module/index.html.twig.twig', [
            'pagination' => $pagination,
            'tableau' => ['titre'=> 'titre','parent'=> 'parent','ordre'=> 'ordre'],
            'modal' => '',                  
            'titre' => 'Liste des modules',
            'critereTitre'=>'',

        ]);
    }

    /**
     * @Route("/module/new", name="module_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface  $em): Response
    {
        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module, [
            'method' => 'POST',
            'action' => $this->generateUrl('module_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('module');

            if ($form->isValid()) {

                $module->setActive(1);
                $em->persist($module);
                $em->flush();

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            }
            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('_admin/module/new.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
            'titre' => 'Module',
        ]);
    }

    /**
     * @Route("/module/{id}/edit", name="module_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Module $module, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(ModuleType::class, $module, [
            'method' => 'POST',
            'action' => $this->generateUrl('module_edit', [
                'id' => $module->getId(),
            ])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('module');

            if ($form->isValid()) {
                $em->persist($module);
                $em->flush();

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('_admin/module/edit.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
            'titre' => 'Module',
        ]);
    }

    /**
     * @Route("/module/{id}/show", name="module_show", methods={"GET"})
     */
    public function show(Module $module): Response
    {
        $form = $this->createForm(ModuleType::class, $module, [
            'method' => 'POST',
            'action' => $this->generateUrl('module_show', [
                'id' => $module->getId(),
            ])
        ]);

        return $this->render('_admin/module/voir.html.twig', [
            'module' => $module,
            'titre' => 'Module',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/module/{id}/active", name="module_active", methods={"GET"})
     */
    public function active($id, Module $module, SerializerInterface $serializer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();


        if ($module->getActive() == 1) {

            $module->setActive(0);
        } else {

            $module->setActive(1);
        }
        $json = $serializer->serialize($module, 'json', ['groups' => ['normal']]);
        $entityManager->persist($module);
        $entityManager->flush();
        return $this->json([
            'code' => 200,
            'message' => 'ça marche bien',
            'active' => $module->getActive(),
        ], 200);
    }
    /**
     * @Route("/module/{id}/confirmation", name="module_confirmation", methods={"GET"})
     * @param $id
     * @param Module $parent
     * @return Response
     */
    public function confirmation($id,Module $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'module',
        ]);
    }


    /**
     * @Route("/module/delete/{id}", name="module_delete", methods={"POST","GET","DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Module $module): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'module_delete',
                    [
                        'id' => $module->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($module);
            $em->flush();

            $redirect = $this->generateUrl('module');

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
        return $this->render('_admin/module/delete.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
        ]);
    }
}
