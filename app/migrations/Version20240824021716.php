<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240824021716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders ADD delivery_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEEBF23851 FOREIGN KEY (delivery_address_id) REFERENCES delivery_addresses (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEEBF23851 ON orders (delivery_address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEEBF23851');
        $this->addSql('DROP INDEX IDX_E52FFDEEEBF23851 ON orders');
        $this->addSql('ALTER TABLE orders DROP delivery_address_id');
    }
}
