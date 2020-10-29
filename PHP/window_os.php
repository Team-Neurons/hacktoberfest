<?php

 /**
 * Determine whether the current environment is Windows based.
 *
 * @return bool
 */
function windows_os()
{
    return PHP_OS_FAMILY === 'Windows';
}
