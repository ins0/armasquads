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
                'required'   => true,
                'filters'    => array(),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY=> 'Please enter a Squad Tag'
                            )
                        )
                    ),
                    array(
                        'name'     => 'Callback',
                        'options' => array(
                            'message' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'FRONTEND_SQUAD_TAG_INVALID_FORMAT',
                            ),
                            'callback' => function($value) {
                                    if( $value['error'] != 0 )
                                        return false;

                                    Try {
                                        $image = new \Imagick( $value['tmp_name'] );
                                        if( ((2^$image->getimageheight())/(2^$image->getimagewidth())) == 1 && $image->getimageheight() <= 512 && $image->getimageheight() >= 16 )
                                        {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    } Catch ( \Exception $e )
                                    {
                                        return false;
                                    }
                                    return false;
                                },
                        ),
                    )
                )
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
                                \Zend\Validator\NotEmpty::IS_EMPTY=> 'Please enter a Squad Name'
                            )
                        )
                    )
                )
            )
        );

        // Homepage
        $this->add(
            array(
                'name'       => 'homepage',
                'required'   => false,
                'filters'    => array(),
                'validators' => array(
                    array(
                        'name'    => 'Zend\Validator\Uri',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'allowRelative' => false,
                            'messages' => array(
                                \Zend\Validator\Uri::INVALID=> 'FRONTEND_SQUAD_URL_INVALID_FORMAT',
                                \Zend\Validator\Uri::NOT_URI=> 'FRONTEND_SQUAD_URL_INVALID_FORMAT'
                            )
                        )
                    ),
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
                'validators' => array(
                    array(
                        'name'    => 'Zend\Validator\EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\EmailAddress::INVALID=> 'FRONTEND_SQUAD_EMAIL_INVALID_FORMAT'
                            )
                        )
                    ),
                )
            )
        );

        // DeleteLogo
        $this->add(
            array(
                'name'       => 'deleteLogo',
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
                    array(
                        'name'     => 'Callback',
                        'options' => array(
                            'message' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'FRONTEND_SQUAD_LOGO_INVALID_FORMAT',
                            ),
                            'callback' => function($value) {
                                if( $value['error'] != 0 )
                                    return false;

                                Try {
                                    $image = new \Imagick( $value['tmp_name'] );
                                    if( ((2^$image->getimageheight())/(2^$image->getimagewidth())) == 1 && $image->getimageheight() <= 512 && $image->getimageheight() >= 16 )
                                    {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                } Catch ( \Exception $e )
                                {
                                    return false;
                                }
                                return false;
                            },
                        ),
                    )
                ),
            )
        );

    }

}