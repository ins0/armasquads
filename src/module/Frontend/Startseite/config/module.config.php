<?php
namespace Frontend\Startseite;

return array (

    'controllers' => array (
        'invokables' => array (
            'Frontend\Startseite\Controller\Startseite' => 'Frontend\Startseite\Controller\StartseiteController'
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
     * VIEW MANAGER
     */
    'view_manager' => array (
        'template_map' => array (

            // error layouts
            'frontend/startseite' => __DIR__ . '/../view/startseite/index.phtml',
        ),
        'template_path_stack' => array (
            __DIR__ . '/../view'
        )
    ),

);
