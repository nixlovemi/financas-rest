<?php
abstract class FunctionAbs {
    public $_Response;
    public $_Request;

    public function __construct() {
        $basePath = $_SESSION["BASE_PATH"] ?? '';
        require_once($basePath . 'Response.php');
        $this->_Response = new Response();
        $this->_Request  = json_decode(file_get_contents("php://input"), true);
    }

    public function autoLoad($id, $method='') {
        if ($method !== '') {
            $this->proccessMethod($method);
        } else {
            $this->proccessRequestMethod($id);
        }
    }

    public function getConn() {
        require_once('Database.php');
        $Database = new Database();
        return $Database->getConnection();
    }

    private function proccessMethod($method) {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->_Response->showError('We could not proccess your request! Try again later.');
        }
    }

    private function proccessRequestMethod($id='') {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === 'GET') {
            $this->fGet($id);
        } else if ($requestMethod === 'POST') {
            $this->fPost();
        } else if ($requestMethod === 'PUT') {
            $this->fPut();
        } else if ($requestMethod === 'DELETE') {
            $this->fDelete($id);
        } else {
            $this->_Response->showError("The requested method desn't exists!");
        }
    }

    abstract protected function fGet($id);
    abstract protected function fPost();
    abstract protected function fPut();
    abstract protected function fDelete($id);
}