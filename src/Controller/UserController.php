<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Adapter\ArrayAdapter;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin")
 * il s'agit du parent des User
 */
class UserController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {

        $this->encoder = $encoder;
    }

    /**
     * @Route("/user/{id}/confirmation", name="user_confirmation", methods={"GET"})
     * @param $id
     * @param User $parent
     * @return Response
     */
    public function confirmation($id,User $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'user',
        ]);
    }


    /**
     * @Route("/user", name="user")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          UserRepository $userRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;

        $totalData = $userRepository->countAll();
        $totalFilteredData = $userRepository->countAll($searchValue);
        $data = $userRepository->getAll($limit, $offset,  $searchValue);

        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;


        $table
            ->add('nom', TextColumn::class, ['label' => 'Nom', 'className' => 'w-30px'])
            ->add('prenoms', TextColumn::class, ['label' => 'Prénoms', 'className' => 'w-100px'])
            ->add('email', TextColumn::class, ['label' => 'Email', 'className' => 'w-100px']);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
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
                                'url' => $this->generateUrl('user_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('user_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                            'delete' => [
                                'url' => $this->generateUrl('user_delete', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-trash-2'
                                , 'attrs' => ['class' => 'btn-danger', 'title' => 'Suppression']

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

        return $this->render('_admin/user/index.html.twig.twig', ['datatable' => $table, 'titre' => 'Liste des utilisateur']);
    }


    /**
     * @Route("/user/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $em,FormError $formError): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user, [
            'method' => 'POST',
            'action' => $this->generateUrl('user_new')
        ]);
        $form->handleRequest($request);
       // $statut =0;
        $data = null;
        $isAjax = $request->isXmlHttpRequest();
        if($form->isSubmitted())
        {
            $response = [];
            $redirect = $this->generateUrl('user');
            $statut = 1;
           // dd($form->getData());
            if($form->isValid()){

                $password = $form->getData();
//dd($password);
                $user->setPassword($this->encoder->hashPassword($user,$password));
                $user->setActive(1);
                $em->persist($user);

                $em->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';

                $this->addFlash('success', $message);

            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
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

        return $this->render('_admin/user/new.html.twig', [
            'user' => $user,
            'titre' => 'Utulisateur',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,User $user, EntityManagerInterface  $em,FormError $formError): Response
    {

        $form = $this->createForm(UserType::class,$user, [
            'method' => 'POST',
            'action' => $this->generateUrl('user_edit',[
                'id'=>$user->getId(),
            ])
        ]);
        $form->handleRequest($request);
         $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $response = [];
            $redirect = $this->generateUrl('user');

            if($form->isValid()){

                $password = $form->getData()->getPassword();

                $user->setPassword($this->enoder->hashPassword($user,$password));

                $em->persist($user);
                $em->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';

                $this->addFlash('success', $message);

            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
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

        return $this->render('_admin/user/edit.html.twig', [
            'user' => $user,
            'titre' => 'Utulisateur',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/show", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $form = $this->createForm(UserType::class,$user, [
            'method' => 'POST',
            'action' => $this->generateUrl('user_show',[
                'id'=>$user->getId(),
            ])
        ]);

        return $this->render('_admin/user/voir.html.twig', [
            'user' => $user,
            'titre' => 'Utulisateur',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/active", name="user_active", methods={"GET"})
     */
    public function active($id,User $user, SerializerInterface $serializer,EntityManagerInterface $entityManager): Response
    {

        if ($user->getActive() == 1){

            $user->setActive(0);

        }else{

            $user->setActive(1);

        }
        $json = $serializer->serialize($user, 'json', ['groups' => ['normal']]);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$user->getActive(),
        ],200);


    }


    /**
     * @Route("/user/delete/{id}", name="user_delete", methods={"POST","GET","DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em,User $user): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'user_delete'
                    ,   [
                        'id' => $user->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($user);
            $em->flush();

            $redirect = $this->generateUrl('user');

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
        return $this->render('_admin/user/delete.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
