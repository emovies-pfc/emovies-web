<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130311204414 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql(<<<SQL
DELETE r FROM rating r
INNER JOIN fos_user u ON r.user_id = u.id
INNER JOIN movie m ON r.movie_id = m.id
WHERE u.movielensId IS NOT NULL
  OR m.movielensId IS NOT NULL
SQL
        );
        $this->addSql("DELETE FROM fos_user WHERE movielensId IS NOT NULL");
        $this->addSql("DELETE FROM movie WHERE movielensId IS NOT NULL");
        $this->addSql("DROP INDEX UNIQ_1D5EF26F8D76678B ON movie");
        $this->addSql("ALTER TABLE movie DROP movielensId");
        $this->addSql("DROP INDEX UNIQ_957A64798D76678B ON fos_user");
        $this->addSql("ALTER TABLE fos_user DROP movielensId");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE fos_user ADD movielensId INT DEFAULT NULL");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_957A64798D76678B ON fos_user (movielensId)");
        $this->addSql("ALTER TABLE movie ADD movielensId INT DEFAULT NULL");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1D5EF26F8D76678B ON movie (movielensId)");
    }
}
