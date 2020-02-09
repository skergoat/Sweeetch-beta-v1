<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200209104613 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE id_card (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) DEFAULT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resume (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) DEFAULT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, profile_id INT DEFAULT NULL, resume_id INT NOT NULL, id_card_id INT NOT NULL, student_card_id INT NOT NULL, proof_habitation_id INT NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, tel_number VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, driving_license TINYINT(1) DEFAULT NULL, disabled TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_B723AF33A76ED395 (user_id), UNIQUE INDEX UNIQ_B723AF33CCFA12B8 (profile_id), UNIQUE INDEX UNIQ_B723AF33D262AF09 (resume_id), UNIQUE INDEX UNIQ_B723AF3394513350 (id_card_id), UNIQUE INDEX UNIQ_B723AF33A7FA2FD8 (student_card_id), UNIQUE INDEX UNIQ_B723AF3360E93A41 (proof_habitation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) DEFAULT NULL, area VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, profile_id INT DEFAULT NULL, language_name VARCHAR(255) DEFAULT NULL, level VARCHAR(255) DEFAULT NULL, INDEX IDX_D4DB71B5CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proof_habitation (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE education (id INT AUTO_INCREMENT NOT NULL, profile_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, school VARCHAR(255) NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME DEFAULT NULL, current TINYINT(1) DEFAULT NULL, INDEX IDX_DB0A5ED2CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_card (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33D262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF3394513350 FOREIGN KEY (id_card_id) REFERENCES id_card (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33A7FA2FD8 FOREIGN KEY (student_card_id) REFERENCES student_card (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF3360E93A41 FOREIGN KEY (proof_habitation_id) REFERENCES proof_habitation (id)');
        $this->addSql('ALTER TABLE language ADD CONSTRAINT FK_D4DB71B5CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED2CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF3394513350');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33A76ED395');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33D262AF09');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33CCFA12B8');
        $this->addSql('ALTER TABLE language DROP FOREIGN KEY FK_D4DB71B5CCFA12B8');
        $this->addSql('ALTER TABLE education DROP FOREIGN KEY FK_DB0A5ED2CCFA12B8');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF3360E93A41');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33A7FA2FD8');
        $this->addSql('DROP TABLE id_card');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE resume');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE proof_habitation');
        $this->addSql('DROP TABLE education');
        $this->addSql('DROP TABLE student_card');
    }
}
