<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421133232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson ADD programme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F362BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id)');
        $this->addSql('CREATE INDEX IDX_F87474F362BB7AEE ON lesson (programme_id)');
        $this->addSql('ALTER TABLE programme ADD theme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FF59027487 FOREIGN KEY (theme_id) REFERENCES programme (id)');
        $this->addSql('CREATE INDEX IDX_3DDCB9FF59027487 ON programme (theme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F362BB7AEE');
        $this->addSql('DROP INDEX IDX_F87474F362BB7AEE ON lesson');
        $this->addSql('ALTER TABLE lesson DROP programme_id');
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FF59027487');
        $this->addSql('DROP INDEX IDX_3DDCB9FF59027487 ON programme');
        $this->addSql('ALTER TABLE programme DROP theme_id');
    }
}
