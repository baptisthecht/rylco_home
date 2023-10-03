<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928215558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component ADD CONSTRAINT FK_49FEA1574FF1E962 FOREIGN KEY (design_system_id) REFERENCES design_system (id)');
        $this->addSql('CREATE INDEX IDX_49FEA1574FF1E962 ON component (design_system_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component DROP FOREIGN KEY FK_49FEA1574FF1E962');
        $this->addSql('DROP INDEX IDX_49FEA1574FF1E962 ON component');
    }
}
