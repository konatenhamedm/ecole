<?php

namespace App\Controller;

use App\Entity\Professeur;
use App\Service\FormError;
use App\Form\ProfesseurType;
use App\Service\ActionRender;
use App\Repository\ProfesseurRepository;
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
 * il s'agit du professeur des module
 */
class ProfesseurController extends AbstractController
{
    /**
     * @Route("/professeur/{id}/confirmation", name="professeur_confirmation", methods={"GET"})
     * @param $id
     * @param Professeur $parent
     * @return Response
     */
    public function confirmation($id,Professeur $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'professeur',
        ]);
    }

    /**
     * @Route("/professeur", name="professeur")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param ProfesseurRepository $professeurRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          ProfesseurRepository $professeurRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $professeurRepository->countAll();
        $totalFilteredData = $professeurRepository->countAll($searchValue);
        $data = $professeurRepository->getAll($limit, $offset,  $searchValue);

//dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;

        $table->add('nom', TextColumn::class, ['label' => 'Nom', 'className' => 'w-100px']);
        $table->add('prenoms', TextColumn::class, ['label' => 'Prenoms', 'className' => 'w-100px']);
        $table->add('salaire_brute', NumberColumn::class, ['label' => 'Salaire brute', 'className' => 'w-100px']);
        $table->add('salaire_reel', NumberColumn::class, ['label' => 'Salaire reel', 'className' => 'w-100px']);


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
                                'url' => $this->generateUrl('professeur_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('professeur_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                            'delete' => [
                                'url' => $this->generateUrl('professeur_delete', ['id' => $value])
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

        return $this->render('_admin/professeur/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des professeurs']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/professeur/new", name="professeur_new", methods={"GET","POST"})
     * @param Request $request
     * @param FormError $formError
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, FormError $formError, EntityManagerInterface  $em): Response
    {
        $professeur = new Professeur();
        $form = $this->createForm(ProfesseurType::class,$professeur, [
            'method' => 'POST',
            'action' => $this->generateUrl('professeur_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('professeur');
            $salaireReel = $form->get('salaireBrute')->getData()-($form->get('salaireBrute')->getData()*8/100);
            //dd($format);
            if ($form->isValid()) {
                $professeur->setCreatedAt(new \DateTime());
                $professeur->setCreatedUsername($this->security);
                $professeur->setUpdatedAt(new \DateTime());
                $professeur->setUpdatedUsername($this->security);
                $professeur->setSalaireReel($salaireReel);

                $em->persist($professeur);
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

        return $this->render('_admin/professeur/new.html.twig', [
            'professeur' => $professeur,
            'form' => $form->createView(),
            'titre' => 'Professeur',
        ]);
    }

    /**
     * @Route("/professeur/{id}/edit", name="professeur_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Professeur $professeur
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request, FormError $formError, Professeur $professeur, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(ProfesseurType::class,$professeur, [
            'method' => 'POST',
            'action' => $this->generateUrl('professeur_edit',[
                'id'=>$professeur->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('professeur');
            $salaireReel = $form->get('salaireBrute')->getData()*8/100;

            if($form->isValid()){
                $professeur->setUpdatedAt(new \DateTime());
                $professeur->setUpdatedUsername($this->security);
                $professeur->setSalaireReel($salaireReel);

                $em->persist($professeur);
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

        return $this->render('_admin/professeur/edit.html.twig', [
            'professeur' => $professeur,
            'form' => $form->createView(),
            'titre' => 'Professeur',
        ]);
    }

    /**
     * @Route("/professeur/{id}/show", name="professeur_show", methods={"GET"})
     * @param Professeur $professeur
     * @return Response
     */
    public function show(Professeur $professeur): Response
    {
        $form = $this->createForm(ProfesseurType::class,$professeur, [
            'method' => 'POST',
            'action' => $this->generateUrl('professeur_show',[
                'id'=>$professeur->getId(),
            ])
        ]);

        return $this->render('_admin/professeur/voir.html.twig', [
            'professeur' => $professeur,
            'titre' => 'Professeur',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/professeur/{id}/active", name="professeur_active", methods={"GET"})
     * @param $id
     * @param Professeur $professeur
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Professeur $professeur, EntityManagerInterface $entityManager): Response
    {

        if ($professeur->getActive() == 1){

            $professeur->setActive(0);

        }else{

            $professeur->setActive(1);

        }
        $entityManager->persist($professeur);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$professeur->getActive(),
        ],200);


    }


    /**
     * @Route("/professeur/{id}/delete", name="professeur_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Professeur $professeur
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Professeur $professeur): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'professeur_delete'
                    ,   [
                        'id' => $professeur->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($professeur);
            $em->flush();

            $redirect = $this->generateUrl('professeur');

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
        return $this->render('_admin/professeur/delete.html.twig', [
            'professeur' => $professeur,
            'form' => $form->createView(),
        ]);
    }

}
