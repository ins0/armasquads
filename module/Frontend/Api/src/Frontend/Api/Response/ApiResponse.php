<?php
namespace Frontend\Api\Response;

use Zend\Http\Response;

class ApiResponse extends Response
{
    protected $errorMessage = null;
    protected $result = null;

    public function __construct($result = null, $errorMessage = null, $statusCode = null)
    {
        $this->getHeaders()->addHeaders(array(
            'Content-Type' => 'application/json; charset=utf-8'
        ));

        if( $result )
            $this->setResult($result);

        if( $errorMessage )
            $this->setErrorMessage($errorMessage);

        if( $statusCode )
            $this->setStatusCode($statusCode);
    }

    public function hasError()
    {
        return (bool) $this->getErrorMessage();
    }

    /**
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getContent()
    {
        $return = array();

        if( $this->getErrorMessage() )
        {
            $return['success'] = false;
            $return['error'] = is_array($this->getErrorMessage()) ? $this->getErrorMessage() : array($this->getErrorMessage());
        } else {

            $return['success'] = true;
            $return['result'] = $this->getResult();
        }

        return json_encode($return);
    }
}
