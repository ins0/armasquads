<?php
namespace Frontend\Login\Form;

use Zend\Form\Form;

class Login extends Form {
	
	public function __construct() {

        parent::__construct('login');

		$this->setAttribute ('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        // Email
        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => 'E-Mail'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-12'
                ),
                'label' => 'E-Mail',
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
                    'class' => 'col-lg-12'
                ),
                'label' => 'Password',
            )
        ));
		
		$this->add ( array (
				'name' => 'remember',
				'type' => 'Zend\Form\Element\Checkbox',
				'attributes' => array (
						'id' => 'remember'
				),
				'options' => array (
						'checkedValue' => 'J',
						'uncheckedValue' => 'N',
						'label' => 'ADMIN_LOGIN_LABEL_REMEMBER_ME',
				),
		) );
		
		$this->add ( array (
				'name' => 'submit',
				'type' => 'Zend\Form\Element\Submit',
				'attributes' => array (
						'id' => 'submit',
						'value' => 'Leeeeeroyyy Jenkinssss!',
						'class' => 'btn btn-primary'
				)
		) );
	
	}
}