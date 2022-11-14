<?php

namespace App\Controller;

use App\Service\GenerateCode;
use App\Classe\UploadFile;
use App\Service\Services;
use App\Entity\Eleve;
use App\Service\FormError;
use App\Form\EleveType;
use App\Form\UploadFileType;
use App\Service\ActionRender;
use App\Service\PaginationService;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Omines\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Omines\DataTablesBundle\Column\TextColumn;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

/*SELECT e.`matricule`,e.`nom`,s.scolarite_personne -SUM(v.montant)+ 5000 AS reste_non_affecte,a.scolarite - SUM(v.montant) + 5000  AS reste,a.scolarite,s.scolarite_personne,
SUM(v.montant) - 5000 AS paye,c.libelle
FROM `eleve` AS e
INNER JOIN scolarite AS s ON s.`eleve_id` = e.`id`
INNER JOIN versement AS v ON v.scolarite_id=s.id
INNER JOIN annee_has_classe AS a ON  s.ahc_id =a.id
INNER JOIN classe AS c ON a.classe_id = c.id AND c.libelle = "2"

GROUP BY e.`matricule`*/

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


    /**
     * @Route("/article/addFile", name="article_addFile_new", methods={"GET","POST"})
     * @param Request $request
     * @param EleveRepository $eleveRepository
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \Exception
     */
    public function addFile(
        Request $request, 
        EleveRepository $eleveRepository,
        EntityManagerInterface $em)
    {
        $dossier = new UploadFile();
        $form = $this->createForm(UploadFileType::class,$dossier, [
            'method' => 'POST',
            'action' => $this->generateUrl('article_addFile_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('eleve');

            //

            if ($form->isValid()) {

                $file = $form->get("upload_file")->getData(); // get the file from the sent request


                $fileFolder = $this->getParameter('kernel.project_dir') . '/public/uploads/';  //choose the folder in which the uploaded file will be stored

                $filePathName = md5(uniqid()) . $file->getClientOriginalName();

                try {
                    $file->move($fileFolder, $filePathName);
                } catch (FileException $e) {
                  dd($e)  ;
                }

                $spreadsheet = IOFactory::load($fileFolder . $filePathName); // Here we are able to read from the excel file

                $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array


                    foreach ($sheetData as $Row)
                    {

                        $ref = $Row['A'];     // store the first_name on each iteration
                        $nom = $Row['B'];   // store the last_name on each iteration
                        $prenoms= $Row['C'];  // store the email on each iteration
                        $date = $Row['D'];
                        $statut = $Row['E'];
                        $sexe = $Row['F'];  // store the phone on each iteration

                        $eleve_existe = $eleveRepository->findOneBy(array('matricule' => $ref));


                        if (!$eleve_existe) {
                            $eleve = new Eleve();
                            $date1 = new \DateTime($date);
                            //echo $date->format('Y-m-d H:i:s');
                            $eleve->setCreatedAt(new \DateTime());
                            $eleve->setCreatedUsername($this->security);
                            $eleve->setUpdatedAt(new \DateTime());
                            $eleve->setUpdatedUsername($this->security);
                            $eleve->setNom($nom);
                            $eleve->setMatricule($ref);
                            $eleve->setPrenoms($prenoms);
                            $eleve->setNaissance($date1);
                            $eleve->setStatut($statut);
                            $eleve->setGenre($sexe);
                            $em->persist($eleve);
                            $em->flush();
                        }else{
                            $date2 = new \DateTime($date);
                            $eleve_existe->setNom($nom);
                            $eleve_existe->setMatricule($ref);
                            $eleve_existe->setPrenoms($prenoms);
                            $eleve_existe->setNaissance($date2);
                            $eleve_existe->setGenre($sexe);
                            $eleve_existe->setStatut($statut);
                            $em->persist($eleve_existe);
                               $em->flush();
                        }

                     
                    }

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }

        }
        return $this->renderForm('_admin/eleve/upload_file_new.html.twig', [
            'form' => $form,
            'titre'=>'Upload'
        ]);
    }

}
