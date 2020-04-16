<?php

namespace Frontend\Squads;

use ZfcRbac\Guard\RouteGuard;

return array (
    'zfc_rbac' => [
        'guards' => [
            RouteGuard::class => [
                'frontend/user/squads/xml*' => ['Guest'],
                'frontend/user/squads*' => ['User'],
            ],
        ],
    ],
    'router' => array (
        'routes' => array (
            'frontend' => array (
                'child_routes' => array(
                    'user' => array(
                        'child_routes' => array(
                            'squads' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/squads',
                                    'defaults' => array(
                                        'controller' => 'Frontend\Squads\Controller\Squads',
                                        'action' => 'index',
                                        'page'  => 1,
                                    )
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(

                                    'xml' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/xml/:id/[squad.xml]',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\SquadXml',
                                                'action' => 'squadFile'
                                            )
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array(

                                            'squad' => array(
                                                'type' => 'literal',
                                                'options' => array(
                                                    'route' => 'squad.xml',
                                                    'defaults' => array(
                                                        'controller' => 'Frontend\Squads\Controller\SquadXml',
                                                        'action' => 'squadFile'
                                                    )
                                                )
                                            ),
                                            'logo' => array(
                                                'type' => 'Regex',
                                                'options' => array(
                                                    'regex' => '(?<logo>[a-f0-9]{32}).paa',
                                                    'spec' => '%logo%.paa',
                                                    'defaults' => array(
                                                        'controller' => 'Frontend\Squads\Controller\SquadXml',
                                                        'action' => 'logoFile'
                                                    )
                                                )
                                            ),
                                            'dtd' => array(
                                                'type' => 'literal',
                                                'options' => array(
                                                    'route' => 'squad.dtd',
                                                    'defaults' => array(
                                                        'controller' => 'Frontend\Squads\Controller\SquadXml',
                                                        'action' => 'dtdFile'
                                                    )
                                                )
                                            ),
                                            /*
                                            'xsl' => array(
                                                'type' => 'literal',
                                                'options' => array(
                                                    'route' => 'squad.xsl',
                                                    'defaults' => array(
                                                        'controller' => 'Frontend\Squads\Controller\SquadXml',
                                                        'action' => 'xslFile'
                                                    )
                                                )
                                            ),
                                            'css' => array(
                                                'type' => 'literal',
                                                'options' => array(
                                                    'route' => 'squad.css',
                                                    'defaults' => array(
                                                        'controller' => 'Frontend\Squads\Controller\SquadXml',
                                                        'action' => 'cssFile'
                                                    )
                                                )
                                            ),

                                            'png' => array(
                                                'type' => 'literal',
                                                'options' => array(
                                                    'route' => 'logo.png',
                                                    'defaults' => array(
                                                        'controller' => 'Frontend\Squads\Controller\SquadXml',
                                                        'action' => 'pngFile'
                                                    )
                                                )
                                            ),
                                            'jpg' => array(
                                                'type' => 'literal',
                                                'options' => array(
                                                    'route' => 'squad.jpg',
                                                    'defaults' => array(
                                                        'controller' => 'Frontend\Squads\Controller\SquadXml',
                                                        'action' => 'logoFile'
                                                    )
                                                )
                                            ),
                                            */
                                        )
                                    ),

                                    'create' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/create',
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
                                            'route' => '/delete/:id',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\Squads',
                                                'action' => 'delete'
                                            )
                                        )
                                    ),
                                    'edit' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/edit/:id',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\Squads',
                                                'action' => 'edit'
                                            )
                                        )
                                    ),
                                    'member' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/member/:id',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\SquadMember',
                                                'action' => 'edit'
                                            )
                                        )
                                    ),
                                    'download' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/download/:id',
                                            'defaults' => array(
                                                'controller' => 'Frontend\Squads\Controller\Squads',
                                                'action' => 'download'
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
            'Frontend\Squads\Controller\SquadMember' => 'Frontend\Squads\Controller\SquadMemberController',
            'Frontend\Squads\Controller\SquadXml' => 'Frontend\Squads\Controller\SquadXmlController'
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
            'SquadImageService' => 'Frontend\Squads\Service\SquadImageServiceFactory',
        ),
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
