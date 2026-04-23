<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260423101317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(50) NOT NULL, line1 VARCHAR(255) NOT NULL, line2 VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(20) NOT NULL, is_default_shipping TINYINT NOT NULL, is_default_billing TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, owner_id INT DEFAULT NULL, INDEX IDX_D4E6F817E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, unit_price INT NOT NULL, created_at DATETIME NOT NULL, cart_id INT NOT NULL, INDEX IDX_F0FE25271AD5CDBF (cart_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(250) NOT NULL, parent_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_64C19C15E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE coupon (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, value INT NOT NULL, usage_limit INT DEFAULT NULL, used_count INT NOT NULL, starts_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, active TINYINT NOT NULL, conditions JSON DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_CODE (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE inventory_movement (id INT AUTO_INCREMENT NOT NULL, `change` INT NOT NULL, reason VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, reference VARCHAR(255) DEFAULT NULL, product_id INT NOT NULL, INDEX IDX_40972F664584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, order_number VARCHAR(250) NOT NULL, status VARCHAR(255) NOT NULL, subtotal INT NOT NULL, shipping INT NOT NULL, tax INT NOT NULL, total INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, shipping_address_snapshot JSON NOT NULL, billing_address_snapshot JSON NOT NULL, owner_id INT NOT NULL, payment_id INT DEFAULT NULL, INDEX IDX_F52993987E3C61F9 (owner_id), UNIQUE INDEX UNIQ_F52993984C3A3BB (payment_id), UNIQUE INDEX UNIQ_IDENTIFIER_ORDER_NUMBER (order_number), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(250) NOT NULL, quantity INT NOT NULL, unit_price INT NOT NULL, total_price INT NOT NULL, product_id INT DEFAULT NULL, product_name VARCHAR(255) NOT NULL, order_owner_id INT NOT NULL, INDEX IDX_52EA1F099FC48348 (order_owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, provider VARCHAR(50) NOT NULL, provider_payment_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, amount INT NOT NULL, paid_at DATETIME DEFAULT NULL, metadata JSON DEFAULT NULL, raw_response JSON DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(100) NOT NULL, name VARCHAR(250) NOT NULL, slug VARCHAR(250) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, stock_quantity INT NOT NULL, in_stock TINYINT NOT NULL, featured TINYINT NOT NULL, attributes JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, category_id INT NOT NULL, INDEX IDX_D34A04AD5E237E06 (name), INDEX IDX_D34A04AD12469DE2 (category_id), UNIQUE INDEX UNIQ_IDENTIFIER_SKU (sku), UNIQUE INDEX UNIQ_IDENTIFIER_SLUG (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE product_image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(255) DEFAULT NULL, position INT NOT NULL, mime_type VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, product_id INT DEFAULT NULL, INDEX IDX_64617F034584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, is_verified TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, last_login_at DATETIME DEFAULT NULL, cart_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D6491AD5CDBF (cart_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F817E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE inventory_movement ADD CONSTRAINT FK_40972F664584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F099FC48348 FOREIGN KEY (order_owner_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6491AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F817E3C61F9');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE inventory_movement DROP FOREIGN KEY FK_40972F664584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987E3C61F9');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984C3A3BB');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F099FC48348');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product_image DROP FOREIGN KEY FK_64617F034584665A');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6491AD5CDBF');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE coupon');
        $this->addSql('DROP TABLE inventory_movement');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
