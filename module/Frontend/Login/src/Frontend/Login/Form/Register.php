<?php
namespace Frontend\Login\Form;

use Zend\Form\Form;

class Register extends Form {

    public function __construct() {

        parent::__construct('register');

        $this->setAttribute ('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setUseInputFilterDefaults(false);

        // Username
        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'username',
                'class' => 'form-control',
                'placeholder' => 'Username'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Username',
            )
        ));

        // Email
        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => 'Email'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Email',
            )
        ));

        // Password
        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Password'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Password',
            )
        ));

        // Password2
        $this->add(array(
            'name' => 'password2',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'id' => 'password2',
                'class' => 'form-control',
                'placeholder' => 'Confirm Password'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Confirm Password',
            )
        ));

        $this->add ( array (
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array (
                'id' => 'submit',
                'value' => 'Get my Account!',
                'class' => 'btn btn-success'
            )
        ) );

    }
}