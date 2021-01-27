<?php


class Statistic
{
    var $category;
    var $year;

    public function __construct(stdClass $category, array $year)
    {
        $this->category = $category;
        $this->year = $year;
    }
}