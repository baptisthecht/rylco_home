<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929151838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, component_id INT NOT NULL, seller_id INT NOT NULL, buyer_id INT NOT NULL, transaction_date DATETIME NOT NULL, amount DOUBLE PRECISION DEFAULT NULL, INDEX IDX_AC74095AE2ABAFFF (component_id), INDEX IDX_AC74095A8DE820D9 (seller_id), INDEX IDX_AC74095A6C755722 (buyer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AE2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A8DE820D9 FOREIGN KEY (seller_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A6C755722 FOREIGN KEY (buyer_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AE2ABAFFF');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A8DE820D9');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A6C755722');
        $this->addSql('DROP TABLE activity');
    }
}
