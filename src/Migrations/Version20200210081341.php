<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200210081341 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE offers_student');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE offers_student (offers_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_DED38F9BA090B42E (offers_id), INDEX IDX_DED38F9BCB944F1A (student_id), PRIMARY KEY(offers_id, student_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE offers_student ADD CONSTRAINT FK_DED38F9BA090B42E FOREIGN KEY (offers_id) REFERENCES offers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offers_student ADD CONSTRAINT FK_DED38F9BCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }
}
