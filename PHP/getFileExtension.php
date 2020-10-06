<?php
function getExt( $path ) { 
    return pathinfo( $path, PATHINFO_EXTENSION );
}
//sample printout
print "test.jpg:      ".getExtension( "test.jpeg" )."<br />\n";
print "test.file.test:   ".getExtension( "test.file.test" )."<br />\n";
print "his/is/a/cpp: ".getExtension( "this/is/a/cpp" )."<br />\n";
?>
