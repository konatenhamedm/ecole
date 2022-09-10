<?php

namespace App\Controller;

use App\Service\GenerateCode;
use App\Service\Services;
use App\Entity\Eleve;
use App\Service\FormError;
use App\Form\EleveType;
use App\Service\ActionRender;
use App\Service\PaginationService;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Omines\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\Request;
use Omines\DataTablesBundle\Column\TextColumn;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 * il s'agit du eleve des module
 */
class EleveController extends AbstractController
{
    /**
     * @Route("/eleve/{id}/confirmation", name="eleve_confirmation", methods={"GET"})
     * @param $id
     * @param Eleve $parent
     * @return Response
     */
    public function confirmation($id,Eleve $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'eleve',
        ]);
    }

    /**
     * @Route("/eleve", name="eleve")
     * @param TypeRepository $repository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          EleveRepository $eleveRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $eleveRepository->countAll();
        $totalFilteredData = $eleveRepository->countAll($searchValue);
        $data = $eleveRepository->getAll($limit, $offset,  $searchValue);

//dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;

        $table->add('matricule', TextColumn::class, ['label' => 'Matricule', 'className' => 'w-100px']);
        $table->add('nom', TextColumn::class, ['label' => 'Nom', 'className' => 'w-100px']);
        $table->add('prenoms', TextColumn::class, ['label' => 'Prenoms', 'className' => 'w-100px']);
        $table->add('naissance', DateTimeColumn::class, ['label' => 'Date de naissance', 'format' => 'd-m-Y']);
        $table->add('genre', TextColumn::class, ['label' => 'Genre', 'className' => 'w-100px']);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            /*    'suivi' =>  new ActionRender(function () use ($etat) {
                    return in_array($etat, ['cree']);
                }),*/
            'delete' => new ActionRender(function (){
                return true;
            }),
            'details' => new ActionRender(function () {
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
                , 'field' => 'id'
                , 'orderable' => false
                ,'globalSearchable' => false
                ,'className' => 'grid_row_actions'
                , 'render' => function ($value, $context) use ($renders) {

                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#extralargemodal1',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('eleve_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('eleve_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                            'delete' => [
                                'url' => $this->generateUrl('eleve_delete', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-trash-2'
                                , 'attrs' => ['class' => 'btn-danger', 'title' => 'Suppression']
                                , 'target' => '#smallmodal'
                                ,  'render' => new ActionRender(function () use ($renders) {
                                    return $renders['delete'];
                                })
                            ],
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

        return $this->render('_admin/eleve/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des eleves']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/eleve/new", name="eleve_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, FormError $formError,EntityManagerInterface  $em): Response
    {
        $eleve = new Eleve();
        $form = $this->createForm(EleveType::class,$eleve, [
            'method' => 'POST',
            'action' => $this->generateUrl('eleve_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('eleve');

            if ($form->isValid()) {
                $eleve->setCreatedAt(new \DateTime());
                $eleve->setCreatedUsername($this->security);
                $eleve->setUpdatedAt(new \DateTime());
                $eleve->setUpdatedUsername($this->security);
                // $eleve->setActive(1);
                $em->persist($eleve);
                $em->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            /*  }*/
            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('_admin/eleve/new.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
            'titre' => 'Eleve',
        ]);
    }

    /**
     * @Route("/eleve/{id}/edit", name="eleve_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Eleve $eleve
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request, FormError $formError, Eleve $eleve, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(EleveType::class,$eleve, [
            'method' => 'POST',
            'action' => $this->generateUrl('eleve_edit',[
                'id'=>$eleve->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('eleve');

            if($form->isValid()){
                $eleve->setUpdatedAt(new \DateTime());
                $eleve->setUpdatedUsername($this->security);
                $em->persist($eleve);
                $em->flush();

                $message       = 'Opération effectuée avec succès';
                $data = true;
                $this->addFlash('success', $message);

            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('_admin/eleve/edit.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
            'titre' => 'Eleve',
        ]);
    }

    /**
     * @Route("/eleve/{id}/show", name="eleve_show", methods={"GET"})
     * @param Eleve $eleve
     * @return Response
     */
    public function show(Eleve $eleve): Response
    {
        $form = $this->createForm(EleveType::class,$eleve, [
            'method' => 'POST',
            'action' => $this->generateUrl('eleve_show',[
                'id'=>$eleve->getId(),
            ])
        ]);

        return $this->render('_admin/eleve/voir.html.twig', [
            'eleve' => $eleve,
            'titre' => 'Eleve',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eleve/{id}/active", name="eleve_active", methods={"GET"})
     * @param $id
     * @param Eleve $eleve
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Eleve $eleve, EntityManagerInterface $entityManager): Response
    {

        if ($eleve->getActive() == 1){

            $eleve->setActive(0);

        }else{

            $eleve->setActive(1);

        }
        $entityManager->persist($eleve);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$eleve->getActive(),
        ],200);


    }


    /**
     * @Route("/eleve/{id}/delete", name="eleve_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Eleve $eleve
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Eleve $eleve): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'eleve_delete'
                    ,   [
                        'id' => $eleve->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($eleve);
            $em->flush();

            $redirect = $this->generateUrl('eleve');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => true
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }



        }
        return $this->render('_admin/eleve/delete.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
        ]);
    }

}
