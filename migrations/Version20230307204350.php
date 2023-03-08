<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307204350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trader ADD roles JSON NOT NULL, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C8A621B3E7927C74 ON trader (email)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, DROP firstame, DROP lastname, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C8A621B3E7927C74 ON trader');
        $this->addSql('ALTER TABLE trader DROP roles, CHANGE email email VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD firstame VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, DROP roles, DROP first_name, DROP last_name, CHANGE email email VARCHAR(255) NOT NULL');
    }
}
