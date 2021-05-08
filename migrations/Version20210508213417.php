<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210508213417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contest (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, title VARCHAR(255) NOT NULL, start_timme DATE NOT NULL, duration INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1A95CB561220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem (id INT AUTO_INCREMENT NOT NULL, contest_source_id INT NOT NULL, letter VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, statement LONGTEXT NOT NULL, input_spec LONGTEXT NOT NULL, output_spec LONGTEXT NOT NULL, validator LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D7E7CCC8376CB870 (contest_source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem_tag (problem_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_6C2625E8A0DCED86 (problem_id), INDEX IDX_6C2625E8BAD26311 (tag_id), PRIMARY KEY(problem_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sample_input (id INT AUTO_INCREMENT NOT NULL, problem_id INT NOT NULL, input LONGTEXT NOT NULL, output LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D0F939D8A0DCED86 (problem_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contest ADD CONSTRAINT FK_1A95CB561220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC8376CB870 FOREIGN KEY (contest_source_id) REFERENCES contest (id)');
        $this->addSql('ALTER TABLE problem_tag ADD CONSTRAINT FK_6C2625E8A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_tag ADD CONSTRAINT FK_6C2625E8BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sample_input ADD CONSTRAINT FK_D0F939D8A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC8376CB870');
        $this->addSql('ALTER TABLE problem_tag DROP FOREIGN KEY FK_6C2625E8A0DCED86');
        $this->addSql('ALTER TABLE sample_input DROP FOREIGN KEY FK_D0F939D8A0DCED86');
        $this->addSql('ALTER TABLE problem_tag DROP FOREIGN KEY FK_6C2625E8BAD26311');
        $this->addSql('ALTER TABLE contest DROP FOREIGN KEY FK_1A95CB561220EA6');
        $this->addSql('DROP TABLE contest');
        $this->addSql('DROP TABLE problem');
        $this->addSql('DROP TABLE problem_tag');
        $this->addSql('DROP TABLE sample_input');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE users');
    }
}
