<?php
# $conn->errorInfo()
$basePath = $basePath ?? ''; # it comes from ApiHandler
include("$basePath/FunctionAbs.php");

class TbUsuario extends FunctionAbs
{
    public function __construct()
    {
        parent::__construct(array(
            "tableName"    => "tb_usuario",
            "primaryKey"   => "usu_id",
            "orderByField" => "usu_nome",
        ));
        $this->addField('usu_id', 'integer', 'NULL');
        $this->addField('usu_login', 'string', 'NULL');
        $this->addField('usu_senha', 'string', 'NULL');
        $this->addField('usu_nome', 'string', 'NULL');
        $this->addField('usu_sobrenome', 'string', 'NULL');
        $this->addField('usu_email', 'string', 'NULL');
        $this->addField('usu_ativo', 'integer', 1);
    }
}
