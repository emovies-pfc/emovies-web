<?php
namespace Emovie\MovieLensBundle\Importer;

use Emovie\MovieLensBundle\File\MovieLensFile;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
interface Importer
{
    /**
     * Imports all the MovieLens data from a file to the database.
     *
     * @param \Emovie\MovieLensBundle\File\MovieLensFile $file
     */
    public function importFromFile(MovieLensFile $file);
}
