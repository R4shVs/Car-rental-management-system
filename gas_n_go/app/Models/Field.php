<?php

class Field
{
    public $type;
    public $name;
    public $data;
    public $isRequired;
    public $value;

    public function __construct($type, $name, $data, $isRequired, $value = NULL)
    {
        $this->type = $type;
        $this->name = $name;
        $this->data = $data;
        $this->isRequired = $isRequired;
        $this->value = $value;
    }
}

?>

