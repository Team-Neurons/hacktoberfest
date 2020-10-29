<?php

if(isset($_GET['date'])) {

    $date_string = htmlspecialchars($_GET['date']);

    $date = strtotime($date_string);

    $dayOfWeek = date("l", $date);

    echo $dayOfWeek;
}