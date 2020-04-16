<?php
namespace Application\View\Helper;

use Zend\View\Helper\ServerUrl as ZendViewHelperServerUrl;

class ServerUrl extends ZendViewHelperServerUrl
{

	public function __invoke($tld = null) {

        $currentServerUrl = parent::__invoke(null);

        if( ! $tld )
            return $currentServerUrl;

        $tld = str_replace('.', '', $tld);
        $tld = '.' . $tld;

        $uri = parse_url( $currentServerUrl );
        $uri['host'] = preg_replace('#\.([a-z]+)$#', $tld, $uri['host'], 1);

		return $uri['scheme'] . '://' . $uri['host'];
	}
}