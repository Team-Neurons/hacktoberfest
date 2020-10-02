<?php


foreach (range(1, 100) as $number) {
    if(0 !== $number % 3 && 0 !== $number % 5) {
        echo $number.'<br>';
        continue;
    }

    if(0 === $number % 3) {
        echo 'Fizz';
    }

    if(0 === $number % 5) {
        echo 'Buzz';
    }

    echo '<br>';
}

