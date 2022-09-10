<?php

namespace App\Controller;

use App\Service\GenerateCode;
use App\Service\Services;
use App\Entity\Annee;
use App\Service\FormError;
use App\Form\AnneeType;
use App\Service\ActionRender;
use App\Repository\AnneeRepository;
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
 * il s'agit du annee des module
 */
class AnneeController extends AbstractController
{
    /**
     * @Route("/annee/{id}/confirmation", name="annee_confirmation", methods={"GET"})
     * @param $id
     * @param Annee $parent
     * @return Response
     */
    public function confirmation($id,Annee $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'annee',
        ]);
    }

    /**
     * @Route("/annee", name="annee")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param AnneeRepository $anneeRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          AnneeRepository $anneeRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $anneeRepository->countAll();
        $totalFilteredData = $anneeRepository->countAll($searchValue);
        $data = $anneeRepository->getAll($limit, $offset,  $searchValue);

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
                                'url' => $this->generateUrl('annee_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('annee_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                            'delete' => [
                                'url' => $this->generateUrl('annee_delete', ['id' => $value])
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

        return $this->render('_admin/annee/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des annees']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/annee/new", name="annee_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request,GenerateCode $generateCode, FormError $formError, EntityManagerInterface  $em): Response
    {


        $annee = new Annee();
        $form = $this->createForm(anneeType::class,$annee, [
            'method' => 'POST',
            'action' => $this->generateUrl('annee_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {

            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('annee');
           $format = $generateCode->setEntityClass(Annee::class)
                ->getData("AN");
           //dd($format);
            if ($form->isValid()) {
                $annee->setCreatedAt(new \DateTime());
                $annee->setUpdatedAt(new \DateTime());
                $annee->setCreatedUsername($this->security);
                $annee->setUpdatedUsername($this->security);
                $annee->setCode($format);
                $em->persist($annee);
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

        return $this->render('_admin/annee/new.html.twig', [
            'annee' => $annee,
            'form' => $form->createView(),
            'titre' => 'Annee',
        ]);
    }

    /**
     * @Route("/annee/{id}/edit", name="annee_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Annee $annee
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request, FormError $formError, Annee $annee, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(AnneeType::class,$annee, [
            'method' => 'POST',
            'action' => $this->generateUrl('annee_edit',[
                'id'=>$annee->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('annee');

            if($form->isValid()){

                $annee->setUpdatedAt(new \DateTime());
                $annee->setUpdatedUsername($this->security);
                $em->persist($annee);
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

        return $this->render('_admin/annee/edit.html.twig', [
            'annee' => $annee,
            'form' => $form->createView(),
            'titre' => 'Annee',
        ]);
    }

    /**
     * @Route("/annee/{id}/show", name="annee_show", methods={"GET"})
     * @param annee $annee
     * @return Response
     */
    public function show(annee $annee): Response
    {
        $form = $this->createForm(AnneeType::class,$annee, [
            'method' => 'POST',
            'action' => $this->generateUrl('annee_show',[
                'id'=>$annee->getId(),
            ])
        ]);

        return $this->render('_admin/annee/voir.html.twig', [
            'annee' => $annee,
            'titre' => 'Annee',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/annee/{id}/active", name="annee_active", methods={"GET"})
     * @param $id
     * @param Annee $annee
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Annee $annee, EntityManagerInterface $entityManager): Response
    {

        if ($annee->getActive() == 1){

            $annee->setActive(0);

        }else{

            $annee->setActive(1);

        }
        $entityManager->persist($annee);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$annee->getActive(),
        ],200);


    }


    /**
     * @Route("/annee/{id}/delete", name="annee_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Annee $annee
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Annee $annee): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'annee_delete'
                    ,   [
                        'id' => $annee->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($annee);
            $em->flush();

            $redirect = $this->generateUrl('annee');

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
        return $this->render('_admin/annee/delete.html.twig', [
            'annee' => $annee,
            'form' => $form->createView(),
        ]);
    }

}
