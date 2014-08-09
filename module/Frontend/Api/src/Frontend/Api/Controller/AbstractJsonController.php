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

    /**
     * @var array
     */
    protected $postData;

    /**
     * Get the current Post data from a Request
     * @return array
     */
    protected function getArrayPostData()
    {
        return (array) $this->postData;
    }

    /**
     * Validate POST DATA
     *
     * @return bool|string
     */
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

    /**
     * Try to get the Request API KEY
     *
     * @return mixed
     */
    private function requestApiKey()
    {
        // check api key
        $apiKey = $this->getRequest()->getHeaders()->get('X-API-Key');

        if( $apiKey !== false )
        {
            return $apiKey->getFieldValue();
        }

        return $this->getRequest()->getQuery('key', false);
    }

    /**
     * On API Dispatch
     *
     * @param MvcEvent $e
     * @return ApiResponse|mixed
     * @throws \Zend\View\Exception\DomainException
     * @throws \Exception
     */
    public function onDispatch(MvcEvent $e)
    {
        $apiResponse = new ApiResponse();
        /** @var GenericHeader $apiRequestKey */
        if( $apiRequestKey = $this->requestApiKey() )
        {
            /** @var Key $key */
            $apiKeyRepository = $this->getEntityManager()->getRepository('Frontend\Api\Entity\Key');
            if( $key = $apiKeyRepository->findOneBy(array('key' => $apiRequestKey )) )
            {
                // check for limit reset
                $key->checkForRateReset();

                // show key limit usage
                $apiResponse->getHeaders()->addHeaders(array(
                    'X-RateLimit-Limit' => $key->getLimit(),
                    'X-RateLimit-Remaining' => $key->getRemainingRate(),
                    'X-RateLimit-Reset' => $key->getNextRateReset()->getTimestamp(),
                ));

                // check if key banned
                if( !$key->getStatus() )
                {
                    $apiResponse->setErrorMessage('API key banned');
                    $apiResponse->setStatusCode(403);
                    return $apiResponse;
                }

                // check key limit
                if( $key->isLimitExceeded() )
                {
                    $apiResponse->setErrorMessage('API limit exceeded');
                    $apiResponse->setStatusCode(429);
                    return $apiResponse;
                }

                // set the current api user
                $this->setApiIdentity($key->getUser());

                // all fine get work done
                $routeMatch = $e->getRouteMatch();
                if (!$routeMatch) {
                    throw new DomainException('Missing route matches; unsure how to retrieve action');
                }

                $action = $routeMatch->getParam('action', array('not-found'));
                $requestMethod = $_SERVER['REQUEST_METHOD'];

                if( is_string($action) )
                {
                    $apiResponse->setStatusCode(400);
                    switch($action)
                    {
                        case 'selectVersion': $apiResponse->setErrorMessage('please specific api version /api/v[versionNumber]'); break;
                        case 'selectResource': $apiResponse->setErrorMessage('no url resource path found');
                    }

                    return $apiResponse;
                }


                // fix for inject template listener
                $actionRequest = isset($action[$requestMethod]) ? $action[$requestMethod] : false;
                $routeMatch->setParam('action', $action);

                // add allow request methods
                $apiResponse->getHeaders()->addHeaders(array(
                    'Allow' => implode(',',array_keys($action))
                ));

                if( $requestMethod == 'POST' || $requestMethod == 'PUT')
                {
                    $result = $this->validatePostData();
                    if( $result !== true )
                    {
                        $apiResponse->setErrorMessage($result);
                        $apiResponse->setStatusCode(400);
                        return $apiResponse;
                    }
                }

                if (!$actionRequest || !method_exists($this, $actionRequest)) {

                    // invalid request
                    $apiResponse->setErrorMessage('method not supported');
                    $apiResponse->setStatusCode(501);
                    return $apiResponse;
                }

                /** @var ApiResponse $actionResponse */
                $actionResponse = $this->$actionRequest();
                if( $actionResponse instanceof ApiResponse )
                {
                    if( ! $actionResponse->hasError() )
                    {
                        // update successfully api request to key
                        $key->update();
                        $this->getEntityManager()->flush();
                    }

                    $actionResponse->getHeaders()->addHeaders($apiResponse->getHeaders());
                    return $actionResponse;

                } else {
                    throw new \Exception('Invalid API response');
                }

            } else {

                $apiResponse->setErrorMessage('api key invalid');
                $apiResponse->setStatusCode(403);
                return $apiResponse;
            }
        }

        // something is invalid with the key error
        $apiResponse->setErrorMessage('api key not found');
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