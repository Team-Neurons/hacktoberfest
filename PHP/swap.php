<?php

/**
 * Swap 2 variables using php 7
 * @param  mixed &$a
 * @param  mixed &$b
 * @return void
 */
function swap(&$a, &$b)
{
	[$a, $b] = [$b, $a];
}
