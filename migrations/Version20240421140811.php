<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421140811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FF59027487');
        $this->addSql('ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FF59027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FF59027487');
        $this->addSql('ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FF59027487 FOREIGN KEY (theme_id) REFERENCES programme (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
