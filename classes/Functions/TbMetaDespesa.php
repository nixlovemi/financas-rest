<?php
# $conn->errorInfo()
$basePath = $basePath ?? ''; # it comes from ApiHandler
include("$basePath/FunctionAbs.php");

class TbMetaDespesa extends FunctionAbs
{
    public function __construct()
    {
        parent::__construct(array(
            "tableName"    => "tb_meta_despesa",
            "primaryKey"   => "mdp_id",
            "orderByField" => "mdp_ano DESC, mdp_mes, mdp_despesa",
        ));
        $this->addField('mdp_id', 'integer', 'NULL');
        $this->addField('mdp_despesa', 'integer', 'NULL');
        $this->addField('mdp_mes', 'numeric', 'NULL');
        $this->addField('mdp_ano', 'numeric', 'NULL');
        $this->addField('mdp_valor', 'numeric', 'NULL');
    }
}
