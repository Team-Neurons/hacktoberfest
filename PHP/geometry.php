<?php


function square_area(float $sidelength) : float {
    return $sidelength * 2;
}

function square_circumference(float $sidelength) : float {
    return $sidelength * 4;
}

function rectangle_area(float $sidelength_a, float $sidelength_b) : float {
    return $sidelength_a * $sidelength_b;
}

function rectangle_circumference(float $sidelength_a, float $sidelength_b) : float {
    return (2 * $sidelength_a) + (2 * $sidelength_b);
}

function circle_cirumference(float $radius) : float {
    return (2 * pi() * $radius);

}

function circle_area(float $radius) : float {
    return pi() * ($radius * $radius);
}

