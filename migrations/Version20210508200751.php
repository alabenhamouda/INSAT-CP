<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210508200751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE problem_tag (problem_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_6C2625E8A0DCED86 (problem_id), INDEX IDX_6C2625E8BAD26311 (tag_id), PRIMARY KEY(problem_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE problem_tag ADD CONSTRAINT FK_6C2625E8A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_tag ADD CONSTRAINT FK_6C2625E8BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem ADD contest_source_id INT NOT NULL');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC8376CB870 FOREIGN KEY (contest_source_id) REFERENCES contest (id)');
        $this->addSql('CREATE INDEX IDX_D7E7CCC8376CB870 ON problem (contest_source_id)');
        $this->addSql('ALTER TABLE sample_input ADD problem_id INT NOT NULL');
        $this->addSql('ALTER TABLE sample_input ADD CONSTRAINT FK_D0F939D8A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id)');
        $this->addSql('CREATE INDEX IDX_D0F939D8A0DCED86 ON sample_input (problem_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE problem_tag');
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC8376CB870');
        $this->addSql('DROP INDEX IDX_D7E7CCC8376CB870 ON problem');
        $this->addSql('ALTER TABLE problem DROP contest_source_id');
        $this->addSql('ALTER TABLE sample_input DROP FOREIGN KEY FK_D0F939D8A0DCED86');
        $this->addSql('DROP INDEX IDX_D0F939D8A0DCED86 ON sample_input');
        $this->addSql('ALTER TABLE sample_input DROP problem_id');
    }
}
