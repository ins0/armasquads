<?php
namespace Frontend\Squads\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Frontend\Application\Form\AbstractFrontendFieldset;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class MemberFieldset extends AbstractFrontendFieldset implements ServiceManagerAwareInterface
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

        // Tag
        $this->add(array(
            'name' => 'tag',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'tag',
                'class' => 'form-control',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Squad Tag',
            )
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'form-control',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Squad Name',
            )
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'title',
                'class' => 'form-control',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Squad Title',
            )
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Squad Email',
            )
        ));

        $this->add(array(
            'name' => 'logo',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(
                'id' => 'logo',
                'class' => 'form-control',
                'accept' => 'image/jpg,image/jpeg,image/gif,image/png'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'col-lg-2 control-label'
                ),
                'label' => 'Logo',
            )
        ));

    }
}