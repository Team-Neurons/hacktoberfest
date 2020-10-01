<?php
require_once 'libs/Request.php';
require_once 'libs/Kernel.php';

$Kernel = new Kernel();
$Kernel->handle(Request::create_from_globals());
