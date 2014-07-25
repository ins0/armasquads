<?php
namespace Frontend\Api\Controller;

use Auth\Entity\Benutzer;
use Frontend\Api\Entity\Key;
use Frontend\Api\Response\ApiResponse;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Headers;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Exception\DomainException;

abstract class AbstractJsonController extends AbstractActionController {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Benutzer
     */
    protected $apiUser;

    protected $postData;

    protected function getArrayPostData()
    {
        return (array) $this->postData;
    }

    protected function validatePostData()
    {
        $postData = file_get_contents('php://input');

        if( !$postData || strlen($postData) <= 0)
        {
            return 'post data empty';
        }

        if( !$jsonData = @json_decode( $postData ) )
        {
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $jsonError = 'no errors';
                    break;
                case JSON_ERROR_DEPTH:
                    $jsonError = 'json data maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $jsonError = 'json data underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $jsonError = 'json data unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $jsonError = 'json data syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $jsonError = 'json data malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $jsonError = 'json data unknown error';
                    break;
            }

            return $jsonError;
        }

        $this->postData = $jsonData;
        return true;
    }

    public function onDispatch(MvcEvent $e)
    {
        // check api key
        $request = $this->getRequest();
        /** @var Headers $headers */
        $headers = $request->getHeaders();

        $apiResponse = new ApiResponse();

        // add no cache header
        $apiResponse->getHeaders()->addHeaders(array(
            'X-No-Cache' => uniqid(microtime(true))
        ));

        /** @var GenericHeader $apiRequestKey */
        if( $apiRequestKey = $headers->get('X-API-Key') )
        {
            $apiKeyRepository = $this->getEntityManager()->getRepository('Frontend\Api\Entity\Key');

            /** @var Key $key */

            if( $key = $apiKeyRepository->findOneBy(array('key' => $apiRequestKey->getFieldValue() )) )
            {
                // show key limit usage
                $apiResponse->getHeaders()->addHeaders(array(
                    'X-RateLimit-Limit' => $key->getLimit(),
                    'X-RateLimit-Remaining' => $key->getRemainingLimit(),
                    'X-RateLimit-Reset' => $key->getLimitResetDate(),
                ));

                if( $key->isLimitExceeded() )
                {
                    $apiResponse->setErrorMessage('API limit exceeded');
                    $apiResponse->setStatusCode(403);
                    return $apiResponse;
                }

                // set the current api user
                $this->setApiIdentity($key->getUser());

                // all fine get work done
                $routeMatch = $e->getRouteMatch();
                if (!$routeMatch) {
                    throw new DomainException('Missing route matches; unsure how to retrieve action');
                }

                $action = $routeMatch->getParam('action', 'not-found');
                $requestMethod = $_SERVER['REQUEST_METHOD'];

                // fix for inject template listener
                $action = isset($action[$requestMethod]) ? $action[$requestMethod] : false;
                $routeMatch->setParam('action', $action);

                if( $requestMethod == 'POST')
                {
                    $result = $this->validatePostData();
                    if( $result !== true )
                    {
                        $apiResponse->setErrorMessage($result);
                        $apiResponse->setStatusCode(400);
                        return $apiResponse;
                    }
                }

                if (!$action || !method_exists($this, $action)) {

                    // invalid request
                    $apiResponse->setErrorMessage('request not supported');
                    $apiResponse->setStatusCode(501);
                    return $apiResponse;
                }

                /** @var ApiResponse $actionResponse */
                $actionResponse = $this->$action();
                if( $actionResponse instanceof ApiResponse )
                {
                    if( ! $actionResponse->hasError() )
                    {
                        // success api response
                        if( $key->getLastRequest()->getTimestamp() < strtotime('today') )
                        {
                            $key->resetRequestsPerDay();
                        }

                        $key->updateLastRequest();
                        $key->updateRequestsPerDay();
                        $this->getEntityManager()->flush();
                    }

                    $actionResponse->getHeaders()->addHeaders($apiResponse->getHeaders());
                    return $actionResponse;

                } else {
                    throw new \Exception('Invalid API response');
                }

            } else {

                $apiResponse->setErrorMessage('api key (x-api-key) invalid');
                $apiResponse->setStatusCode(403);
                return $apiResponse;
            }
        }

        // something is invalid with the key error
        $apiResponse->setErrorMessage('api key (x-api-key) invalid');
        $apiResponse->setStatusCode(401);
        return $apiResponse;
    }

    /**
     * @return Benutzer
     */
    protected function getApiIdentity()
    {
        return $this->apiUser;
    }

    private function setApiIdentity(Benutzer $benutzer)
    {
        $this->apiUser = $benutzer;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }


}