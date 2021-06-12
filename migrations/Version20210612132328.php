<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210612132328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problem ADD input_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC836421AD6 FOREIGN KEY (input_id) REFERENCES input (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7E7CCC836421AD6 ON problem (input_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC836421AD6');
        $this->addSql('DROP INDEX UNIQ_D7E7CCC836421AD6 ON problem');
        $this->addSql('ALTER TABLE problem DROP input_id');
    }
}
