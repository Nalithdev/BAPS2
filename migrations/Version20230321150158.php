<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321150158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feed DROP FOREIGN KEY FK_234044AB79F37AE5');
        $this->addSql('ALTER TABLE feed DROP FOREIGN KEY FK_234044ABA883D940');
        $this->addSql('DROP INDEX IDX_234044AB79F37AE5 ON feed');
        $this->addSql('DROP INDEX IDX_234044ABA883D940 ON feed');
        $this->addSql('ALTER TABLE feed ADD user_id INT DEFAULT NULL, DROP id_commerce_id, DROP id_user_id');
        $this->addSql('ALTER TABLE feed ADD CONSTRAINT FK_234044ABA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_234044ABA76ED395 ON feed (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feed DROP FOREIGN KEY FK_234044ABA76ED395');
        $this->addSql('DROP INDEX IDX_234044ABA76ED395 ON feed');
        $this->addSql('ALTER TABLE feed ADD id_user_id INT DEFAULT NULL, CHANGE user_id id_commerce_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD CONSTRAINT FK_234044AB79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE feed ADD CONSTRAINT FK_234044ABA883D940 FOREIGN KEY (id_commerce_id) REFERENCES commerce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_234044AB79F37AE5 ON feed (id_user_id)');
        $this->addSql('CREATE INDEX IDX_234044ABA883D940 ON feed (id_commerce_id)');
    }
}
