<?php
namespace Frontend\Dashboard;

use ZfcRbac\Guard\RouteGuard;

return array (

    'zfc_rbac' => [
        'guards' => [
            RouteGuard::class => [
                'frontend/user/*' => ['User'],
            ],
        ],
    ],

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
                            'route' => 'user',
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
                                    'route' => '/home',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Dashboard\Controller\Dashboard',
                                        'action' => 'index'
                                    )
                                ),
                                'may_terminate' => true
                            ),

                            'donate' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/support',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Dashboard\Controller\Dashboard',
                                        'action' => 'donate'
                                    )
                                ),
                                'may_terminate' => true
                            ),

                            'account' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/account',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Dashboard\Controller\Account',
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
            'Frontend\Dashboard\Controller\Dashboard' => 'Frontend\Dashboard\Controller\DashboardController',
            'Frontend\Dashboard\Controller\Account' => 'Frontend\Dashboard\Controller\AccountController'
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
