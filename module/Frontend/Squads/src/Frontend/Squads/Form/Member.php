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

    public function init( $object = null )
    {
        $this->setHydrator(new DoctrineObject($this->getEntityManager()));

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $memberFieldset =  new MemberFieldset();
        $memberFieldset->setHydrator(new DoctrineObject($this->getEntityManager()));
        $memberFieldset->setEntityManager( $this->getEntityManager() );
        $memberFieldset->setUseAsBaseFieldset(true);

        $this->add(array(
            'name' => 'members',
            'type' => 'Zend\Form\Element\Collection',
            'options' => array(
                'count' => count($object->getMembers()),
                'should_create_template' => true,
                'target_element' => $memberFieldset,
                'allow_remove' => true,
                'allow_add' => true,
            ),
        ));

        $this->bind($object);
    }
}