<?php
namespace Frontend\Dashboard;

return array (

    /**
     * ROUTER
     */
    'router' => array (
        'routes' => array (
            'frontend' => array (
                'child_routes' => array(
                    'user' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'user/',
                            'defaults' => array(
                                'controller' => 'Frontend\Dashboard\Controller\Dashboard',
                                'action' => 'index'
                            )
                        ),
                        'may_terminate' => true,

                        'child_routes' => array(
                            'home' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => 'home/',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Dashboard\Controller\Dashboard',
                                        'action' => 'index'
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

    'translator' => array (
        'translation_file_patterns' => array (
            // FRONTED TRANSLATION
            array (
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../language/',
                'pattern' => '%s.php',
                'text_domain' => 'default'
            ),

        )
    ),

    'controllers' => array (
        'invokables' => array (
            'Frontend\Dashboard\Controller\Dashboard' => 'Frontend\Dashboard\Controller\DashboardController'
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

);
