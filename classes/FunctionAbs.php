<?php
# $message = $e->getMessage();

abstract class FunctionAbs {
    private $_arrFields     = [];
    private $_tableName     = '';
    private $_tableNameEcho = '';
    private $_primaryKey    = '';
    private $_orderByField  = '';
    public $_Response;
    public $_Request;

    public function __construct($arrVars) {
        $this->_tableName     = $arrVars["tableName"] ?? '';
        $this->_primaryKey    = $arrVars["primaryKey"] ?? '';
        $this->_orderByField  = $arrVars["orderByField"] ?? '';
        $this->_tableNameEcho = strtolower(str_replace(['tb_', '_'], '', $this->_tableName));

        $basePath = $_SESSION["BASE_PATH"] ?? '';
        require_once($basePath . 'Response.php');
        $this->_Response = new Response();
        $this->_Request  = json_decode(file_get_contents("php://input"), true);
    }

    public function getConn() {
        require_once('Database.php');
        $Database = new Database();
        return $Database->getConnection();
    }

    public function addField($name, $type, $default) {
        $this->_arrFields[] = array(
            "name"    => $name,
            "type"    => $type,
            "default" => $default,
        );
    }

    public function autoLoad($id, $method='') {
        if ($method !== '') {
            $this->proccessMethod($method);
        } else {
            $this->proccessRequestMethod($id);
        }
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

    private function getFieldType($fieldName) {
        foreach ($this->_arrFields as $field) {
            if ($field["name"] === $fieldName) {
                return $field["type"];
            }
        }

        return false;
    }

    private function getStrFieldsName() {
        $arrFieldName = [];
        foreach ($this->_arrFields as $field) {
            $arrFieldName[] = $field["name"];
        }

        return implode(",", $arrFieldName);
    }

    public function query($arrFilters) {
        $echoTbName    = $this->_tableNameEcho;
        $strFieldsName = $this->getStrFieldsName();
        $tableName     = $this->_tableName;
        $orderBy       = $this->_orderByField;

        $sql  = " SELECT $strFieldsName FROM $tableName WHERE TRUE ";
        foreach ($arrFilters as $fieldName => $fieldValue) {
            if ($fieldValue !== NULL) {
                $fieldType = $this->getFieldType($fieldName);

                $strWhere  = "";
                if ($fieldType === "string") {
                    $strWhere = " AND $fieldName LIKE '%" . utf8_decode($fieldValue) . "%'";
                } else {
                    $strWhere = " AND $fieldName = $fieldValue";
                }

                $sql .= $strWhere;
            }
        }
        $sql .= " ORDER BY $orderBy ";

        try {
            $conn = $this->getConn();
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $this->_Response->showError("($echoTbName@Query) Error getting information about this endpoint!");
            } else {
                $rs     = $conn->query($sql);
                $rows   = $rs->fetchAll(PDO::FETCH_NUM);
                return $this->queryArrRet($rows);
            }
        } catch (PDOException $e) {
            // Do your error handling here
            // $message = $e->getMessage();
            $this->_Response->showError("($echoTbName@PDOQuery) Error getting information about this endpoint!");
        }
    }

    private function queryArrRet($rows) {
        $arrRet = [];

        foreach ($rows as $row) {
            $arrLine = [];
            for ($i = 0; $i < count($this->_arrFields); $i++) {
                $field      = $this->_arrFields[$i];
                $fieldName  = $field["name"] ?? "";
                $fieldType  = $field["type"] ?? "";
                $fieldValue = $row[$i] ?? "";

                if ($fieldType === 'string') {
                    $fieldValue = utf8_decode($fieldValue);
                }

                $arrLine[$fieldName] = $fieldValue;
            }

            $arrRet[] = $arrLine;
        }

        return $arrRet;
    }

    public function getTbEchoName() {
        return $this->_tableNameEcho;
    }

    public function fGet($id){
        $echoTbName = $this->_tableNameEcho;
        $arrRet     = $this->query(
            array(
                $this->_primaryKey => $id
            )
        );
        $this->_Response->showSuccess("($echoTbName@PDOPost) Information returned.", [$arrRet]);
    }

    public function fPost() {
        $request       = $this->_Request;
        $arrFieldName  = [];
        $arrFieldValue = [];

        foreach ($this->_arrFields as $field) {
            $fieldName   = $field["name"];
            $fieldValue  = (isset($request[$fieldName])) ? $request[$fieldName]: $field["default"];

            $canAddField = ($fieldName !== $this->_primaryKey) || ($fieldName === $this->_primaryKey && $fieldValue > 0);
            if($canAddField){
                if ($field["type"] === "string" && $fieldValue !== 'NULL') {
                    $fieldValue = "'" . utf8_encode($fieldValue) . "'";
                }

                $arrFieldName[]  = $fieldName;
                $arrFieldValue[] = $fieldValue;
            }
        }

        $echoTbName  = $this->_tableNameEcho;
        $tableName   = $this->_tableName;
        $tableFields = implode(",", $arrFieldName);
        $tableValues = implode(",", $arrFieldValue);
        $sql         = "INSERT INTO $tableName ($tableFields) VALUES ($tableValues)";

        try {
            $conn        = $this->getConn();
            $stmt        = $conn->prepare($sql);

            if (!$stmt) {
                $this->_Response->showError("($echoTbName@Post) Error getting information about this endpoint!");
            } else {
                $conn->query($sql);
                $vPkValue = $conn->lastInsertId();

                $arrRet = $this->query(array(
                    $this->_primaryKey => $vPkValue
                ));
                $this->_Response->showSuccess("(" . $this->_tableNameEcho . ") Information added successfully!", $arrRet);
            }
        } catch (PDOException $e) {
            $this->_Response->showError("($echoTbName@PDOPost) Error getting information about this endpoint!");
        }
    }

    public function fPut() {
        $request         = $this->_Request;
        $primaryKey      = $this->_primaryKey;
        $primaryKeyValue = $request[$primaryKey] ?? "";
        $arrUpdateFields = [];

        foreach ($this->_arrFields as $field) {
            $fieldName = $field["name"];
            if (isset($request[$fieldName]) && $fieldName !== $primaryKey) {
                $fieldValue = $request[$fieldName];
                if ($field["type"] === "string") {
                    $fieldValue = "'" . utf8_encode($fieldValue) . "'";
                }

                $arrUpdateFields[] = " $fieldName = $fieldValue ";
            }
        }

        $echoTbName  = $this->_tableNameEcho;
        $tableName   = $this->_tableName;
        $strFields   = implode(",", $arrUpdateFields);
        $sql         = "UPDATE $tableName SET $strFields WHERE $primaryKey = $primaryKeyValue";

        try {
            $conn        = $this->getConn();
            $stmt        = $conn->prepare($sql);

            if (!$stmt) {
                $this->_Response->showError("($echoTbName@Put) Error getting information about this endpoint!");
            } else {
                $conn->query($sql);

                $arrRet = $this->query(array(
                    $this->_primaryKey => $primaryKeyValue
                ));
                $this->_Response->showSuccess("(" . $this->_tableNameEcho . ") Information updated successfully!", $arrRet);
            }
        } catch (PDOException $e) {
            $this->_Response->showError("($echoTbName@PDOPut) Error getting information about this endpoint!");
        }
    }

    public function fDelete($id) {
        $primaryKey = $this->_primaryKey;
        $echoTbName = $this->_tableNameEcho;
        $tableName  = $this->_tableName;

        $sql  = "DELETE FROM $tableName WHERE $primaryKey = $id";

        try {
            $conn = $this->getConn();
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $this->_Response->showError("($echoTbName@Delete) Error getting information about this endpoint!");
            } else {
                $conn->query($sql);
                $this->_Response->showSuccess("($echoTbName) Information deleted successfully!", []);
            }
        } catch (PDOException $e) {
            $this->_Response->showError("($echoTbName@PDODelete) Error getting information about this endpoint!");
        }
    }
}