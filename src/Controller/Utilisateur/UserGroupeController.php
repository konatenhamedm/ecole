<?php

namespace App\Controller\Utilisateur;

use App\Entity\UserGroupe;
use App\Form\UserGroupeType;
use App\Repository\UserGroupeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FormError;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\ActionRender;
use App\Annotation\Module;

/**
 * @Route("/admin/utilisateur/groupe", options={"expose"=true}))
 */
class UserGroupeController extends AbstractController
{

   
    /**
     * @Route("/", name="app_utilisateur_user_groupe_index", methods={"GET", "POST"}, options={"expose"=true})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('name', TextColumn::class, ['label' => 'Libellé'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => UserGroupe::class,
        ])
        ->setName('dt_app_utilisateur_user_groupe');
        
        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];

        
        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions'
                , 'orderable' => false
                ,'globalSearchable' => false
                ,'className' => 'grid_row_actions'
                , 'render' => function ($value, UserGroupe $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#largemodal',
                            
                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_utilisateur_user_groupe_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% fe fe-edit'
                            , 'attrs' => ['class' => 'btn-primary']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#smallmodal'
                            , 'url' => $this->generateUrl('app_utilisateur_user_groupe_delete', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% fe fe-trash'
                            , 'attrs' => ['class' => 'btn-danger']
                            ,  'render' => $renders['delete']
                        ]
                    ] 
                            
                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }
       

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('utilisateur/user_groupe/index.html.twig.twig', ['datatable' => $table]);
    }

    /**
     * @Route("/new", name="app_utilisateur_user_groupe_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $userGroupe = new UserGroupe();
        $form = $this->createForm(UserGroupeType::class, $userGroupe, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_utilisateur_user_groupe_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $code = 200;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_user_groupe_index');

            if ($form->isValid()) {
                
                $em->persist($userGroupe);
                $em->flush();
                $data = true;

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);

                
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $code = 500;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }
                
            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $code);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('utilisateur/user_groupe/new.html.twig', [
            'user_groupe' => $userGroupe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="app_utilisateur_user_groupe_show", methods={"GET"})
     */
    public function show(UserGroupe $userGroupe): Response
    {
        return $this->render('utilisateur/user_groupe/show.html.twig', [
            'user_groupe' => $userGroupe,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_utilisateur_user_groupe_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserGroupe $userGroupe, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserGroupeType::class, $userGroupe, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_utilisateur_user_groupe_edit', ['id' =>  $userGroupe->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        $data = null;
        $code = 200;
        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_user_groupe_index');

            if ($form->isValid()) {
                $em->flush();
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);

                
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $code = 500;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }
                
            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $code);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('utilisateur/user_groupe/edit.html.twig', [
            'user_groupe' => $userGroupe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_utilisateur_user_groupe_delete", methods={"DELETE", "GET"})
     */
    public function delete(Request $request, EntityManagerInterface $em, UserGroupe $userGroupe): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_utilisateur_user_groupe_delete'
                ,   [
                        'id' => $userGroupe->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $em->remove($userGroupe);
            $em->flush();

            $redirect = $this->generateUrl('app_utilisateur_user_groupe_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }


           
        }

        return $this->render('utilisateur/user_groupe/delete.html.twig', [
            'user_groupe' => $userGroupe,
            'form' => $form->createView(),
        ]);
    }
}
