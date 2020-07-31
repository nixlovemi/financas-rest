<?php
# $conn->errorInfo()
$basePath = $basePath ?? ''; # it comes from ApiHandler
include("$basePath/FunctionAbs.php");

class TbConta extends FunctionAbs {
    private function queryContas($arrParam) {
        $vConId    = $arrParam["con_id"] ?? NULL;
        $vConAtivo = $arrParam["con_ativo"] ?? NULL;

        $where = "";
        if($vConId !== NULL && $vConId > 0) {
            $where .= " AND con_id = $vConId ";
        }
        if($vConAtivo !== NULL) {
            $where .= " AND con_ativo = $vConAtivo ";
        }

        $conn = $this->getConn();
        $sql  = "
            SELECT con_id, con_nome, con_sigla, con_data_saldo, con_saldo_inicial, con_ativo
            FROM tb_conta
            WHERE TRUE
            $where
            ORDER BY con_nome
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $this->_Response->showError('(Conta@Query) Error getting information about this endpoint!');
        } else {
            $rs     = $conn->query($sql);
            $rows   = $rs->fetchAll(PDO::FETCH_NUM);
            $arrRet = [];

            foreach ($rows as $row) {
                $arrRet[] = array(
                    "con_id"            => $row[0],
                    "con_nome"          => $row[1],
                    "con_sigla"         => $row[2],
                    "con_data_saldo"    => $row[3],
                    "con_saldo_inicial" => $row[4],
                    "con_ativo"         => $row[5],
                );
            }

            return $arrRet;
        }
    }

    public function fGet($id) {
        $arrRet = $this->queryContas(
            array(
                "con_id" => $id
            )
        );
        $this->_Response->showSuccess("(Conta@fGet) Information returned.", [$arrRet]);
    }

    public function fPost() {
        $vConNome         = $this->_Request['con_nome'] ?? '';
        $vConSigla        = $this->_Request['con_sigla'] ?? '';
        $vConDataSaldo    = $this->_Request['con_data_saldo'] ?? NULL;
        $vConSaldoInicial = $this->_Request['con_saldo_inicial'] ?? 0;
        $vConAtivo        = $this->_Request['con_ativo'] ?? 1;

        $conn = $this->getConn();
        $sql  = "
            INSERT INTO tb_conta (con_nome, con_sigla, con_data_saldo, con_saldo_inicial, con_ativo)
            VALUES ('$vConNome', '$vConSigla', '$vConDataSaldo', $vConSaldoInicial, $vConAtivo)
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $this->_Response->showError('(Conta@Post) Error getting information about this endpoint!');
        } else {
            $conn->query($sql);
            $vConId = $conn->lastInsertId();

            $arrRet = $this->queryContas(array(
                "con_id" => $vConId
            ));

            $this->_Response->showSuccess('(Conta) Information added successfully!', $arrRet);
        }
    }

    public function fPut() {
        $vConId           = $this->_Request['con_id'] ?? NULL;
        $vConNome         = $this->_Request['con_nome'] ?? '';
        $vConSigla        = $this->_Request['con_sigla'] ?? '';
        $vConDataSaldo    = $this->_Request['con_data_saldo'] ?? NULL;
        $vConSaldoInicial = $this->_Request['con_saldo_inicial'] ?? 0;
        $vConAtivo        = $this->_Request['con_ativo'] ?? 1;

        $conn = $this->getConn();
        $sql  = "
            UPDATE tb_conta
            SET con_nome = '$vConNome', con_sigla = '$vConSigla', con_data_saldo = '$vConDataSaldo',
            con_saldo_inicial = $vConSaldoInicial, con_ativo = $vConAtivo
            WHERE con_id = $vConId
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $this->_Response->showError('(Conta@Put) Error getting information about this endpoint!');
        } else {
            $conn->query($sql);

            $arrRet = $this->queryContas(array(
                "con_id" => $vConId
            ));
            
            $this->_Response->showSuccess('(Conta) Information updated successfully!', $arrRet);
        }
    }

    public function fDelete($id) {
        $conn = $this->getConn();
        $sql  = "
            DELETE FROM tb_conta
            WHERE con_id = $id
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $this->_Response->showError('(Conta@Delete) Error getting information about this endpoint!');
        } else {
            $conn->query($sql);
            $this->_Response->showSuccess('(Conta) Information deleted successfully!', []);
        }
    }

    public function fSoAtivas() {
        $arrRet = $this->queryContas(
            array(
                "con_ativo" => 1
            )
        );
        $this->_Response->showSuccess("(Conta@fSoAtivas) Information returned.", [$arrRet]);
    }
}