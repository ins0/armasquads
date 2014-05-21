<?php
namespace Frontend\Squads\Form\Filter;
use Doctrine\ORM\EntityManager;
use Zend\InputFilter\InputFilter;

class Squad extends InputFilter {

    public function __construct( EntityManager $entityManager ) {

        // Tag
        $this->add(
            array(
                'name'       => 'tag',
                'required'   => false,
                'filters'    => array(),
                'validators' => array()
            )
        );

        // Name
        $this->add(
            array(
                'name'       => 'name',
                'required'   => true,
                'filters'    => array(),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY=> 'This squad needs a name!'
                            )
                        )
                    )
                )
            )
        );

        // Title
        $this->add(
            array(
                'name'       => 'title',
                'required'   => false,
                'filters'    => array(),
                'validators' => array()
            )
        );

        // Email
        $this->add(
            array(
                'name'       => 'email',
                'required'   => false,
                'filters'    => array(),
                'validators' => array()
            )
        );

        // Upload
        $this->add(
            array(
                'name'       => 'logo',
                'required'   => false,
                'filters'    => array(),
                'validators' => array(
                    array(
                        'name'    => 'Zend\Validator\File\Extension',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'extension' => array('jpg','jpeg','gif','png'),
                            'messages' => array(
                                \Zend\Validator\File\Extension::FALSE_EXTENSION=> 'Es werden folgende Logos unterstützt: png,jpg,jpeg,gif'
                            )
                        )
                    ),
                ),
            )
        );

    }

}