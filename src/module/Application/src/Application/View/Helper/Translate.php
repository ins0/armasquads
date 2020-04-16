<?php
namespace Application\View\Helper;

use Doctrine\Common\Util\Debug;
use Zend\I18n\View\Helper\Translate as ZendViewHelperTranslate;

class Translate extends ZendViewHelperTranslate
{

	public function __invoke() {
		
		$args = func_get_args();
		
		$message = parent::__invoke( $args[0] );
		unset( $args[0] );

        /**
         * @todo Execption to few Arguments
         */
        $message = @vsprintf($message, $args );

		return $message;
	}
}