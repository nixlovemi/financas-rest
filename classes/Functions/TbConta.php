<?php
# $conn->errorInfo()
$basePath = $basePath ?? ''; # it comes from ApiHandler
include("$basePath/FunctionAbs.php");

class TbConta extends FunctionAbs
{
    public function __construct()
    {
        parent::__construct(array(
            "tableName"    => "tb_conta",
            "primaryKey"   => "con_id",
            "orderByField" => "con_nome",
        ));
        $this->addField('con_id', 'integer', 'NULL');
        $this->addField('con_nome', 'string', 'NULL');
        $this->addField('con_sigla', 'string', 'NULL');
        $this->addField('con_data_saldo', 'string', 'NULL');
        $this->addField('con_saldo_inicial', 'numeric', 0);
        $this->addField('con_ativo', 'integer', 1);
    }

    public function fSoAtivas() {
        $arrRet = $this->query(
            array(
                "con_ativo" => 1
            )
        );
        $this->_Response->showSuccess("(Conta@fSoAtivas) Information returned.", [$arrRet]);
    }
}