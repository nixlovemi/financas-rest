<?php
# $conn->errorInfo()
$basePath = $basePath ?? ''; # it comes from ApiHandler
include("$basePath/FunctionAbs.php");

class TbBaseDespesa extends FunctionAbs {
    public function __construct() {
        parent::__construct(array(
            "tableName"    => "tb_base_despesa",
            "primaryKey"   => "bdp_id",
            "orderByField" => "bdp_descricao",
        ));
        $this->addField('bdp_id', 'integer', 'NULL');
        $this->addField('bdp_descricao', 'string', 'NULL');
        $this->addField('bdp_tipo', 'string', 'NULL');
        $this->addField('bdp_contabiliza', 'integer', 1);
        $this->addField('bdp_ativo', 'integer', 1);
    }
}
