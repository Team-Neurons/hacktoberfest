<?php

function getFib($n) {
    $fib_array = [0, 1];
    for ($i = 2; $i < $n; $i++) {
        $fib_array[$i] = $fib_array[$i - 1] + $fib_array[$i - 2];
    }
    return $fib_array;
}

echo "<pre>";
print_r(getFib(50));
