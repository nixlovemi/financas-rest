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
    private $_apiToken = '0e7f8a9d3bdc13c386a810e236e73c4d';

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
            'BaseDespesa' => [
                'require' => 'TbBaseDespesa.php',
                'class'   => 'TbBaseDespesa',
            ],
            'Menu' => [
                'require' => 'TbMenu.php',
                'class'   => 'TbMenu',
            ],
            'MetaDespesa' => [
                'require' => 'TbMetaDespesa.php',
                'class'   => 'TbMetaDespesa',
            ],
            'Usuario' => [
                'require' => 'TbUsuario.php',
                'class'   => 'TbUsuario',
            ],
            'Lancamento' => [
                'require' => 'TbLancamento.php',
                'class'   => 'TbLancamento',
            ]
        ];
    }

    private function getApiHandlerToken() {
        return $this->_apiToken;
    }

    private function getAuthorizationHeader() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function checkBearerToken() {
        // header = Authorization:
        // value = Bearer $TOKEN

        $headerToken = $this->getBearerToken();
        return $this->getApiHandlerToken() === $headerToken;
    }

    public function execRequest() {
        require_once("classes/Response.php");
        $Response = new Response();

        if( !$this->checkBearerToken() ) {
            $Response->showError("Invalid API Token!");
        } else {
            $functionName          = $_REQUEST['functionName'] ?? NULL;
            $id                    = $_REQUEST['id'] ?? NULL;
            $basePath              = $this->_basePath;
            $_SESSION["BASE_PATH"] = $basePath;

            $retFunctionMap = $this->_functionMap[$functionName] ?? NULL;
            $requireFile    = $retFunctionMap['require'] ?? '';
            $pathToInclude  = $basePath . "Functions/$requireFile";
            $requireExists  = file_exists($pathToInclude);
            $requireIsFile  = is_file($pathToInclude);

            if ($requireExists && $requireIsFile) {
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
}