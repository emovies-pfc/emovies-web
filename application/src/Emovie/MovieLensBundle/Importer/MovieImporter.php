<?php
namespace Emovie\MovieLensBundle\Importer;

use Doctrine\DBAL\Connection;
use Emovie\MovieLensBundle\File\MovieLensFile;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class MovieImporter implements Importer
{
    private $connection;
    private $callback;
    private $period;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function importFromFile(MovieLensFile $file)
    {
        $insertMovieQuery =
            $this->connection->prepare('INSERT IGNORE INTO movie(movielensId, name) VALUES (:movielens_id, :name)');

        $position = 0;

        while ($data = $file->getNextRecord()) {
            list($movielensId, $name, $tags) = $data;

            $insertMovieQuery->execute(array('movielens_id' => $movielensId, 'name' => $name));
            ++ $position;

            if ($this->callback && $position % $this->period === 0) {
                call_user_func($this->callback, $position);
            }
        }
    }

    public function setCallback(callable $callback, $period)
    {
        $this->callback = $callback;
        $this->period = $period;
    }
}
