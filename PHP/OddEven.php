<?php

class OddEven
{
    private $number;
    function __construct(int $number)
    {
        $this->number = $number;
    }

    public function isOdd()
    {
        return !($this->number % 2 == 0);
    }

    public function isEven()
    {
        return $this->number % 2 == 0;
    }
}

// Sample Printout
$number = 5;
$oddeven = new OddEven($number);
echo $oddeven->isOdd() ? "The number is odd" : "The number is even";
echo $oddeven->isEven() ? "The number is even" : "The number is odd";
