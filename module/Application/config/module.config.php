<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Application\Service;

return array (

		'router' => array (
				'routes' => array (

                        // FRONTEND
						'frontend' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/',
										'defaults' => array (
												'controller' => 'Frontend\Startseite\Controller\Startseite',
												'action' => 'index',
										),
										'constraints' => array(
                                            'locale' => '.{2}'
                                        )
								),
                                'may_terminate' => true
						),

                        // ADMINISTRATION
						'admin' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/[:locale/]admin/',
										'defaults' => array (
												'controller' => 'Administration\Dashboard\Controller\Dashboard',
												'action' => 'index',
                                                'locale' => 'en'
										),
										'constraints' => array(
												'locale' => '.{2}'
										)
								),
								'may_terminate' => true
						)
				)
		),


		'service_manager' => array (
			'factories' => array (
				\Racecore\GATracking\GATracking::class => Service\AnalyticsServiceFactory::class
			),
			'shared' => [
				\Racecore\GATracking\GATracking::class => false
			]
		),

		'controller_plugins' => array(
			'invokables' => array(
				'Message' => 'Zend\Mvc\Controller\Plugin\FlashMessenger'
			),
			'factories' => array()
		),
		
		// ###############################################
		// VIEW HELPERS
		// ###############################################
		'view_helpers'       => array(
			'invokables'=> array(
				'FlashMessenger'	=> 'Application\View\Helper\FlashMessenger',
				'formErrors'	=> 'Application\View\Helper\formErrors',
				'ServerUrl'	=> 'Application\View\Helper\ServerUrl',

				//'Translate'			=> 'Application\View\Helper\Translate',
					//'DateFormat'		=> 'Application\View\Helper\DateFormat',
			)
		),		
		
		'view_manager' => array (
				'display_not_found_reason' => false,
				'display_exceptions' => false,
				'doctype' => 'HTML5',
				'template_map' => array (

                    'layout/layout' => __DIR__ . '/../view/layout/default.phtml',


				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		),


);
