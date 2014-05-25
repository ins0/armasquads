<?php
namespace Frontend\Squads\Form;

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
                'class' => 'col-xs-12 col-lg-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-xs-12 col-lg-2 control-label'
                ),
                'label' => 'UUID',
            )
        ));

        // username
        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'username',
                'class' => 'col-lg-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
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
                'class' => 'col-lg-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Name',
            )
        ));

        // Email
        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'col-lg-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
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
                'class' => 'col-lg-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
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
                'class' => 'col-lg-8',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label',
                    'required' => 'required'
                ),
                'label' => 'Remark',
            )
        ));

        // remark
        $this->add(array(
            'name' => 'delete',
            'type' => 'Zend\Form\Element\Button',
            'attributes' => array(
                'id' => 'delete',
                'class' => 'btn btn-danger',
                'onClick' => 'javascript:$(this).parent().parent().slideUp(function(){$(this).remove()});'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => ''
                ),
                'label' => 'Remove Member',
            )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(

        );
    }

}