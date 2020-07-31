<?php
if ((!defined('CONST_INCLUDE_KEY')) || (CONST_INCLUDE_KEY !== 'd4e2ad09-b1c3-4d70-9a9a-0e6115031985')) {
    // If someone tries to browse directly to this PHP file, send 404 and exit. It can only included
    // as part of our API.
    header("Location: /404.html", TRUE, 404);
    echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    die;
}

class ApiHandler {
    private $_functionMap;
    private $_basePath;

    public function __construct() {
        $this->loadFunctionMap();
        $this->loadBasePath();
    }

    private function loadBasePath() {
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/financas-rest/';

        $this->_basePath = $dir . 'classes/' ?? '';
    }

    private function loadFunctionMap() {
        // load up all public facing functions
        $this->_functionMap = [
            'Conta' => [
                'require' => 'TbConta.php',
                'class'   => 'TbConta',
            ],
            'Conta-SoAtivas' => [
                'require' => 'TbConta.php',
                'class'   => 'TbConta',
                'method'  => 'fSoAtivas',
            ],
        ];
    }

    public function execRequest() {
        require_once("classes/Response.php");
        $Response = new Response();

        $functionName          = $_REQUEST['functionName'] ?? NULL;
        $id                    = $_REQUEST['id'] ?? NULL;
        $basePath              = $this->_basePath;
        $_SESSION["BASE_PATH"] = $basePath;

        $retFunctionMap = $this->_functionMap[$functionName] ?? NULL;
        $requireFile    = $retFunctionMap['require'] ?? '';
        $pathToInclude  = $basePath . "Functions/$requireFile";
        $requireExists  = file_exists($pathToInclude);
        $requireIsFile  = is_file($pathToInclude);

        if($requireExists && $requireIsFile) {
            $ClassName = $retFunctionMap['class'] ?? '';
            $Method    = $retFunctionMap['method'] ?? '';

            require_once($pathToInclude);
            $RequestedClass = new $ClassName();
            $RequestedClass->autoLoad($id, $Method);
        } else {
            $Response->showError("The requested endpoint doesn't exists!");
        }
    }
}