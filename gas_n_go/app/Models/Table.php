<?php

class Table
{
    public $tableHeaders;
    public $rowHeader;
    public $rowData;
    public $actions;

    public function __construct($tableHeaders, $rowHeader, $rowData, $actions = NULL)
    {
        $this->tableHeaders = str_replace('_', ' ', $tableHeaders);
        $this->rowHeader = $rowHeader;
        $this->rowData = $rowData;
        if (!is_null($actions))
            $this->actions = $actions;
    }
}
