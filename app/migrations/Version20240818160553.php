<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240818160553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('DROP INDEX IDX_52EA1F098D9F6D38 ON order_item');
        $this->addSql('ALTER TABLE order_item ADD order_obj_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09A5862391 FOREIGN KEY (order_obj_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09A5862391 ON order_item (order_obj_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09A5862391');
        $this->addSql('DROP INDEX IDX_52EA1F09A5862391 ON order_item');
        $this->addSql('ALTER TABLE order_item DROP order_obj_id');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
    }
}
