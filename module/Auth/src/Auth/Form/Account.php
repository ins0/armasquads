<?php
namespace Auth\Form;

use Auth\Form\Fieldset\ResetPassword;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

class Account extends Form
{
    public function __construct( ServiceLocatorInterface $sm )
    {
        parent::__construct('account');

        $this->setAttribute('id', 'account');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');

        $resetPasswordFieldset = new ResetPassword( $sm );
        $resetPasswordFieldset->setName('resetPassword');
        $this->add($resetPasswordFieldset);

        $csrf = new \Zend\Form\Element\Csrf('csrf');
        $csrf->setOptions(array(
            'csrf_options' => array(
                'timeout' => 600,
                'messages' => array(
                    \Zend\Validator\Csrf::NOT_SAME => 'Das von Ihnen abgesendete Formular ist ungültig. Bitte versuchen Sie es erneut.'
                ),
            ),
        ));
        $this->add($csrf);
    }

}