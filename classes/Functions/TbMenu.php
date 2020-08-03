<?php
# $conn->errorInfo()
$basePath = $basePath ?? ''; # it comes from ApiHandler
include("$basePath/FunctionAbs.php");

class TbMenu extends FunctionAbs
{
    public function __construct()
    {
        parent::__construct(array(
            "tableName"    => "tb_menu",
            "primaryKey"   => "men_id",
            "orderByField" => "men_descricao",
        ));
        $this->addField('men_id', 'integer', 'NULL');
        $this->addField('men_descricao', 'string', 'NULL');
        $this->addField('men_controller', 'string', 'NULL');
        $this->addField('men_action', 'string', 'NULL');
        $this->addField('men_vars', 'string', 'NULL');
        $this->addField('men_id_pai', 'integer', 'NULL');
        $this->addField('men_ativo', 'integer', 1);
        $this->addField('men_icon', 'string', 'NULL');
        $this->addField('men_order', 'integer', 1);
    }
}
