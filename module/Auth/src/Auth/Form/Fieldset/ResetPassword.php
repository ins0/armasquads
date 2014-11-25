<?php
namespace Auth\Form\Fieldset;

use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Validator\Callback;
use Zend\Validator\Identical;

class ResetPassword extends Fieldset implements InputFilterProviderInterface
{
    /** @var ServiceLocatorAwareInterface */
    private $sm;

    public function __construct( $sm )
    {
        parent::__construct('resetPassword');
        $this->sm = $sm;

        $altesPasswort = new Password('oldPass');
        $altesPasswort->setLabel('LABEL_CHANGE_PASSWORD_OLD_PASS');
        $altesPasswort->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $altesPasswort->setAttribute('class', 'form-control');
        $altesPasswort->setAttribute('id', 'oldPass');
        $this->add($altesPasswort);

        $neuesPasswort = new Password('newPass');
        $neuesPasswort->setLabel('LABEL_CHANGE_PASSWORD_NEW_PASS');
        $neuesPasswort->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $neuesPasswort->setAttribute('class', 'form-control');
        $neuesPasswort->setAttribute('id', 'newPass');
        $this->add($neuesPasswort);

        $neuesPasswortConfirm = new Password('newPassConfirm');
        $neuesPasswortConfirm->setLabel('LABEL_CHANGE_PASSWORD_NEW_CONFIRM');
        $neuesPasswortConfirm->setLabelAttributes(array('class' => 'col-sm-2 control-label'));
        $neuesPasswortConfirm->setAttribute('class', 'form-control');
        $neuesPasswortConfirm->setAttribute('id', 'newPassConfirm');
        $this->add($neuesPasswortConfirm);

        $submit = new Submit('resetPasswordSubmit');
        $submit->setValue('LABEL_CHANGE_PASSWORD_SUBMIT');
        $submit->setAttribute('class', 'btn btn-default');
        $this->add($submit);

        return $this;
    }

    /**
     * Password Form Validate Callback
     *
     * @author  Marco Rieger
     * @param $value
     * @return mixed
     */
    public function currentPasswortCallback( $value ){

        if( ! $value )
            return false;

        $authService = $this->sm->get('AuthService');
        return $authService->getIdentity()->checkAgainstPassword($value);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'oldPass' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'ERROR_CHANGE_PASSWORD_ENTER_CURRENT'
                            ),
                        ),
                    ),
                    array(
                        'name'    => 'Zend\Validator\Callback',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'callback' => array($this, 'currentPasswortCallback'),
                            'messages' => array(
                                Callback::INVALID_VALUE =>      'ERROR_CHANGE_PASSWORD_WRONG'
                            )
                        )
                    )
                )
            ),

            'newPass' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'ERROR_CHANGE_PASSWORD_ENTER_NEW'
                            ),
                        ),
                    ),
                    array(
                        'name' => 'Zend\Validator\Identical',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'token' => 'newPassConfirm',
                            'messages' => array(
                                Identical::NOT_SAME=>    'ERROR_CHANGE_PASSWORD_NOT_SAME',
                            )
                        ),
                    )
                )
            ),

            'newPassConfirm' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' =>'NotEmpty',
                        'breakChainOnFailure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'ERROR_CHANGE_PASSWORD_ENTER_NEW_CONFIRM'
                            ),
                        ),
                    )
                )
            ),

        );
    }
}