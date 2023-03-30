<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330133404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, product_id INTEGER DEFAULT NULL, quantity INTEGER NOT NULL, status VARCHAR(255) DEFAULT NULL, cdate DATETIME NOT NULL, CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C849554584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C849554584665A ON reservation (product_id)');
        $this->addSql('ALTER TABLE user ADD COLUMN loyalty_points INTEGER DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, commerce_id, email, roles, password, firstname, lastname, siren, approved FROM "user"');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, commerce_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, siren VARCHAR(255) DEFAULT NULL, approved BOOLEAN DEFAULT NULL, CONSTRAINT FK_8D93D649B09114B7 FOREIGN KEY (commerce_id) REFERENCES commerce (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "user" (id, commerce_id, email, roles, password, firstname, lastname, siren, approved) SELECT id, commerce_id, email, roles, password, firstname, lastname, siren, approved FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649B09114B7 ON "user" (commerce_id)');
    }
}
