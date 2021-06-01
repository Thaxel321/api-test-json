<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531122233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CF7D4A6F6');
        $this->addSql('DROP INDEX IDX_11BA68CF7D4A6F6 ON notes');
        $this->addSql('ALTER TABLE notes CHANGE elevesss_id eleves_id INT NOT NULL');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CC2140342 FOREIGN KEY (eleves_id) REFERENCES eleves (id)');
        $this->addSql('CREATE INDEX IDX_11BA68CC2140342 ON notes (eleves_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CC2140342');
        $this->addSql('DROP INDEX IDX_11BA68CC2140342 ON notes');
        $this->addSql('ALTER TABLE notes CHANGE eleves_id elevesss_id INT NOT NULL');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CF7D4A6F6 FOREIGN KEY (elevesss_id) REFERENCES eleves (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_11BA68CF7D4A6F6 ON notes (elevesss_id)');
    }
}
