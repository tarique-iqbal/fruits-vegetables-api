<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022082212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fruit CHANGE date_time_added created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE vegetable CHANGE date_time_added created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fruit CHANGE created_at date_time_added DATETIME NOT NULL');
        $this->addSql('ALTER TABLE vegetable CHANGE created_at date_time_added DATETIME NOT NULL');
    }
}
