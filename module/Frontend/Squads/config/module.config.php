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
        'template_path_stack' => array (
            __DIR__ . '/../view'
        )
    ),

);
