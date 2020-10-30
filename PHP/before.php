<?php

/**
 * Get the portion of a string before the first occurrence of a given value.
 *
 * @param  string  $subject
 * @param  string  $search
 * @return string
 */
function before($subject, $search)
{
    return $search === '' ? $subject : explode($search, $subject)[0];
}
