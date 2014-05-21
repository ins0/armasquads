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
                                    'route' => 'squads/',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Squads\Controller\Squads',
                                        'action' => 'index',
                                        'page'  => 1,
                                    )
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'create' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => 'create/',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\Squads',
                                                'action' => 'create',
                                                'page'  => 1,
                                            )
                                        )
                                    ),
                                    'delete' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => 'delete/:id/',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\Squads',
                                                'action' => 'delete'
                                            )
                                        )
                                    ),
                                    'edit' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => 'edit/:id/',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\Squads',
                                                'action' => 'edit'
                                            )
                                        )
                                    ),
                                    'member' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => 'member/:id/',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\SquadMember',
                                                'action' => 'edit'
                                            )
                                        )
                                    ),
                                )
                            ),
                        )
                    ),
                )
            )

        )
    ),

    'controllers' => array (
        'invokables' => array (
            'Frontend\Squads\Controller\Squads' => 'Frontend\Squads\Controller\SquadsController',
            'Frontend\Squads\Controller\SquadMember' => 'Frontend\Squads\Controller\SquadMemberController'
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
