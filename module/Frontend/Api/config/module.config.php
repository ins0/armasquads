<?php
namespace Frontend\Api;

use ZfcRbac\Guard\RouteGuard;

return array (

    'zfc_rbac' => [
        'guards' => [
            RouteGuard::class => [
                'frontend/restapi' => ['Guest'],
                'frontend/user' => ['User'],
                'frontend/api*' => ['Guest'],
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
                    'restapi' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'RESTful/api',
                            'defaults' => array(
                                'controller' => 'Frontend\Api\Controller\Api',
                                'action' => 'indexPublic'
                            )
                        ),
                    ),

                    'user' => array(
                        'child_routes' => array(
                            'api' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/api',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Api\Controller\Api',
                                        'action' => 'index'
                                    )
                                ),
                            )
                        )
                    ),
                    'api' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'api',
                            'defaults' => array(
                                'controller' => 'Frontend\Api\v1\Controller\Squads',
                                'action' => 'selectVersion'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'v1' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/v1',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Api\v1\Controller\Squads',
                                        'action' => 'selectResource'
                                    )
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'squad' => array(
                                        'type' => 'literal',
                                        'options' => array(
                                            'route' => '/squads',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Api\v1\Controller\Squads',
                                                'action' => array(
                                                    'GET'   =>  'fetchAll',
                                                    'POST'  =>  'create'
                                                )
                                            )
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array(
                                            'id' => array(
                                                'type' => 'segment',
                                                'options' => array(
                                                    'route' => '/:id',
                                                    'defaults' => array(
                                                        'controller' => 'Frontend\Api\v1\Controller\Squads',
                                                        'action' => array(
                                                            'DELETE'    =>  'delete',
                                                            'PUT'       =>  'update',
                                                            'GET'       =>  'fetch'
                                                        )
                                                    )
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array(
                                                    'members' => array(
                                                        'type' => 'literal',
                                                        'options' => array(
                                                            'route' => '/members',
                                                            'defaults' => array(
                                                                'controller' => 'Frontend\Api\v1\Controller\Members',
                                                                'action' => array(
                                                                    'GET'   =>  'fetchAll',
                                                                    'POST'  =>  'create'
                                                                )
                                                            )
                                                        ),
                                                        'may_terminate' => true,
                                                        'child_routes' => array(
                                                            'id' => array(
                                                                'type' => 'segment',
                                                                'options' => array(
                                                                    'route' => '/:mid',
                                                                    'defaults' => array(
                                                                        'controller' => 'Frontend\Api\v1\Controller\Members',
                                                                        'action' => array(
                                                                            'DELETE'    =>  'delete',
                                                                            'PUT'       =>  'update',
                                                                            'GET'       =>  'fetch'
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            ),
                            // v2?
                        )
                    )
                )
            )

        )
    ),

    'controllers' => array (
        'invokables' => array (

            'Frontend\Api\Controller\Api' => 'Frontend\Api\Controller\ApiController',

            /** V1 */
            'Frontend\Api\v1\Controller\Squads' => 'Frontend\Api\v1\Controller\SquadsController',
            'Frontend\Api\v1\Controller\Members' => 'Frontend\Api\v1\Controller\MembersController'
        )
    ),

    /**
     * TRANSLATION
     */
    'translator' => array (
        'translation_file_patterns' => array (
            // ADMIN TRANSLATIONS
            array (
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../language/',
                'pattern' => '%s.php',
                'text_domain' => 'default'
            )
        )
    ),

    /**
     * SERVICE MANAGER
     */
    'service_manager' => array(
        'factories' => array(
        ),
    ),

    /**
     * VIEW MANAGER
     */
    'view_manager' => array (
        'template_path_stack' => array (
            __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
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
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity',
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/v1/Entity'
                )
            ),
            'orm_default' => array (
                'drivers' => array (
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                    __NAMESPACE__ . '\v1\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),

);
