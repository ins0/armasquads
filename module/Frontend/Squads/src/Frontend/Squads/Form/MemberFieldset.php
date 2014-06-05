<?php
namespace Frontend\Squads\Form;

use DoctrineModule\Validator\NoObjectExists;
use Frontend\Application\Form\AbstractFrontendFieldset;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class MemberFieldset extends AbstractFrontendFieldset implements ServiceManagerAwareInterface
{
    public function __construct()
    {
        parent::__construct('member');
        $this->setObject(new \Frontend\Squads\Entity\Member());
        $this->setLabel('Member');

        // uuid
        $this->add(array(
            'name' => 'uuid',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'uuid',
                'class' => 'col-xs-12 col-sm-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-xs-12 col-sm-2 control-label'
                ),
                'label' => 'PlayerID',
            )
        ));

        // username
        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'username',
                'class' => 'col-xs-12 col-sm-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-xs-12 col-sm-2 control-label'
                ),
                'label' => 'Username',
            )
        ));

        // Name
        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'col-xs-12 col-sm-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-xs-12 col-sm-2 control-label'
                ),
                'label' => 'Name',
            )
        ));

        // Email
        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'email',
                'class' => 'col-xs-12 col-sm-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-xs-12 col-sm-2 control-label'
                ),
                'label' => 'Email',
            )
        ));

        // icq
        $this->add(array(
            'name' => 'icq',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'icq',
                'class' => 'col-xs-12 col-sm-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-xs-12 col-sm-2 control-label'
                ),
                'label' => 'ICQ',
            )
        ));

        // remark
        $this->add(array(
            'name' => 'remark',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'remark',
                'class' => 'col-xs-12 col-sm-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-xs-12 col-sm-2 control-label',
                    'required' => 'required'
                ),
                'label' => 'Remark',
            )
        ));

    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name'       => 'uuid',
                'required'   => true,
                'allow_empty' => true,
                'filters'    => array(),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY=> 'PlayerID required!'
                            )
                        )
                    )

                )
            ),
            array(
                'name'       => 'username',
                'required'   => true,
                'allow_empty' => true,
                'filters'    => array(),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY=> 'Username required!'
                            )
                        )
                    )
                )
            ),
            array(
                'name'       => 'name',
                'required'   => false,
                'filters'    => array(),
                'validators' => array()
            ),
            array(
                'name'       => 'email',
                'required'   => false,
                'filters'    => array(),
                'validators' => array(
                    array(
                        'name'    => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Please enter a valid Email!'
                            )
                        )
                    ),
                )
            ),
            array(
                'name'       => 'icq',
                'required'   => false,
                'filters'    => array(),
                'validators' => array()
            )
        );
    }
}