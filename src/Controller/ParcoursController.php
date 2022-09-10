<?php

namespace App\Controller;

use App\Entity\Annee;
use App\Service\GenerateCode;
use App\Service\Services;
use App\Entity\Parcours;
use App\Service\FormError;
use App\Form\ParcoursType;
use App\Service\ActionRender;
use App\Repository\ParcoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Omines\Adapter\ArrayAdapter;
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
 * il s'agit du parcours des module
 */
class ParcoursController extends AbstractController
{
    /**
     * @Route("/parcours/{id}/confirmation", name="parcours_confirmation", methods={"GET"})
     * @param $id
     * @param parcours $parent
     * @return Response
     */
    public function confirmation($id,parcours $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'parcours',
        ]);
    }

    /**
     * @Route("/parcours", name="parcours")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param ParcoursRepository $parcoursRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          ParcoursRepository $parcoursRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $parcoursRepository->countAll();
        $totalFilteredData = $parcoursRepository->countAll($searchValue);
        $data = $parcoursRepository->getAll($limit, $offset,  $searchValue);

//dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;


        $table->add('code', TextColumn::class, ['label' => 'Code', 'className' => 'w-100px']);
        $table->add('libelle', TextColumn::class, ['label' => 'Libelle', 'className' => 'w-100px']);
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
                                'url' => $this->generateUrl('parcours_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('parcours_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                            'delete' => [
                                'url' => $this->generateUrl('parcours_delete', ['id' => $value])
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

        return $this->render('_admin/parcours/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des parcourss']);
    }

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }
    /**
     * @Route("/parcours/new", name="parcours_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, FormError $formError,GenerateCode $generateCode, EntityManagerInterface  $em): Response
    {
        $parcours = new parcours();
        $form = $this->createForm(parcoursType::class,$parcours, [
            'method' => 'POST',
            'action' => $this->generateUrl('parcours_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('parcours');

            $format = $generateCode->setEntityClass(Parcours::class)
                ->getData("PARC");
            //dd($format);
            if ($form->isValid()) {
                $parcours->setCreatedAt(new \DateTime());
                $parcours->setCreatedUsername($this->security);
                $parcours->setUpdatedAt(new \DateTime());
                $parcours->setUpdatedUsername($this->security);
                $parcours->setCode($format);
                $em->persist($parcours);
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

        return $this->render('_admin/parcours/new.html.twig', [
            'parcours' => $parcours,
            'form' => $form->createView(),
            'titre' => 'Parcours',
        ]);
    }

    /**
     * @Route("/parcours/{id}/edit", name="parcours_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Parcours $parcours
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request, FormError $formError, Parcours $parcours, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(ParcoursType::class,$parcours, [
            'method' => 'POST',
            'action' => $this->generateUrl('parcours_edit',[
                'id'=>$parcours->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('parcours');

            if($form->isValid()){
                $parcours->setUpdatedAt(new \DateTime());
                $parcours->setUpdatedUsername($this->security);
                $em->persist($parcours);
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

        return $this->render('_admin/parcours/edit.html.twig', [
            'parcours' => $parcours,
            'form' => $form->createView(),
            'titre' => 'Parcours',
        ]);
    }

    /**
     * @Route("/parcours/{id}/show", name="parcours_show", methods={"GET"})
     * @param parcours $parcours
     * @return Response
     */
    public function show(parcours $parcours): Response
    {
        $form = $this->createForm(ParcoursType::class,$parcours, [
            'method' => 'POST',
            'action' => $this->generateUrl('parcours_show',[
                'id'=>$parcours->getId(),
            ])
        ]);

        return $this->render('_admin/parcours/voir.html.twig', [
            'parcours' => $parcours,
            'titre' => 'Parcours',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/parcours/{id}/active", name="parcours_active", methods={"GET"})
     * @param $id
     * @param Parcours $parcours
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Parcours $parcours, EntityManagerInterface $entityManager): Response
    {

        if ($parcours->getActive() == 1){

            $parcours->setActive(0);

        }else{

            $parcours->setActive(1);

        }
        $entityManager->persist($parcours);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$parcours->getActive(),
        ],200);


    }


    /**
     * @Route("/parcours/{id}/delete", name="parcours_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Parcours $parcours
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Parcours $parcours): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'parcours_delete'
                    ,   [
                        'id' => $parcours->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($parcours);
            $em->flush();

            $redirect = $this->generateUrl('parcours');

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
        return $this->render('_admin/parcours/delete.html.twig', [
            'parcours' => $parcours,
            'form' => $form->createView(),
        ]);
    }

}
