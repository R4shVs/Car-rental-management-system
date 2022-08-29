<?php
class Chart
{
    private $dataSet;
    private $y_axis;
    private $x_axis;
    private $title;
    private $type;

    const LINE = 'monthly_chart';
    const BAR = 'annual_chart';
    const PIE = 'performance_chart';

    public function __construct($title, $type, $y_axis, $x_axis = NULL)
    {
        $this->y_axis = $y_axis;
        $this->title = $title;
        $this->type = $type;

        if ($type == self::LINE) {
            $this->setMonthlyDataSet();
        } else if ($type == self::BAR) {
            $this->setAnnualDataSet();
        } else if ($type == self::PIE) {
            $this->x_axis = $x_axis;
        }
    }

    public function add($index, $value)
    {
        $this->dataSet[$this->y_axis][$index] = $value;
    }

    public function addX($index, $value)
    {
        // data[rentals][mese] = numero di noleggi
        $this->dataSet[$this->x_axis][$index] = $value;
    }

    public function plot()
    {
        return json_encode($this->dataSet);
    }

    private function setMonthlyDataSet()
    {
        for ($i = 0; $i < date("t"); $i++) {
            $this->dataSet[$this->y_axis][$i] =  0;
            $this->dataSet['days'][$i] =  $i + 1;
        }
    }

    private function setAnnualDataSet()
    {
        $months = [
            "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno",
            "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"
        ];

        for ($i = 0; $i < 12; $i++) {
            $this->dataSet[$this->y_axis][$i] =  0;
            $this->dataSet['months'][$i] =  $months[$i];
        }
    }

    public function getTitle()
    {
        return $this->title;
    }
    public function getType()
    {
        return $this->type;
    }

    public function setTitle($title){
        $this->title = $title;
    }
}
