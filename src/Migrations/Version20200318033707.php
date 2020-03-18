<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318033707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE school ADD pictures_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABBBC415685 FOREIGN KEY (pictures_id) REFERENCES pictures (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F99EDABBBC415685 ON school (pictures_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE school DROP FOREIGN KEY FK_F99EDABBBC415685');
        $this->addSql('DROP INDEX UNIQ_F99EDABBBC415685 ON school');
        $this->addSql('ALTER TABLE school DROP pictures_id');
    }
}
