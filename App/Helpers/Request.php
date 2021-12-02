<?php


namespace App\Helpers;


use http\Exception\InvalidArgumentException;

class Request
{
    private $httpRequestMethod = [];
    private $data = [];

    /**
     * Request constructor.
     * @throws \Exception
     */
    public function __construct() {
        $this->setHttpRequestMethod();
        $this->setInputData();
    }

    /**
     *
     */
    protected function setHttpRequestMethod() {
        $this->httpRequestMethod = $this->validateHttpRequestMethod($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @param $input
     * @return mixed
     */
    protected function validateHttpRequestMethod($input) {
        if(empty($input)) {
            throw new InvalidArgumentException('not valid value');
        }
        switch ($input) {
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
            case 'HEAD':
                return $input;
            default:
                throw new InvalidArgumentException('Unexpected value.');
        }
    }

    protected function setInputData() {
        switch ($this->httpRequestMethod) {
            case 'GET':
            case 'HEAD':
                $this->setDataFromGet();
                break;
            case 'POST':
                $this->setDataFromPost();
                break;
            default:
                throw new \Exception(
                    "Unmapped httpActionMethod. Value provided: '{$this->httpRequestMethod}'"
            );
        }
    }

    protected function setDataFromGet() {
        $this->data = $_GET;
    }

    protected function setDataFromPost() {
        $this->data = $_POST;
    }

    public function all(){
        return $this->data;
    }
}