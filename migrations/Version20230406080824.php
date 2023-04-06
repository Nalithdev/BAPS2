<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406080824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commerce ADD COLUMN adresse VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__commerce AS SELECT id, name, description FROM commerce');
        $this->addSql('DROP TABLE commerce');
        $this->addSql('CREATE TABLE commerce (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO commerce (id, name, description) SELECT id, name, description FROM __temp__commerce');
        $this->addSql('DROP TABLE __temp__commerce');
    }
}
