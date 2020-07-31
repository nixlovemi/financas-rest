<?php
include_once("Response.php");

class Database {
    // specify your own database credentials
    private $_host     = "mysql:host=localhost;dbname=croche_financas;charset=utf8";
    private $_userName = "croche_pw";
    private $_password = "verdaumsdrobs";
    public $_conn;

    // get the database connection
    public function __construct() {
        $this->checkDevEnv();
    }

    private function checkDevEnv() {
        $isDev = $_SESSION['AMBIENTE_DEV'] ?? false;

        if($isDev) {
            $this->_userName = 'root';
            $this->_password = '';
        }
    }

    public function getConnection() {
        
        $Response    = new Response();
        $this->_conn = null;

        $vHost     = $this->_host;
        $vUserName = $this->_userName;
        $vPassword = $this->_password;

        try {
            $db = new PDO($vHost, $vUserName, $vPassword);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_conn = $db;

            if (!$db) {
                $Response->showError("Connection failed: " . mysqli_connect_error());
            }
        } catch (PDOException $exception) {
            $Response->showError("Connection error: " . $exception->getMessage());
        }

        return $this->_conn;
    }
}
