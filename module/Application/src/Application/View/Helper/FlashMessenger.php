<?php
namespace Application\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as ZendControllerFlashMessenger;
use Zend\View\Helper\FlashMessenger as ZendFlashMessenger;

class FlashMessenger extends ZendFlashMessenger
{
    private function setDefaultFlashMessengerSettings()
    {
        // set classes
        $this->classMessages = array(
            'error' 	=> 'alert alert-dismissable alert-danger',
            'default' 	=> 'alert alert-dismissable alert-warning',
            'info' 		=> 'alert alert-dismissable alert-info',
            'success'	=> 'alert alert-dismissable alert-success',
        );

        $this->setMessageSeparatorString('</li><li>');
        $this->setMessageCloseString ('</li></ul></div>');
    }

    public function renderMessagesDirectFormErrors()
    {
        $this->setDefaultFlashMessengerSettings();

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Error', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $error = parent::renderCurrent('error-formError', array(
            'alert alert-dismissable alert-danger'
        ));

        return $error;
    }

    public function renderMessagesDirect()
    {
        $this->setDefaultFlashMessengerSettings();

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Warning', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $default = parent::renderCurrent( ZendControllerFlashMessenger::NAMESPACE_DEFAULT );

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Error', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $error = parent::renderCurrent( ZendControllerFlashMessenger::NAMESPACE_ERROR );

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Information', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $info = parent::renderCurrent( ZendControllerFlashMessenger::NAMESPACE_INFO );

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Success', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $success = parent::renderCurrent( ZendControllerFlashMessenger::NAMESPACE_SUCCESS );

        $messageContainer = $default
            . $error
            . $info
            . $success;

        return $messageContainer;
    }

	public function render($namespace = ZendControllerFlashMessenger::NAMESPACE_DEFAULT, array $classes = array(), $autoEscape = null)
    {
        $this->setDefaultFlashMessengerSettings();

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Warning', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $default = parent::render( ZendControllerFlashMessenger::NAMESPACE_DEFAULT );

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Error', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $error = parent::render( ZendControllerFlashMessenger::NAMESPACE_ERROR );

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Information', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $info = parent::render( ZendControllerFlashMessenger::NAMESPACE_INFO );

        $this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('Success', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
        $success = parent::render( ZendControllerFlashMessenger::NAMESPACE_SUCCESS );

		$messageContainer = $default
							. $error
							. $info
							. $success;

		return $messageContainer;
	}
}