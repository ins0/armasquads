<?php
namespace Frontend\Squads\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Frontend\Application\Form\AbstractFrontendForm;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Member extends AbstractFrontendForm implements ServiceManagerAwareInterface
{

    public function __construct()
    {
        parent::__construct('squad');
    }

    public function init( \Frontend\Squads\Entity\Member $object = null )
    {
        $this->setHydrator(new DoctrineObject($this->getEntityManager()));

        if( $object ) {
            $this->bind( $object );
        } else {
            $this->bind(new \Frontend\Squads\Entity\Squad() );
        }

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->setInputFilter(new \Frontend\Squads\Form\Filter\Squad(
            $this->getEntityManager()
        ));

        $this->add(array(
            'type' => 'Frontend\Squads\Form\MemberFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));

        // Submit
        $this->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'id' => 'submit',
                'class' => 'btn btn-success',
            ),
            'options' => array(
                'label' => '',
            )
        ));
    }
}