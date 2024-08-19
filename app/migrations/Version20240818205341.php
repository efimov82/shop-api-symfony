<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240818205341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09A5862391');
        $this->addSql('DROP INDEX IDX_52EA1F09A5862391 ON order_item');
        $this->addSql('ALTER TABLE order_item CHANGE order_obj_id order_entity_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F093DA206A5 FOREIGN KEY (order_entity_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F093DA206A5 ON order_item (order_entity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F093DA206A5');
        $this->addSql('DROP INDEX IDX_52EA1F093DA206A5 ON order_item');
        $this->addSql('ALTER TABLE order_item CHANGE order_entity_id order_obj_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09A5862391 FOREIGN KEY (order_obj_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_52EA1F09A5862391 ON order_item (order_obj_id)');
    }
}
