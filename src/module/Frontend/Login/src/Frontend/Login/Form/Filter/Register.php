<?php
namespace Frontend\Login\Form\Filter;

use DoctrineModule\Validator\NoObjectExists;
use Zend\InputFilter\InputFilter;

class Register extends InputFilter
{
    public function __construct( $em )
    {
        // username
        $this->add(
            array(
                'name'       => 'username',
                'required'   => true,
                'breakChainOnFailure' => true,
                'filters'    => array(
                    array(
                        'name' => 'Zend\Filter\StringTrim'
                    ),
                    array(
                        'name' => 'Zend\Filter\StripTags'
                    )
                ),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Choose a Username'
                            )
                        )
                    ),
                    array(
                        'name'    => 'DoctrineModule\Validator\NoObjectExists',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'object_repository' => $em->getRepository('Auth\Entity\Benutzer'),
                            'fields' => array('username'),
                            'messages' => array(
                                NoObjectExists::ERROR_OBJECT_FOUND => 'Username already exists!'
                            )
                        )
                    )

                )
            )
        );

        // EMAIL
        $this->add(
            array(
                'name'       => 'email',
                'required'   => true,
                'filters'    => array(
                    array(
                        'name' => 'Zend\Filter\StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Enter a valid Email'
                            )
                        )
                    ),
                    array(
                        'name'    => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Please enter a valid Email!'
                            )
                        )
                    ),
                    array(
                        'name'    => 'DoctrineModule\Validator\NoObjectExists',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'object_repository' => $em->getRepository('Auth\Entity\Benutzer'),
                            'fields' => array('email'),
                            'messages' => array(
                                NoObjectExists::ERROR_OBJECT_FOUND => 'Account with this Email already exists!'
                            )
                        )
                    )
                )
            )
        );

        // PASSWORT
        $this->add(
            array(
                'name'       => 'password',
                'required'   => true,
                'filters'    => array(
                    array(
                        'name' => 'Zend\Filter\StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Enter a Password'
                            )
                        )
                    )
                )
            )
        );

        // PASSWORT
        $this->add(
            array(
                'name'       => 'password2',
                'required'   => true,
                'filters'    => array(
                    array(
                        'name' => 'Zend\Filter\StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Please repeat your Password!'
                            )
                        )
                    ),

                    array(
                        'name'    => 'Identical',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'token' => 'password',
                            'messages' => array(
                                \Zend\Validator\Identical::NOT_SAME => 'Both entered passwords are different!'
                            )
                        )
                    ),
                )
            )
        );

    }


}