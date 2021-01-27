<?php


class StatisticMonth
{
    var $year;
    var $months;
    var $count;

    public function __construct(int $year, array $months)
    {
        $this->year = $year;
        $this->months = $months;
        $sumOfYear = 0;
        foreach ($months as $name => $item){
            $sumOfYear = $sumOfYear + (int)$item['count'];
        }
        $this->count = $sumOfYear;
    }
}


