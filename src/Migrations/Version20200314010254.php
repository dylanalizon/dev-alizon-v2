<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200314010254 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE timeline_item ADD image_id INT DEFAULT NULL, DROP image, DROP updated_image_at');
        $this->addSql('ALTER TABLE timeline_item ADD CONSTRAINT FK_1E13D06B3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_1E13D06B3DA5256D ON timeline_item (image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE timeline_item DROP FOREIGN KEY FK_1E13D06B3DA5256D');
        $this->addSql('DROP INDEX IDX_1E13D06B3DA5256D ON timeline_item');
        $this->addSql('ALTER TABLE timeline_item ADD image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD updated_image_at DATETIME DEFAULT NULL, DROP image_id');
    }
}
