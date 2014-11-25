<?php
namespace Auth;

return array(
	
		'service_manager' => array(
				'factories' => array(
                    'AuthService' => 'Auth\Factory\AuthorizeFactory',
				),
                'aliases' => array(
                    'Zend\Authentication\AuthenticationService' => 'AuthService'
                )
		),		
		
		'controller_plugins' => array(
				'factories' => array(
                        'requireLogin' => 'Auth\Factory\requireLoginFactory',
						'setAccess' => 'Auth\Factory\setAccessFactory',
						'hasAccess' => 'Auth\Factory\hasAccessFactory'
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
		
		// ###############################################
		// VIEW HELPERS
		// ###############################################
		'view_helpers'       => array(
				'invokables'=> array(
                    'hasAccess' => 'Auth\View\Helper\hasAccess'
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