<?php

namespace App\Controller;

use App\Entity\Annee;
use App\Service\GenerateCode;
use App\Service\Services;
use App\Entity\Classe;
use App\Service\FormError;
use App\Form\ClasseType;
use App\Service\ActionRender;
use App\Service\PaginationService;
use App\Repository\ClasseRepository;
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
 * il s'agit du classe des module
 */
class ClasseController extends AbstractController
{
    /**
     * @Route("/classe/{id}/confirmation", name="classe_confirmation", methods={"GET"})
     * @param $id
     * @param Classe $parent
     * @return Response
     */
    public function confirmation($id,Classe $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'classe',
        ]);
    }

    /**
     * @Route("/classe", name="classe")
     * @param TypeRepository $repository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          ClasseRepository $classeRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $classeRepository->countAll();
        $totalFilteredData = $classeRepository->countAll($searchValue);
        $data = $classeRepository->getAll($limit, $offset,  $searchValue);

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
        $table->add('parcours_id', TextColumn::class, ['label' => 'Parcours', 'className' => 'w-100px']);

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
                                'url' => $this->generateUrl('classe_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('classe_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                            'delete' => [
                                'url' => $this->generateUrl('classe_delete', ['id' => $value])
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

        return $this->render('_admin/classe/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des classes']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/classe/new", name="classe_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, FormError $formError,GenerateCode $generateCode, EntityManagerInterface  $em): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class,$classe, [
            'method' => 'POST',
            'action' => $this->generateUrl('classe_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('classe');
            $format = $generateCode->setEntityClass(Classe::class)
                ->getData("CL");
            //dd($format);
            if ($form->isValid()) {
                $classe->setCreatedAt(new \DateTime());
                $classe->setCreatedUsername($this->security);
                $classe->setUpdatedAt(new \DateTime());
                $classe->setUpdatedUsername($this->security);
                $classe->setCode($format);
                $em->persist($classe);
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

        return $this->render('_admin/classe/new.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
            'titre' => 'Classe',
        ]);
    }

    /**
     * @Route("/classe/{id}/edit", name="classe_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Classe $classe
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request, FormError $formError, Classe $classe, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(ClasseType::class,$classe, [
            'method' => 'POST',
            'action' => $this->generateUrl('classe_edit',[
                'id'=>$classe->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('classe');

            if($form->isValid()){
                $classe->setUpdatedAt(new \DateTime());
                $classe->setUpdatedUsername($this->security);
                $em->persist($classe);
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

        return $this->render('_admin/classe/edit.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
            'titre' => 'Classe',
        ]);
    }

    /**
     * @Route("/classe/{id}/show", name="classe_show", methods={"GET"})
     * @param Classe $classe
     * @return Response
     */
    public function show(Classe $classe): Response
    {
        $form = $this->createForm(ClasseType::class,$classe, [
            'method' => 'POST',
            'action' => $this->generateUrl('classe_show',[
                'id'=>$classe->getId(),
            ])
        ]);

        return $this->render('_admin/classe/voir.html.twig', [
            'classe' => $classe,
            'titre' => 'Classe',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/classe/{id}/active", name="classe_active", methods={"GET"})
     * @param $id
     * @param Classe $classe
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Classe $classe, EntityManagerInterface $entityManager): Response
    {

        if ($classe->getActive() == 1){

            $classe->setActive(0);

        }else{

            $classe->setActive(1);

        }
        $entityManager->persist($classe);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$classe->getActive(),
        ],200);


    }


    /**
     * @Route("/classe/{id}/delete", name="classe_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Classe $classe
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Classe $classe): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'classe_delete'
                    ,   [
                        'id' => $classe->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($classe);
            $em->flush();

            $redirect = $this->generateUrl('classe');

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
        return $this->render('_admin/classe/delete.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }

}
