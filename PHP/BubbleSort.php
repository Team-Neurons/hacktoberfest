<!-- A Simple - bubble sort -->
<!-- A bubble sort, a sorting algorithm that continuously steps through a list, swapping items until they appear in the correct order. -->

<?php
        function  bubblesort($a1,$a2) {
                for($i  =  sizeof($a1);  $i  >=  1;  $i--) {
                        for($j  =  1;  $j  <=  $i;  $j++) {
                                if($a1[$j-1]  >  $a1[$j]) {
                                        $t  =  $a1[$j-1];
                                        $t2  =  $a2[$j-1];
                                        $a1[$j-1]  =  $a1[$j];
                                        $a2[$j-1]  =  $a2[$j];
                                        $a1[$j]  =  $t;
                                        $a2[$j]  =  $t2;
                                }
                        }
                }
        }
?>
