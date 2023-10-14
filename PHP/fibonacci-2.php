<?php

function fibonacci($n)
{

    $a = 0;
    $b = 1;
    $counter = 0;

    while ($counter < $n) {
        echo ' ' . $a;
        $c = $a + $b;
        $a = $b;
        $b = $c;

        $counter = $counter + 1;
    }
}

fibonacci(10);
