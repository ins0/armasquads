<?php
namespace Frontend\Login;

return array (


		/**
		 * ROUTER
		 */
		'router' => array (
				'routes' => array (
						'frontend' => array (
								'child_routes' => array(
                                        'register' => array(
                                            'type' => 'literal',
                                            'options' => array(
                                                'route' => 'register',
                                                'defaults' => array(
                                                    'controller' => 'Frontend\Login\Controller\Login',
                                                    'action' => 'register'
                                                )
                                            )
                                        ),
										'login' => array(
												'type' => 'literal',
												'options' => array(
														'route' => 'login',
														'defaults' => array(
																'controller' => 'Frontend\Login\Controller\Login',
																'action' => 'login'
														)
												)
										),
										'logout' => array(
												'type' => 'literal',
												'options' => array(
														'route' => 'logout',
														'defaults' => array(
																'controller' => 'Frontend\Login\Controller\Login',
																'action' => 'logout'
														)
												)
										)
								)
						)

				)
		),

		/**
		 * SERVICE MANAGER
		 */
		'service_manager' => array (
				'factories' => array ()
		),

		/**
		 * CONTROLLERS
		 */
		'controllers' => array (
				'invokables' => array (
						'Frontend\Login\Controller\Login' => 'Frontend\Login\Controller\LoginController'
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
                'frontend/login/form/modal' => __DIR__ . '/../view/login/login_modal.phtml',
                'frontend/login/form' => __DIR__ . '/../view/login/login.phtml',


            ),
            'template_path_stack' => array (
                __DIR__ . '/../view'
            )
        ),

		/**
		 * DOCTRINE
		 */
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
