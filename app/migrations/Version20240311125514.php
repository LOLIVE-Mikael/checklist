<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311125514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE checklists_taches ADD CONSTRAINT FK_7F9DF8AF72E666 FOREIGN KEY (checklists_id) REFERENCES checklists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE checklists_taches ADD CONSTRAINT FK_7F9DF8B8A61670 FOREIGN KEY (taches_id) REFERENCES taches (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE checklists_taches DROP FOREIGN KEY FK_7F9DF8AF72E666');
        $this->addSql('ALTER TABLE checklists_taches DROP FOREIGN KEY FK_7F9DF8B8A61670');
    }
}
