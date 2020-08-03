<?php
# $conn->errorInfo()
$basePath = $basePath ?? ''; # it comes from ApiHandler
include("$basePath/FunctionAbs.php");

class TbLancamento extends FunctionAbs
{
    public function __construct()
    {
        parent::__construct(array(
            "tableName"    => "tb_lancamento",
            "primaryKey"   => "lan_id",
            "orderByField" => "lan_vencimento",
        ));
        $this->addField('lan_id', 'integer', 'NULL');
        $this->addField('lan_despesa', 'string', 'NULL');
        $this->addField('lan_tipo', 'string', 'NULL');
        $this->addField('lan_parcela', 'string', 'NULL');
        $this->addField('lan_vencimento', 'string', 'NULL');
        $this->addField('lan_valor', 'numeric', 'NULL');
        $this->addField('lan_categoria', 'integer', 'NULL');
        $this->addField('lan_pagamento', 'string', 'NULL');
        $this->addField('lan_valor_pago', 'numeric', 'NULL');
        $this->addField('lan_conta', 'integer', 'NULL');
        $this->addField('lan_observacao', 'string', 'NULL');
        $this->addField('lan_confirmado', 'integer', 0);
    }
}
