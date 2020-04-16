<?php
namespace Application\View\Helper;

use Zend\I18n\View\Helper\DateFormat as ZendViewHelperDateFormat;

class DateFormat extends ZendViewHelperDateFormat
{

	public function __invoke( $dateObject, $dateFormat = null, $timeFormat = null, $locale = null, $pattern = null ) {
		
		if( ! $dateObject instanceof \DateTime ) {
			$dateObject = new \DateTime( $dateObject );
		}
		
		$dateString = parent::__invoke($dateObject, $dateFormat, $timeFormat, $locale, $pattern );
		return $dateString;
	}
}