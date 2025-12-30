<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251230213336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09CFFE9AD6');
        $this->addSql('DROP INDEX IDX_52EA1F09CFFE9AD6 ON order_item');
        $this->addSql('ALTER TABLE order_item DROP orders_id');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `orders` (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('ALTER TABLE orders ADD customer_name VARCHAR(255) NOT NULL, ADD customer_email VARCHAR(255) NOT NULL, ADD customer_phone VARCHAR(50) DEFAULT NULL, ADD shipping_address LONGTEXT NOT NULL, ADD total_amount NUMERIC(10, 2) NOT NULL, DROP total, CHANGE user_id user_id INT DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DCFFE9AD6');
        $this->addSql('DROP INDEX UNIQ_6D28840DCFFE9AD6 ON payment');
        $this->addSql('ALTER TABLE payment ADD method VARCHAR(50) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP orders_id, CHANGE stripe_session_id transaction_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D8D9F6D38 FOREIGN KEY (order_id) REFERENCES `orders` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D8D9F6D38 ON payment (order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `orders` ADD total DOUBLE PRECISION NOT NULL, DROP customer_name, DROP customer_email, DROP customer_phone, DROP shipping_address, DROP total_amount, CHANGE user_id user_id INT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('DROP INDEX IDX_52EA1F098D9F6D38 ON order_item');
        $this->addSql('ALTER TABLE order_item ADD orders_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09CFFE9AD6 ON order_item (orders_id)');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D8D9F6D38');
        $this->addSql('DROP INDEX UNIQ_6D28840D8D9F6D38 ON payment');
        $this->addSql('ALTER TABLE payment ADD orders_id INT DEFAULT NULL, DROP method, DROP created_at, CHANGE transaction_id stripe_session_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DCFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840DCFFE9AD6 ON payment (orders_id)');
    }
}
