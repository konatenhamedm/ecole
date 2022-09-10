<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name ?>;
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
 * @Route("<?= $route_path ?>", options={"expose"=true}))
 */
class <?= $class_name ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{

   
    /**
     * @Route("/", name="<?= $route_name ?>_index", methods={"GET", "POST"}, options={"expose"=true})
     */
<?php if (isset($repository_full_class_name)): ?>
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->createAdapter(ORMAdapter::class, [
            'entity' => <?= $entity_class_name ?>::class,
        ])
        ->setName('dt_<?= $route_name ?>');
        
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
                , 'render' => function ($value, <?= $entity_class_name ?> $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#extralargemodal1',
                            
                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('<?= $route_name ?>_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% fe fe-edit'
                            , 'attrs' => ['class' => 'btn-primary']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#smallmodal'
                            , 'url' => $this->generateUrl('<?= $route_name ?>_delete', ['id' => $value])
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

        return $this->render('<?= $templates_path ?>/index.html.twig', ['datatable' => $table]);
    }
<?php else: ?>
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->createAdapter(ORMAdapter::class, [
            'entity' => <?= $entity_class_name ?>::class,
        ])
        ->add('id', TextColumn::class, [
            'label' => 'Actions'
            , 'orderable' => false
            ,'globalSearchable' => false
            ,'className' => 'grid_row_actions'
            , 'render' => function($value, <?= $entity_class_name ?> $context) {
            $options = [
                'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                //'attrs' => ['class' => 'btn btn-xs btn-clean btn-icon mr-2 '],
                'target' => '#exampleModalSizeLg2',
                
                'actions' => [
                    'edit' => [
                        'url' => $this->generateUrl('<?= $route_name ?>_edit', ['id' => $value])
                        , 'icon' => '%icon% flaticon2-pen'
                        , 'attrs' => ['class' => 'btn-light-primary']
                    ],
                    'delete' => [
                        'target' => '#exampleModalSizeNormal',
                        'url' => $this->generateUrl('<?= $route_name ?>_delete', ['id' => $value])
                        , 'icon' => '%icon% flaticon-delete-1'
                        , 'attrs' => ['class' => 'btn-light-danger']
                    ]
                ] 
                
            ];
            return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
        }])
        ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('<?= $templates_path ?>/index.html.twig', ['datatable' => $table]);
    }
<?php endif ?>

    /**
     * @Route("/new", name="<?= $route_name ?>_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $<?= $entity_var_singular ?> = new <?= $entity_class_name ?>();
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>, [
            'method' => 'POST',
            'action' => $this->generateUrl('<?= $route_name ?>_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $code = 200;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('<?= $route_name ?>_index');

            if ($form->isValid()) {
                
                $em->persist($<?= $entity_var_singular ?>);
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

        return $this->render('<?= $templates_path ?>/new.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}/show", name="<?= $route_name ?>_show", methods={"GET"})
     */
    public function show(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        return $this->render('<?= $templates_path ?>/show.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
        ]);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}/edit", name="<?= $route_name ?>_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>, [
            'method' => 'POST',
            'action' => $this->generateUrl('<?= $route_name ?>_edit', ['<?= $entity_identifier ?>' =>  $<?= $entity_var_singular ?>->get<?= ucfirst($entity_identifier) ?>()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        $data = null;
        $code = 200;
        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('<?= $route_name ?>_index');

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

        return $this->render('<?= $templates_path ?>/edit.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}/delete", name="<?= $route_name ?>_delete", methods={"DELETE", "GET"})
     */
    public function delete(Request $request, EntityManagerInterface $em, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                '<?= $route_name ?>_delete'
                ,   [
                        '<?= $entity_identifier ?>' => $<?= $entity_var_singular ?>->get<?= ucfirst($entity_identifier) ?>()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $em->remove($<?= $entity_var_singular ?>);
            $em->flush();

            $redirect = $this->generateUrl('<?= $route_name ?>_index');

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

        return $this->render('<?= $templates_path ?>/delete.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }
}
