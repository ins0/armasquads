<?php
namespace Frontend\Startseite;

return array (

    /**
     * ROUTER
     */
    'router' => array (
        'routes' => array (
            'frontend' => array (
                'child_routes' => array(
                    'impressum' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'impressum',
                            'defaults' => array(
                                'controller' => 'Frontend\Impressum\Controller\Impressum',
                                'action' => 'index'
                            )
                        ),
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
            'Frontend\Impressum\Controller\Impressum' => 'Frontend\Impressum\Controller\ImpressumController'
        )
    ),

    /**
     * VIEW MANAGER
     */
    'view_manager' => array (
        'template_map' => array (
            'frontend/impressum' => __DIR__ . '/../view/impressum/index.phtml',
        ),
        'template_path_stack' => array (
            __DIR__ . '/../view'
        )
    ),

);
