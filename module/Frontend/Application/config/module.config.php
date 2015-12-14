<?php
namespace Frontend\Application;

use Zend\Cache\StorageFactory;

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

    'service_manager' => array(
        'factories' => array(
            'Zend\Cache\Storage\Filesystem' => function($sm){
                    $cache = StorageFactory::factory(array(
                        'adapter' => 'filesystem',
                        'plugins' => array(
                            'exception_handler' => array('throw_exceptions' => false),
                            'serializer'
                        )
                    ));

                    $cache->setOptions(array(
                        'cache_dir' => './data/cache',
                        'ttl' => 3600,
                    ));

                    return $cache;
                },
        ),
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
            'error/403' => __DIR__ . '/../view/layout/frontend/error/403.phtml',
            'error/index' => __DIR__ . '/../view/layout/frontend/error/exception.phtml',
        ),
        // default layout
        'layout/layout'  =>  'layout/frontend',

        // view path
        'template_path_stack' => array (
            __DIR__ . '/../view'
        )
    ),


);
