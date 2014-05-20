<?php
namespace Application\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as ZendControllerFlashMessenger;
use Zend\View\Helper\FlashMessenger as ZendFlashMessenger;

class FlashMessenger extends ZendFlashMessenger
{
	public function render($namespace = ZendControllerFlashMessenger::NAMESPACE_DEFAULT, array $classes = array()){

		// set classes
		$this->classMessages = array(
				'error' 	=> 'alert alert-dismissable alert-danger',
				'default' 	=> 'alert alert-dismissable alert-warning',
				'info' 		=> 'alert alert-dismissable alert-info',
				'success'	=> 'alert alert-dismissable alert-success'
		);

		$this->setMessageSeparatorString('</li><li>');
		$this->setMessageCloseString ('</li></ul></div>');
		
		$this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('SYS_MESSAGES_LABEL_WARNING', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
		$default = parent::render( ZendControllerFlashMessenger::NAMESPACE_DEFAULT );
		
		$this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('SYS_MESSAGES_LABEL_ERROR', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
		$error = parent::render( ZendControllerFlashMessenger::NAMESPACE_ERROR );
		
		$this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('SYS_MESSAGES_LABEL_INFORMATION', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
		$info = parent::render( ZendControllerFlashMessenger::NAMESPACE_INFO );
		
		$this->setMessageOpenFormat('<div%s><h4>' . $this->getTranslator()->translate('SYS_MESSAGES_LABEL_SUCCESS', $this->getTranslatorTextDomain() ) . '</h4><ul><li>');
		$success = parent::render( ZendControllerFlashMessenger::NAMESPACE_SUCCESS );
		
		
		$messageContainer = $default
							. $error
							. $info
							. $success;

		return $messageContainer;
	}
}