<?php

namespace App\Controller;

use App\Entity\Annee;
use App\Service\GenerateCode;
use App\Service\Services;
use App\Entity\AnneeHasClasse;
use App\Service\FormError;
use App\Form\AnneeHasClasseType;
use App\Service\ActionRender;
use App\Service\PaginationService;
use App\Repository\AnneeHasClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Omines\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
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
 * il s'agit du anneeHas des module
 */
class AnneeHasClasseController extends AbstractController
{
    /**
     * @Route("/anneeHas/{id}/confirmation", name="anneeHas_confirmation", methods={"GET"})
     * @param $id
     * @param AnneeHasClasse $parent
     * @return Response
     */
    public function confirmation($id,AnneeHasClasse $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'anneeHas',
        ]);
    }

    /**
     * @Route("/anneeHas", name="anneeHas")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param AnneeHasClasseRepository $anneeHasRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          AnneeHasClasseRepository $anneeHasRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $anneeHasRepository->countAll();
        $totalFilteredData = $anneeHasRepository->countAll($searchValue);
        $data = $anneeHasRepository->getAll($limit, $offset,  $searchValue);

//dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;

        $table->add('scolarite', NumberColumn::class, ['label' => 'Scolarite', 'className' => 'w-100px']);
        $table->add('classe_id', TextColumn::class, ['label' => 'Classe', 'className' => 'w-100px']);
        $table->add('annee_id', TextColumn::class, ['label' => 'Annee', 'className' => 'w-100px']);
        $table->add('description', TextColumn::class, ['label' => 'Description', 'className' => 'w-100px']);
        $table->add('observations', TextColumn::class, ['label' => 'Observations', 'className' => 'w-100px']);

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
                                'url' => $this->generateUrl('anneeHas_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('anneeHas_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                            'delete' => [
                                'url' => $this->generateUrl('anneeHas_delete', ['id' => $value])
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

        return $this->render('_admin/anneeHas/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des anneeHass']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/anneeHas/new", name="anneeHas_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, FormError $formError,EntityManagerInterface  $em): Response
    {
        $anneeHas = new AnneeHasClasse();
        $form = $this->createForm(AnneeHasClasseType::class,$anneeHas, [
            'method' => 'POST',
            'action' => $this->generateUrl('anneeHas_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('anneeHas');

            //dd($format);
            if ($form->isValid()) {
                $anneeHas->setCreatedAt(new \DateTime());
                $anneeHas->setCreatedUsername($this->security);
                $anneeHas->setUpdatedAt(new \DateTime());
                $anneeHas->setUpdatedUsername($this->security);
                $em->persist($anneeHas);
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

        return $this->render('_admin/anneeHas/new.html.twig', [
            'anneeHas' => $anneeHas,
            'form' => $form->createView(),
            'titre' => 'AnneeHasClasse',
        ]);
    }

    /**
     * @Route("/anneeHas/{id}/edit", name="anneeHas_edit", methods={"GET","POST"})
     * @param Request $request
     * @param AnneeHasClasse $anneeHas
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request, FormError $formError, AnneeHasClasse $anneeHas, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(AnneeHasClasseType::class,$anneeHas, [
            'method' => 'POST',
            'action' => $this->generateUrl('anneeHas_edit',[
                'id'=>$anneeHas->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('anneeHas');

            if($form->isValid()){


                $anneeHas->setUpdatedAt(new \DateTime());
                $anneeHas->setUpdatedUsername($this->security);
                $em->persist($anneeHas);
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

        return $this->render('_admin/anneeHas/edit.html.twig', [
            'anneeHas' => $anneeHas,
            'form' => $form->createView(),
            'titre' => 'AnneeHasClasse',
        ]);
    }

    /**
     * @Route("/anneeHas/{id}/show", name="anneeHas_show", methods={"GET"})
     * @param AnneeHasClasse $anneeHas
     * @return Response
     */
    public function show(AnneeHasClasse $anneeHas): Response
    {
        $form = $this->createForm(AnneeHasClasseType::class,$anneeHas, [
            'method' => 'POST',
            'action' => $this->generateUrl('anneeHas_show',[
                'id'=>$anneeHas->getId(),
            ])
        ]);

        return $this->render('_admin/anneeHas/voir.html.twig', [
            'anneeHas' => $anneeHas,
            'titre' => 'AnneeHasClasse',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/anneeHas/{id}/active", name="anneeHas_active", methods={"GET"})
     * @param $id
     * @param AnneeHasClasse $anneeHas
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,AnneeHasClasse $anneeHas, EntityManagerInterface $entityManager): Response
    {

        if ($anneeHas->getActive() == 1){

            $anneeHas->setActive(0);

        }else{

            $anneeHas->setActive(1);

        }
        $entityManager->persist($anneeHas);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$anneeHas->getActive(),
        ],200);


    }


    /**
     * @Route("/anneeHas/{id}/delete", name="anneeHas_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param AnneeHasClasse $anneeHas
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,AnneeHasClasse $anneeHas): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'anneeHas_delete'
                    ,   [
                        'id' => $anneeHas->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($anneeHas);
            $em->flush();

            $redirect = $this->generateUrl('anneeHas');

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
        return $this->render('_admin/anneeHas/delete.html.twig', [
            'anneeHas' => $anneeHas,
            'form' => $form->createView(),
        ]);
    }

}
