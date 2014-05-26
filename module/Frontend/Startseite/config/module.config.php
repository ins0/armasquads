<?php
namespace Frontend\Startseite;

return array (

    'controllers' => array (
        'invokables' => array (
            'Frontend\Startseite\Controller\Startseite' => 'Frontend\Startseite\Controller\StartseiteController'
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
