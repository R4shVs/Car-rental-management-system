<?php

class Action
{
    public $title;
    public $link;
    public $queryParam;
    public $color;

    const SELECT = 'text-green-600';
    const UPDATE = 'text-blue-600';
    const DELETE = 'text-red-600';

    public function __construct($title, $link, $queryParam, $color)
    {
        $this->title = $title;
        $this->link = $link;
        $this->queryParam = $queryParam;
        $this->color = $color;
    }
}
