<?php
namespace Frontend\Application;

return array (

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

    'view_manager' => array (

        'template_map' => array (
            // frontend layout
            'layout/frontend'           =>  __DIR__ . '/../view/layout/frontend/index.phtml',
            'layout/frontend/registered'           =>  __DIR__ . '/../view/layout/frontend/registered.phtml',



        ),
        // default layout
        'layout'  =>  'layout/frontend',
        // view path
        'template_path_stack' => array (
            __DIR__ . '/../view'
        )
    ),


);
