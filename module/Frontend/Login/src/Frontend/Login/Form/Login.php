<?php
namespace Frontend\Login\Form;

use Zend\Form\Form;

class Login extends Form {
	
	public function init() {

		$this->setAttribute ( 'method', 'post' );
		
		$username = new \Zend\Form\Element\Text('username');
		$username->setAttribute('class', 'text');
		$username->setAttribute('id', 'username');
		$username->setLabel('ADMIN_LOGIN_LABEL_USERNAME');
		$this->add( $username );
		
		$this->add ( array (
				'name' => 'password',
				'type' => 'Zend\Form\Element\Password',
				'attributes' => array (
						'class' => 'text',
						'id' => 'password',
				),
				'options' => array(
						'label' => 'ADMIN_LOGIN_LABEL_PASSWORD',
				),
		) );
		
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
						'value' => 'ADMIN_LOGIN_BUTTON_LOGIN_SUBMIT',
						'class' => 'button ok'
				)
		) );
	
	}

}