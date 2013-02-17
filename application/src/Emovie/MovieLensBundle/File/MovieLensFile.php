<?php
namespace Emovie\MovieLensBundle\File;

use Emovie\MovieLensBundle\File\Exception\FileNotFoundException;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class MovieLensFile
{
    protected $file;

    public function __construct($filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException("File not found at \"$filePath\"");
        }

        $this->file = fopen($filePath, 'r');
    }

    public function getTotalRecords()
    {
        $previousPosition = ftell($this->file);
        rewind($this->file);

        $lines = 0;

        while(fgets($this->file)) {
            ++$lines;
        }

        fseek($this->file, $previousPosition);

        return $lines;
    }

    public function getNextRecord()
    {
        $line = fgets($this->file);

        if ($line) {
            return explode('::', $line);
        }

        return null;
    }
}
