<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511170003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contest (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, title VARCHAR(255) NOT NULL, duration INT NOT NULL, is_published TINYINT(1) NOT NULL, start_time TIME NOT NULL, start_date DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1A95CB561220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contest_user (contest_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_46F8C6821CD0F0DE (contest_id), INDEX IDX_46F8C682A76ED395 (user_id), PRIMARY KEY(contest_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem (id INT AUTO_INCREMENT NOT NULL, contest_id INT NOT NULL, letter VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, statement LONGTEXT NOT NULL, input_spec LONGTEXT NOT NULL, output_spec LONGTEXT NOT NULL, validator LONGTEXT NOT NULL, solution LONGTEXT DEFAULT NULL, proof LONGTEXT DEFAULT NULL, points INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D7E7CCC81CD0F0DE (contest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem_tag (problem_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_6C2625E8A0DCED86 (problem_id), INDEX IDX_6C2625E8BAD26311 (tag_id), PRIMARY KEY(problem_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sample_input (id INT AUTO_INCREMENT NOT NULL, problem_id INT NOT NULL, input LONGTEXT NOT NULL, output LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D0F939D8A0DCED86 (problem_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE submission (id INT AUTO_INCREMENT NOT NULL, problem_id INT NOT NULL, user_id INT NOT NULL, code LONGTEXT NOT NULL, language VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DB055AF3A0DCED86 (problem_id), INDEX IDX_DB055AF3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contest ADD CONSTRAINT FK_1A95CB561220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contest_user ADD CONSTRAINT FK_46F8C6821CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contest_user ADD CONSTRAINT FK_46F8C682A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC81CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id)');
        $this->addSql('ALTER TABLE problem_tag ADD CONSTRAINT FK_6C2625E8A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_tag ADD CONSTRAINT FK_6C2625E8BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sample_input ADD CONSTRAINT FK_D0F939D8A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contest_user DROP FOREIGN KEY FK_46F8C6821CD0F0DE');
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC81CD0F0DE');
        $this->addSql('ALTER TABLE problem_tag DROP FOREIGN KEY FK_6C2625E8A0DCED86');
        $this->addSql('ALTER TABLE sample_input DROP FOREIGN KEY FK_D0F939D8A0DCED86');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3A0DCED86');
        $this->addSql('ALTER TABLE problem_tag DROP FOREIGN KEY FK_6C2625E8BAD26311');
        $this->addSql('ALTER TABLE contest DROP FOREIGN KEY FK_1A95CB561220EA6');
        $this->addSql('ALTER TABLE contest_user DROP FOREIGN KEY FK_46F8C682A76ED395');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3A76ED395');
        $this->addSql('DROP TABLE contest');
        $this->addSql('DROP TABLE contest_user');
        $this->addSql('DROP TABLE problem');
        $this->addSql('DROP TABLE problem_tag');
        $this->addSql('DROP TABLE sample_input');
        $this->addSql('DROP TABLE submission');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE user');
    }
}
