<?php

namespace App\Controller;

use App\Entity\Annee;
use App\Entity\Scolarite;
use App\Entity\Versement;
use App\Form\ScolariteType;
use App\Repository\VersementRepository;
use App\Service\FormError;
use App\Service\ActionRender;
use App\Repository\ScolariteRepository;
use App\Service\GenerateCode;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Omines\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\Request;
use Omines\DataTablesBundle\Column\TextColumn;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 * il s'agit du scolarite des module
 */
class ScolariteController extends AbstractController
{

    private $security;
    private $securityRole;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();
        $this->securityRole = $security->getUser()->getRoles();

    }

    /**
     * @Route("/scolarite", name="scolarite")
     * @return Response
     */
    public function index(Request $request): Response
    {
        $etats = [
            'affecte' => "Les affectés",
            'non' => "Les cas individuelles",
        ];
        return $this->render('_admin/scolarite/index.html.twig', ['etats' => $etats, 'titre' => 'Liste des scolarités']);
    }


    /**
     * @Route("/scolarite/{etat}/liste", name="scolarite_liste")
     * @param Request $request
     * @param string $etat
     * @param DataTableFactory $dataTableFactory
     * @param ScolariteRepository $scolariteRepository
     * @return Response
     */
    public function liste(Request $request,
                          string $etat,
                          DataTableFactory $dataTableFactory,
                          ScolariteRepository $scolariteRepository
    ): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;


if ($etat == 'affecte'){
    $totalData = $scolariteRepository->countAll();
    $totalFilteredData = $scolariteRepository->countAll($searchValue);
    $data = $scolariteRepository->getAll($limit, $offset, $searchValue);

}else{
    $totalData = $scolariteRepository->countAllNon();
    $totalFilteredData = $scolariteRepository->countAllNon($searchValue);
    $data = $scolariteRepository->getAllNon($limit, $offset, $searchValue);

}


        // dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ])
            ->setName('dt_');


        if ($etat == 'affecte'){
            $table
                ->add('matricule', TextColumn::class, ['label' => 'Eleve', 'className' => 'w-20px'])
                ->add('scolarite', NumberColumn::class, ['label' => 'Scolarite', 'className' => 'w-100px'])
                ->add('paye', NumberColumn::class, ['label' => 'Somme versée', 'className' => 'w-100px'])
                ->add('reste', NumberColumn::class, ['label' => 'Reste à payer', 'className' => 'w-100px']);

        }else{
            $table
                ->add('matricule', TextColumn::class, ['label' => 'Eleve', 'className' => 'w-20px'])
                ->add('scolarite_personne', NumberColumn::class, ['label' => 'Scolarite', 'className' => 'w-100px'])
                ->add('paye', NumberColumn::class, ['label' => 'Somme versée', 'className' => 'w-100px'])
                ->add('reste_non_affecte', NumberColumn::class, ['label' => 'Reste à payer', 'className' => 'w-100px']);

        }



        if ($this->securityRole[0] === "ROLE_USER"){
            $renders = [

                'edit' =>  new ActionRender(function () {

                    return false;


                }),
                'delete' => new ActionRender(function (){
                    return false;
                }),
                'details' => new ActionRender(function () {
                    return true;
                }),
                'imprimer' =>  new ActionRender(function () {
                    return true;
                }),
            ];
        }else{
            $renders = [

                'edit' =>  new ActionRender(function () {

                    return true;


                }),
                'delete' => new ActionRender(function (){
                    return false;
                }),
                'details' => new ActionRender(function () {
                    return true;
                }),
                'imprimer' =>  new ActionRender(function () {
                    return true;
                }),
            ];
        }


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
                , 'globalSearchable' => false
                , 'className' => 'grid_row_actions'
                , 'render' => function ($value, $context) use ($renders, $etat) {

                    if ($this->securityRole[0] === "ROLE_USER"){
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#extralargemodal1',

                            'actions' => [

                                /*  'edit' => [
                                     'url' => $this->generateUrl('scolarite_edit', ['id' => $value])
                                     , 'ajax' => true
                                     , 'icon' => '%icon% fe fe-edit'
                                     , 'attrs' => ['class' => 'btn-success']
                                     , 'render' => new ActionRender(function () use ($renders) {
                                         return $renders['edit'];
                                     })
                                 ],*/
                                'details' => [
                                    'url' => $this->generateUrl('scolarite_show', ['id' => $value])
                                    , 'ajax' => true
                                    , 'icon' => '%icon% fe fe-eye'
                                    , 'attrs' => ['class' => 'btn-primary']
                                    , 'render' => new ActionRender(function () use ($renders) {
                                        return $renders['details'];
                                    })
                                ],

                                /* 'delete' => [
                                     'url' => $this->generateUrl('scolarite_delete', ['id' => $value])
                                     , 'ajax' => true
                                     , 'icon' => '%icon% fe fe-trash-2'
                                     , 'target' => '#smallmodal'
                                     , 'attrs' => ['class' => 'btn-danger', 'title' => 'Suppression']

                                     ,  'render' => new ActionRender(function () use ($renders) {
                                         return $renders['delete'];
                                     })
                                 ],*/
                                'imprimer' => [
                                    'url' => $this->generateUrl('fiche', ['id' => $value])
                                    , 'ajax' => false
                                    , 'target' => '_blank'
                                    , 'icon' => '%icon% fe fe-download'
                                    , 'attrs' => ['class' => 'btn-info', 'title' => 'Imprimer document','target' => '_blank']
                                    , 'render' =>$renders['imprimer']

                                ],
                            ]
                        ];
                    }else{
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#extralargemodal1',

                            'actions' => [

                                'edit' => [
                                    'url' => $this->generateUrl('scolarite_edit', ['id' => $value])
                                    , 'ajax' => true
                                    , 'icon' => '%icon% fe fe-edit'
                                    , 'attrs' => ['class' => 'btn-success']
                                    , 'render' => new ActionRender(function () use ($renders) {
                                        return $renders['edit'];
                                    })
                                ],
                                'details' => [
                                    'url' => $this->generateUrl('scolarite_show', ['id' => $value])
                                    , 'ajax' => true
                                    , 'icon' => '%icon% fe fe-eye'
                                    , 'attrs' => ['class' => 'btn-primary']
                                    , 'render' => new ActionRender(function () use ($renders) {
                                        return $renders['details'];
                                    })
                                ],
                                /* 'delete' => [
                                     'url' => $this->generateUrl('scolarite_delete', ['id' => $value])
                                     , 'ajax' => true
                                     , 'icon' => '%icon% fe fe-trash-2'
                                     , 'target' => '#smallmodal'
                                     , 'attrs' => ['class' => 'btn-danger', 'title' => 'Suppression']

                                     ,  'render' => new ActionRender(function () use ($renders) {
                                         return $renders['delete'];
                                     })
                                 ],*/

                                'imprimer' => [
                                    'url' => $this->generateUrl('fiche', ['id' => $value])
                                    , 'ajax' => false
                                    , 'icon' => '%icon% fe fe-download'
                                    , 'attrs' => ['class' => 'btn-info', 'title' => 'Imprimer document','target' => '_blank']
                                    , 'render' =>$renders['imprimer']

                                ],
                            ]
                        ];
                    }

                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('_admin/scolarite/liste.html.twig', ['datatable' => $table, 'etat' => $etat]);
    }

    /**
     * @Route("/scolarite2", name="scolarite2")
     * @param ScolariteRepository $repository
     * @return Response
     */
    public function index2(Request $request,
                          DataTableFactory $dataTableFactory,
                          ScolariteRepository $scolariteRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $scolariteRepository->countAll();
        $totalFilteredData = $scolariteRepository->countAll($searchValue);
        $data = $scolariteRepository->getAll($limit, $offset,  $searchValue);

    //dd($this->securityRole);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;
//dd($table->add('matricule', TextColumn::class, ['label' => 'Eleve', 'className' => 'w-20px']));

        $table
            ->add('matricule', TextColumn::class, ['label' => 'Eleve', 'className' => 'w-20px'])
            ->add('scolarite', NumberColumn::class, ['label' => 'Scolarite', 'className' => 'w-100px'])
            ->add('scolarite_personne', NumberColumn::class, ['label' => 'Scolarite ind', 'className' => 'w-100px'])

            ->add('paye', NumberColumn::class, ['label' => 'Somme versée', 'className' => 'w-100px'])
            ->add('reste', NumberColumn::class, ['label' => 'Reste à payer', 'className' => 'w-100px']);


        if ($this->securityRole[0] === "ROLE_USER"){
            $renders = [

                'edit' =>  new ActionRender(function () {

                    return false;


                }),
                'delete' => new ActionRender(function (){
                    return false;
                }),
                'details' => new ActionRender(function () {
                    return true;
                }),
                'imprimer' =>  new ActionRender(function () {
                    return true;
                }),
            ];
        }else{
            $renders = [

                'edit' =>  new ActionRender(function () {

                    return true;


                }),
                'delete' => new ActionRender(function (){
                    return false;
                }),
                'details' => new ActionRender(function () {
                    return true;
                }),
                'imprimer' =>  new ActionRender(function () {
                    return true;
                }),
            ];
        }



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
                    if ($this->securityRole[0] === "ROLE_USER"){
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#extralargemodal1',

                            'actions' => [

                                  'edit' => [
                                     'url' => $this->generateUrl('scolarite_edit', ['id' => $value])
                                     , 'ajax' => true
                                     , 'icon' => '%icon% fe fe-edit'
                                     , 'attrs' => ['class' => 'btn-success']
                                     , 'render' => new ActionRender(function () use ($renders) {
                                         return $renders['edit'];
                                     })
                                 ],
                                 'details' => [
                                     'url' => $this->generateUrl('scolarite_show', ['id' => $value])
                                     , 'ajax' => true
                                     , 'icon' => '%icon% fe fe-eye'
                                     , 'attrs' => ['class' => 'btn-primary']
                                     , 'render' => new ActionRender(function () use ($renders) {
                                         return $renders['details'];
                                     })
                                 ],

                                 /* 'delete' => [
                                      'url' => $this->generateUrl('scolarite_delete', ['id' => $value])
                                      , 'ajax' => true
                                      , 'icon' => '%icon% fe fe-trash-2'
                                      , 'target' => '#smallmodal'
                                      , 'attrs' => ['class' => 'btn-danger', 'title' => 'Suppression']

                                      ,  'render' => new ActionRender(function () use ($renders) {
                                          return $renders['delete'];
                                      })
                                  ],*/
                                'imprimer' => [
                                    'url' => $this->generateUrl('fiche', ['id' => $value])
                                    , 'ajax' => false
                                    , 'target' => '_blank'
                                    , 'icon' => '%icon% fe fe-download'
                                    , 'attrs' => ['class' => 'btn-info', 'title' => 'Imprimer document', 'target' => '_blank']
                                    , 'render' =>$renders['imprimer']

                                ],
                            ]
                        ];
                    }else{
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#extralargemodal1',

                            'actions' => [

                                'edit' => [
                                    'url' => $this->generateUrl('scolarite_edit', ['id' => $value])
                                    , 'ajax' => true
                                    , 'icon' => '%icon% fe fe-edit'
                                    , 'attrs' => ['class' => 'btn-success']
                                    , 'render' => new ActionRender(function () use ($renders) {
                                        return $renders['edit'];
                                    })
                                ],
                                'details' => [
                                    'url' => $this->generateUrl('scolarite_show', ['id' => $value])
                                    , 'ajax' => true
                                    , 'icon' => '%icon% fe fe-eye'
                                    , 'attrs' => ['class' => 'btn-primary']
                                    , 'render' => new ActionRender(function () use ($renders) {
                                        return $renders['details'];
                                    })
                                ],
                                /* 'delete' => [
                                     'url' => $this->generateUrl('scolarite_delete', ['id' => $value])
                                     , 'ajax' => true
                                     , 'icon' => '%icon% fe fe-trash-2'
                                     , 'target' => '#smallmodal'
                                     , 'attrs' => ['class' => 'btn-danger', 'title' => 'Suppression']

                                     ,  'render' => new ActionRender(function () use ($renders) {
                                         return $renders['delete'];
                                     })
                                 ],*/

                                'imprimer' => [
                                    'url' => $this->generateUrl('fiche', ['id' => $value])
                                    , 'ajax' => false
                                    , 'icon' => '%icon% fe fe-download'
                                    , 'attrs' => ['class' => 'btn-info', 'title' => 'Imprimer document']
                                    , 'render' =>$renders['imprimer']

                                ],
                            ]
                        ];
                    }

                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('_admin/scolarite/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des scolarités']);
    }




    /**
     * @Route("/scolarite/new", name="scolarite_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, FormError $formError,GenerateCode $generateCode, EntityManagerInterface  $em): Response
    {
        $scolarite = new Scolarite();

       /* $versement = new Versement();
        $format = $generateCode->setEntityClass(Versement::class)
            ->getData("VERS");
        $versement->setNumeroEtape(0)
            ->setCode($format)
            ->setLibelle("1 Paquet de rame + markers")
            ->setDateVersement(new \DateTime())
            ->setMontant(5000);
        $scolarite->addVersement($versement);*/
        $form = $this->createForm(ScolariteType::class,$scolarite, [
            'method' => 'POST',
            'action' => $this->generateUrl('scolarite_new')
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('scolarite');
            $scolarite_personne = $form->get('scolaritePersonne')->getData();
            if ($form->isValid()) {

                if ($scolarite_personne == null){
                    $scolarite->setScolaritePersonne(0);
                }
                $scolarite->setCreatedAt(new \DateTime());
                $scolarite->setCreatedUsername($this->security);
                $scolarite->setUpdatedAt(new \DateTime());
                $scolarite->setUpdatedUsername($this->security);
                //$scolarite->setActive(1);
                $em->persist($scolarite);
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
        }

        return $this->render('_admin/scolarite/new.html.twig', [
            'scolarite' => $scolarite,
            'form' => $form->createView(),
            'titre' => 'Scolarite ',
        ]);
    }

    /**
     * @Route("/scolarite/{id}/edit", name="scolarite_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Scolarite $scolarite
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request, FormError $formError, Scolarite $scolarite, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(ScolariteType::class,$scolarite, [
            'method' => 'POST',
            'action' => $this->generateUrl('scolarite_edit',[
                'id'=>$scolarite->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('scolarite');

            if($form->isValid()){
                $scolarite->setUpdatedAt(new \DateTime());
                $scolarite->setUpdatedUsername($this->security);
                $em->persist($scolarite);
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

        return $this->render('_admin/scolarite/edit.html.twig', [
            'scolarite' => $scolarite,
            'form' => $form->createView(),
            'titre' => 'Scolarité ',
        ]);
    }

    /**
     * @Route("/scolarite/{id}/show", name="scolarite_show", methods={"GET"})
     * @param Scolarite $scolarite
     * @return Response
     */
    public function show(Scolarite $scolarite): Response
    {
        $form = $this->createForm(ScolariteType::class,$scolarite, [
            'method' => 'POST',
            'action' => $this->generateUrl('scolarite_show',[
                'id'=>$scolarite->getId(),
            ])
        ]);

        return $this->render('_admin/scolarite/voir.html.twig', [
            'scolarite' => $scolarite,
            'titre' => 'Scolarite ',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/scolarite/{id}/active", name="scolarite_active", methods={"GET"})
     * @param $id
     * @param Scolarite $scolarite
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Scolarite $scolarite, EntityManagerInterface $entityManager): Response
    {

        if ($scolarite->getActive() == 1){

            $scolarite->setActive(0);

        }else{

            $scolarite->setActive(1);

        }

        $entityManager->persist($scolarite);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$scolarite->getActive(),
        ],200);


    }


    /**
     * @Route("/scolarite/{id}/delete", name="scolarite_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Scolarite $scolarite
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Scolarite $scolarite): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'scolarite_delete'
                    ,   [
                        'id' => $scolarite->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();

        
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($scolarite);
            $em->flush();

            $redirect = $this->generateUrl('scolarite');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'data' => true,
                'redirect' => $redirect,
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }



        }
        return $this->render('_admin/scolarite/delete.html.twig', [
            'scolarite' => $scolarite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fiche/{id}", name="fiche", methods={"GET","POST"})
     * @param $id
     * @param Request $request
     * @param VersementRepository $versementRepository
     * @throws \Mpdf\MpdfException
     */
    public function imprimer($id, Request $request, VersementRepository $versementRepository,ScolariteRepository $scolariteRepository)
    {

//dd($membreRepository->find($id));

        $html = $this->renderView('_admin/scolarite/imprimer.html.twig', [
            'info'=>$scolariteRepository->getInfoEleve($id),
            'versement' => $versementRepository->getAllVersementBySolarite($id),
        ]);


        //}
        $mpdf = new \Mpdf\Mpdf([

            'mode' => 'utf-8', 'format' => 'A5'
        ]);
        $mpdf->PageNumSubstitutions[] = [
            'from' => 1,
            'reset' => 0,
            'type' => 'I',
            'suppress' => 'on'
        ];

        $mpdf->WriteHTML($html);
        $mpdf->SetFontSize(6);
        $mpdf->Output();


    }


    

}
