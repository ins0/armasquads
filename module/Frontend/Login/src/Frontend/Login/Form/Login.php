<?php
namespace Frontend\Login\Form;

use Zend\Form\Form;

class Login extends Form {
	
	public function __construct() {

        parent::__construct('login');

		$this->setAttribute ('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

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