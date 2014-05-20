<?php
namespace Frontend\Squads\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Frontend\Application\Form\AbstractFrontendForm;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Squad extends AbstractFrontendForm implements ServiceManagerAwareInterface
{

    public function __construct()
    {
        parent::__construct('squad');
    }

    public function init( \Frontend\Squads\Entity\Squad $object = null )
    {
        $this->setHydrator(new DoctrineObject($this->getEntityManager()));

        if( $object ) {
            $this->bind( $object );
        } else {
            $this->bind(new \Frontend\Squads\Entity\Squad() );
        }

        $object = $this->getObject();

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        //$this->setInputFilter(new \Servicebereich\Form\Gutscheinerfassung\Filter\Artikel(
        //    $this->getEntityManager()
        //));

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
                'class' => '',
            ),
            'options' => array(
                'label' => 'Squad Name',
            )
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'id' => 'email',
                'class' => '',
            ),
            'options' => array(
                'label' => 'Squad Email',
            )
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'title',
                'class' => '',
            ),
            'options' => array(
                'label' => 'Squad Title',
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