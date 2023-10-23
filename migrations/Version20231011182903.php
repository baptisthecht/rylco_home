<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011182903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D399395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_95DB9D399395C3F3 ON deposit (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D399395C3F3');
        $this->addSql('DROP INDEX IDX_95DB9D399395C3F3 ON deposit');
    }
}
