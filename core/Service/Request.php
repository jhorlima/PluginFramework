<?php
namespace Core\Service;

use Core\Service\Log;

class Request {

	private $method;
	private $listMethod = [
		'isPut' => 'PUT', 
		'isPost' => 'POST',
		'isGet' => 'GET',
		'isDelete' => 'DELETE',
		'isOptions' => 'OPTIONS',
	];
	private $isPost;
	private $isGet;
	private $isPut;
	private $isDelete;
	private $isOptions;
    private $isAjax;
    private $postData;
    private $getData;
    private $putData;
    private $deleteData;
    private $wpPath;

    public function setWpPath(\stdClass $wpPath) {
        $this->wpPath = $wpPath;
    }

    public function __construct() {
        $this->setMethod($_SERVER['REQUEST_METHOD']);
        if(defined( 'DOING_AJAX' ) && DOING_AJAX) {
            $this->isAjax = true;
        }
    }

    public function getMethod() {
		return $this->method;
	}

    private function setMethod($method) {
		if(in_array($method, $this->listMethod)){
			$this->method = $method;
			$this->{array_keys($this->listMethod, $this->method, true)[0]} = true;
            $this->processData($this->method);
		} else
            Log::register($this->wpPath->pathPlugin, "Método do request Invalido!");

	}

    private function processData($method) {
        $this->getData = $_GET;
        switch($method){
            case "POST":
                $this->postData = $_POST;
                $postTemp = file_get_contents('php://input');
                $postTemp = $this->isJSON($postTemp)?json_decode($postTemp,true):[];
                $this->postData = array_merge($this->postData, $postTemp);
            break;
            case "PUT":
                $putTemp = file_get_contents('php://input');
                $putTemp = $this->isJSON($putTemp)?json_decode($putTemp,true):[];
                $this->putData = $putTemp;
            break;
            case "DELETE":
                $deleteTemp = file_get_contents('php://input');
                $deleteTemp = $this->isJSON($deleteTemp)?json_decode($deleteTemp,true):[];
                $this->deleteData = $deleteTemp;
            break;
        }
    }

	public function isPost() {
		return !is_null($this->isPost) ? true : false;
	}

	public function isGet() {
		return !is_null($this->isGet) ? true : false;
	}

	public function isPut() {
		return !is_null($this->isPut) ? true : false;
	}

	public function isDelete() {
		return !is_null($this->isDelete) ? true : false;
	}

	public function isOptions() {
		return !is_null($this->isOptions) ? true : false;
	}

    public function isAjax() {
        return !is_null($this->isAjax) ? true : false;
    }

    public function getDeleteData() {
        return $this->isDelete() ? $this->deleteData : [];
    }

    public function getPutData() {
        return $this->isPut() ? $this->putData : [];
    }

    public function getGetData() {
        return $this->getData;
    }

    public function getPostData() {
        return $this->isPost() ? $this->postData : [];
    }

    private function isJSON($string){
        return is_string($string) && is_object(json_decode($string)) ? true : false;
    }
}