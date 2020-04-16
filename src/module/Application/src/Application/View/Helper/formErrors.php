<?php
namespace Application\View\Helper;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\FieldsetInterface;
use Zend\I18n\View\Helper\AbstractTranslatorHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class errorMessages
 *
 * @author        Marco Rieger
 * @copyright     Copyright (c) 2013 AUBI-plus GmbH (http://www.aubi-plus.de)
 * @package       Application\View\Helper
 */
class formErrors extends AbstractTranslatorHelper implements ServiceLocatorAwareInterface
{

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Prüft ob ein CSRF Error in der Form vorliegt
     *
     * @author  Marco Rieger
     * @param $form
     * @return bool|\Zend\Form\Element
     */
    private function checkIsCsrfError( $form )
    {
        foreach ( $form->getMessages() as $elementID => $messages ) {

            /** @var $element \Zend\Form\Element */
            $element = $form->get( $elementID );

            if ($element instanceof Csrf) {
                return $element;
            }
        }

        return false;
    }

    /**
     * Ausgabe der Nachrichten
     *
     * @author  Marco Rieger
     * @return string
     */
    public function __invoke( FieldsetInterface $form )
    {
        $messageHolderStack = array();

        /** @var \Application\View\Helper\flashMessenger $flashMessenger **/
        $flashMessenger = $this->getServiceLocator()->get('flashMessenger');
        $pluginFlashMessenger = $flashMessenger->getPluginFlashMessenger();
        $namespace = $pluginFlashMessenger->getNamespace();
        // set namespace
        $pluginFlashMessenger->setNamespace('error-formError');

        $csrfElement = $this->checkIsCsrfError( $form );
        if ($csrfElement === false) {

            // normal error
            foreach ( $form->getMessages() as $elementID => $messages ) {

                /** @var $element \Zend\Form\Element */
                $element = $form->get( $elementID );

                if( $element instanceof Hidden ) {
                    // hidden element no error message
                    // continue;
                }

                if( $element instanceof FieldsetInterface ) {
                    // hidden element no error message
                    return $this->__invoke($element);
                }

                // set error classes
                $labelAttributes = $element->getLabelAttributes();
                if ( ! isset( $labelAttributes['class'] ) ) {
                    $labelAttributes['class'] = 'has-error';
                } else {
                    $labelAttributes['class'] .= ' has-error';
                }

                $element->setLabelAttributes( $labelAttributes );
                $element->setAttribute(
                    'class',
                    $element->getAttribute('class') . ' has-error'
                );

                // add error message
                foreach ($messages as $message) {
                    $messageHolderStack[] = $message;
                    //$pluginFlashMessenger->addMessage( $message );
                }
            }

        } else {

            // set warning namespace
            $pluginFlashMessenger->setNamespace('info-formError');

            // csrf error
            foreach ( $csrfElement->getMessages() as $message ) {
                $messageHolderStack[] = $message;
                //$pluginFlashMessenger->addMessage( $message );
            }
        }

        foreach ($messageHolderStack as $message) {
            $pluginFlashMessenger->addMessage( $message );
        }

        $return = $flashMessenger->renderMessagesDirectFormErrors();

        $pluginFlashMessenger->clearCurrentMessagesFromNamespace( $pluginFlashMessenger->getNamespace() );
        $pluginFlashMessenger->setNamespace( $namespace );

        return $return;
    }

    /**
     * Set the service locator.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

}