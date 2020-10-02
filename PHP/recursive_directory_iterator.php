<?php

$numberOfFiles = 0;

function filesIn(string $path): \Generator { //the data returned must belong to the Generator class, as is 'yield'

    if (! is_dir($path)) {
        throw new \RuntimeException("{$path} is not a directory ");
    }

    $it = new \RecursiveDirectoryIterator($path);
    $it = new \RecursiveIteratorIterator($it);
    $it = new \RegexIterator($it, '/\.pdf$/', \RegexIterator::MATCH);

    //yield, unlike 'return', returns the value only when needed and doesn't try to keep the entire dataset in memory
    //this is useful if we iterate over a very large dataset, for example very large log files or very large arrays
    yield from $it;
}

$folder = "your path/folder here";

foreach (filesIn($folder) as $file) {
    
    $numberOfFiles++;
}

echo "Inside " .$folder. " were found " . $numberOfFiles . " files matching your criteria";