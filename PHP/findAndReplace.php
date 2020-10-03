<?php

class Inputter
{
    // File which includes the old input, which will be replaced
    const OLD_INPUT_FILE = 'old.txt';

    // File which includes the new input
    const INPUT_FILE = 'input.txt';

    // Only edit files, which path contains this pattern
    const SEARCH_PATTERN_FILE_EXTENSION = '/\.html/';

    const TRIM = true;

    /** @var string */
    private $searchDirectory;

    public function __construct($searchDirectory)
    {
        $this->searchDirectory = $searchDirectory;
    }

    public function run()
    {
        $filepaths = $this->getFilepaths();
        foreach ($filepaths as $filepath) {
            // Get file input
            $content = file_get_contents($filepath);

            // Replace input
            if (self::TRIM) {
                $content = str_replace(
                    trim($this->getOldInput()),
                    trim($this->getNewInput()),
                    $content
                );
            } else {
                $content = str_replace(
                    $this->getOldInput(),
                    $this->getNewInput(),
                    $content
                );
            }

            // Write new input to file
            file_put_contents($filepath, $content);
        }
    }

    /**
     * @return string
     */
    private function getOldInput(): string
    {
        return file_get_contents($this->getOldFilepath());
    }

    /**
     * @return string
     */
    private function getNewInput(): string
    {
        return file_get_contents($this->getInputFilepath());
    }

    /**
     * Get all valid filepaths from search directory
     *
     * @return array
     */
    private function getFilepaths(): array
    {
        $files = [];

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->searchDirectory));
        foreach ($rii as $file) {
            /** @var SplFileInfo#6 $file */
            if ($file->isDir()) {
                continue;
            }

            if (!preg_match(self::SEARCH_PATTERN_FILE_EXTENSION, $file->getFilename())) {
                continue;
            }

            $files[] = $file->getPathname();
        }

        return $files;
    }

    /**
     * @return string
     */
    private function getInputFilepath(): string
    {
        return sprintf('%s/%s', __DIR__, self::INPUT_FILE);
    }

    /**
     * @return string
     */
    private function getOldFilepath(): string
    {
        return sprintf('%s/%s', __DIR__, self::OLD_INPUT_FILE);
    }
}

$searchDirectory = $argv[1] ?? '';

if ($searchDirectory) {
    $inputter = new Inputter($searchDirectory);
    $inputter->run();
} else {
    echo "\nERROR: Please provide a path to the search directory.\n\n";
}

