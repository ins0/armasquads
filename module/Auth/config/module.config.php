<?php

namespace Auth;

use Auth\Service\AuthenticationService;
use Auth\Service\AuthenticationServiceFactory;
use Zend\Authentication\AuthenticationService as ZendAuthenticationService;

return array(

    'service_manager' => array(
        'factories' => array(
            ZendAuthenticationService::class => AuthenticationServiceFactory::class,
        ),
        'aliases' => [
            AuthenticationService::class => ZendAuthenticationService::class,
        ],
    ),

    'translator' => array(
        'translation_file_patterns' => array(
            // FRONTED TRANSLATION
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../language/',
                'pattern' => '%s.php',
                'text_domain' => 'default'
            ),

        )
    ),


    // ###############################################
    // DOCTRINE
    // ###############################################
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
);
