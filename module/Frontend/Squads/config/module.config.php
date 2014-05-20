<?php
namespace Frontend\Squads;

return array (
    /**
     * ROUTER
     */
    'router' => array (
        'routes' => array (
            'frontend' => array (
                'child_routes' => array(
                    'user' => array(
                        'child_routes' => array(
                            'squads' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => 'squads/[page/:page/]',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Squads\Controller\Squads',
                                        'action' => 'index',
                                        'page'  => 1,
                                    )
                                ),
                                'may_terminate' => true
                            ),
                        )
                    ),
                )
            )

        )
    ),

    'controllers' => array (
        'invokables' => array (
            'Frontend\Squads\Controller\Squads' => 'Frontend\Squads\Controller\SquadsController'
        )
    ),

    /**
     * VIEW MANAGER
     */
    'view_manager' => array (
        'template_path_stack' => array (
            __DIR__ . '/../view'
        )
    ),

    // ###############################################
    // DOCTRINE
    // ###############################################
    'doctrine' => array (
        'driver' => array (
            __NAMESPACE__ . '_driver' => array (
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array (
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                )
            ),
            'orm_default' => array (
                'drivers' => array (
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),

);
