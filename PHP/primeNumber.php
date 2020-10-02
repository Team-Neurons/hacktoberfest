<?php

function prime(int $integer) {

    $cont = 0;
    $divisors = array();

    for($i = 1; $i <= $integer; $i++) {

        if($integer % $i == 0){
            $cont++;
            
            if ($i != 1 && $i != $integer) {
                array_push($divisors, $i);
            }
        }
    }
    
    if($cont == 2) {

        return "{$integer} is prime";

    } else {

        return $divisors;
    }
}

//another way

function divisors($integer) {
    $divisors = [];

    for($i = 2; $i < $integer; $i++) {

        if(!($integer % $i))
        $divisors[] = $i;
    }

    return $divisors ?: $integer . ' is prime';
}
