<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210613002030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE contest_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE input_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE problem_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sample_input_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE submission_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE contest (id INT NOT NULL, creator_id INT NOT NULL, title VARCHAR(255) NOT NULL, duration INT NOT NULL, is_published BOOLEAN NOT NULL, start_time TIME(0) WITHOUT TIME ZONE NOT NULL, start_date DATE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1A95CB561220EA6 ON contest (creator_id)');
        $this->addSql('CREATE TABLE contest_user (contest_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(contest_id, user_id))');
        $this->addSql('CREATE INDEX IDX_46F8C6821CD0F0DE ON contest_user (contest_id)');
        $this->addSql('CREATE INDEX IDX_46F8C682A76ED395 ON contest_user (user_id)');
        $this->addSql('CREATE TABLE input (id INT NOT NULL, input TEXT NOT NULL, output TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE problem (id INT NOT NULL, contest_id INT NOT NULL, input_id INT DEFAULT NULL, letter VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, statement TEXT NOT NULL, input_spec TEXT NOT NULL, output_spec TEXT NOT NULL, validator TEXT NOT NULL, solution TEXT DEFAULT NULL, proof TEXT DEFAULT NULL, points INT NOT NULL, time_limit DOUBLE PRECISION DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D7E7CCC81CD0F0DE ON problem (contest_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7E7CCC836421AD6 ON problem (input_id)');
        $this->addSql('CREATE TABLE problem_tag (problem_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(problem_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_6C2625E8A0DCED86 ON problem_tag (problem_id)');
        $this->addSql('CREATE INDEX IDX_6C2625E8BAD26311 ON problem_tag (tag_id)');
        $this->addSql('CREATE TABLE sample_input (id INT NOT NULL, problem_id INT NOT NULL, input TEXT NOT NULL, output TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D0F939D8A0DCED86 ON sample_input (problem_id)');
        $this->addSql('CREATE TABLE status (id INT NOT NULL, description VARCHAR(255) NOT NULL, code INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE submission (id INT NOT NULL, problem_id INT NOT NULL, user_id INT NOT NULL, status_id INT NOT NULL, code TEXT NOT NULL, language VARCHAR(255) NOT NULL, token VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB055AF3A0DCED86 ON submission (problem_id)');
        $this->addSql('CREATE INDEX IDX_DB055AF3A76ED395 ON submission (user_id)');
        $this->addSql('CREATE INDEX IDX_DB055AF36BF700BD ON submission (status_id)');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B7835E237E06 ON tag (name)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('ALTER TABLE contest ADD CONSTRAINT FK_1A95CB561220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contest_user ADD CONSTRAINT FK_46F8C6821CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contest_user ADD CONSTRAINT FK_46F8C682A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC81CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC836421AD6 FOREIGN KEY (input_id) REFERENCES input (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE problem_tag ADD CONSTRAINT FK_6C2625E8A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE problem_tag ADD CONSTRAINT FK_6C2625E8BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sample_input ADD CONSTRAINT FK_D0F939D8A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF36BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contest_user DROP CONSTRAINT FK_46F8C6821CD0F0DE');
        $this->addSql('ALTER TABLE problem DROP CONSTRAINT FK_D7E7CCC81CD0F0DE');
        $this->addSql('ALTER TABLE problem DROP CONSTRAINT FK_D7E7CCC836421AD6');
        $this->addSql('ALTER TABLE problem_tag DROP CONSTRAINT FK_6C2625E8A0DCED86');
        $this->addSql('ALTER TABLE sample_input DROP CONSTRAINT FK_D0F939D8A0DCED86');
        $this->addSql('ALTER TABLE submission DROP CONSTRAINT FK_DB055AF3A0DCED86');
        $this->addSql('ALTER TABLE submission DROP CONSTRAINT FK_DB055AF36BF700BD');
        $this->addSql('ALTER TABLE problem_tag DROP CONSTRAINT FK_6C2625E8BAD26311');
        $this->addSql('ALTER TABLE contest DROP CONSTRAINT FK_1A95CB561220EA6');
        $this->addSql('ALTER TABLE contest_user DROP CONSTRAINT FK_46F8C682A76ED395');
        $this->addSql('ALTER TABLE submission DROP CONSTRAINT FK_DB055AF3A76ED395');
        $this->addSql('DROP SEQUENCE contest_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE input_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE problem_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sample_input_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE submission_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE contest');
        $this->addSql('DROP TABLE contest_user');
        $this->addSql('DROP TABLE input');
        $this->addSql('DROP TABLE problem');
        $this->addSql('DROP TABLE problem_tag');
        $this->addSql('DROP TABLE sample_input');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE submission');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE users');
    }
}
