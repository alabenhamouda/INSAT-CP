<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210509040333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contest_users (contest_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_18FED5C11CD0F0DE (contest_id), INDEX IDX_18FED5C167B3B43D (users_id), PRIMARY KEY(contest_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contest_users ADD CONSTRAINT FK_18FED5C11CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contest_users ADD CONSTRAINT FK_18FED5C167B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contest_users');
    }
}
