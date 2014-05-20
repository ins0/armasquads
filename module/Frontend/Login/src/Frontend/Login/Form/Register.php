<?php
namespace Frontend\Login\Form;

use Zend\Form\Form;

class Login extends Form {
	
	public function init() {

		$this->setAttribute ( 'method', 'post' );
		
		$username = new \Zend\Form\Element\Text('username');
		$username->setAttribute('class', 'text');
		$username->setAttribute('id', 'username');
		$username->setLabel('REGISTER_LABEL_USERNAME');
		$this->add( $username );
		
		$this->add ( array (
				'name' => 'password',
				'type' => 'Zend\Form\Element\Password',
				'attributes' => array (
						'class' => 'text',
						'id' => 'password',
				),
				'options' => array(
						'label' => 'REGISTER_LABEL_PASSWORD',
				),
		) );

        $this->add ( array (
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array (
                'class' => 'text',
            ),
            'options' => array(
                'label' => 'REGISTER_LABEL_EMAIL',
            ),
        ) );
		
		$this->add ( array (
				'name' => 'submit',
				'type' => 'Zend\Form\Element\Submit',
				'attributes' => array (
						'id' => 'submit',
						'value' => 'LOGIN_BUTTON_LOGIN_SUBMIT',
						'class' => 'button ok'
				)
		) );
	
	}

}