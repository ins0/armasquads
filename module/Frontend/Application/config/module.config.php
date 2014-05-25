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

    // ###############################################
    // VIEW HELPERS
    // ###############################################
    'view_helpers'       => array(
        'invokables'=> array(
            'formCollection'	=> 'Frontend\Application\View\Helper\formCollection',
        )
    ),

    'view_manager' => array (

        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array (
            // frontend layout
            'layout/frontend'           =>  __DIR__ . '/../view/layout/frontend/index.phtml',
            'layout/frontend/registered'           =>  __DIR__ . '/../view/layout/frontend/registered.phtml',

            // error layouts
            'error/404' => __DIR__ . '/../view/layout/frontend/error/404.phtml',
            'error/403' => __DIR__ . '/../view/layout/frontend/error/404.phtml',
            'error/index' => __DIR__ . '/../view/layout/frontend/error/404.phtml',
        ),
        // default layout
        'layout/layout'  =>  'layout/frontend',

        // view path
        'template_path_stack' => array (
            __DIR__ . '/../view'
        )
    ),


);
