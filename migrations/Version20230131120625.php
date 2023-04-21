<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131120625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apartment_guest (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_day DATETIME NOT NULL, card_number VARCHAR(255) NOT NULL, card_type VARCHAR(50) NOT NULL, reservationItem_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_EB70C9F8E4AF4C20 (card_number), INDEX IDX_EB70C9F86C10C5DA (reservationItem_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, `lead` VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, published_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C0155143989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, zip VARCHAR(50) NOT NULL, city VARCHAR(50) NOT NULL, street_and_other VARCHAR(255) NOT NULL, phone VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, service_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_472B783AED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_translations (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_78AB76C9232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_item (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, user_id INT DEFAULT NULL, company_name VARCHAR(255) NOT NULL, company_priority SMALLINT NOT NULL, paid TINYINT(1) NOT NULL, type VARCHAR(50) NOT NULL, sum_price INT NOT NULL, INDEX IDX_3E98B26AB83297E7 (reservation_id), INDEX IDX_3E98B26AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation_item_payment_item (payment_item_id INT NOT NULL, reservation_item_id INT NOT NULL, INDEX IDX_12AE1B50B265CB4 (payment_item_id), INDEX IDX_12AE1B5075FAE9DB (reservation_item_id), PRIMARY KEY(payment_item_id, reservation_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, reservation_id INT DEFAULT NULL, transaction_id INT DEFAULT NULL, created_at DATETIME NOT NULL, identifier VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_5399B645772E836A (identifier), INDEX IDX_5399B645A76ED395 (user_id), UNIQUE INDEX UNIQ_5399B645B83297E7 (reservation_id), UNIQUE INDEX UNIQ_5399B6452FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, receipt_id INT DEFAULT NULL, locale VARCHAR(2) NOT NULL, reservation_status VARCHAR(50) NOT NULL, sum_price INT NOT NULL, created_at DATETIME NOT NULL, reservation_number VARCHAR(13) NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), UNIQUE INDEX UNIQ_42C849552B5CA896 (receipt_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation_item (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, service_id INT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, with_captain TINYINT(1) DEFAULT NULL, licence_number VARCHAR(50) DEFAULT NULL, reservation_paid_succesfully TINYINT(1) NOT NULL, reservation_price INT DEFAULT NULL, paid_assurance INT DEFAULT NULL, assurance_paid_succesfully TINYINT(1) DEFAULT NULL, INDEX IDX_922E876B83297E7 (reservation_id), INDEX IDX_922E876ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, gift_service_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, `lead` VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, cover_image VARCHAR(255) NOT NULL, cover_image_collection VARCHAR(255) NOT NULL, reservation_type VARCHAR(50) NOT NULL, avaible_same_time INT NOT NULL, min_day INT NOT NULL, gift_image VARCHAR(255) DEFAULT NULL, min_gift_day INT DEFAULT NULL, gift_text VARCHAR(255) DEFAULT NULL, deleted TINYINT(1) NOT NULL, service_type VARCHAR(50) NOT NULL, price INT DEFAULT NULL, company_name VARCHAR(255) NOT NULL, company_priority SMALLINT NOT NULL, beds INT DEFAULT NULL, extra_beds INT DEFAULT NULL, assurance INT DEFAULT NULL, cleaning_charge INT DEFAULT NULL, captain_type VARCHAR(50) DEFAULT NULL, captain_price INT DEFAULT NULL, half_day_price INT DEFAULT NULL, full_day_price INT DEFAULT NULL, INDEX IDX_E19D9AD240AD6E0D (gift_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE related_services (service_source INT NOT NULL, service_target INT NOT NULL, INDEX IDX_D5B1AFC4614D7A45 (service_source), INDEX IDX_D5B1AFC478A82ACA (service_target), PRIMARY KEY(service_source, service_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE condition_services (service_source INT NOT NULL, service_target INT NOT NULL, INDEX IDX_54743370614D7A45 (service_source), INDEX IDX_5474337078A82ACA (service_target), PRIMARY KEY(service_source, service_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_translations (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_191BAF62232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, payment_item_id INT DEFAULT NULL, transaction_id VARCHAR(32) DEFAULT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, type VARCHAR(50) NOT NULL, has_receipt TINYINT(1) NOT NULL, INDEX IDX_723705D1A76ED395 (user_id), INDEX IDX_723705D1B265CB4 (payment_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, newsletter TINYINT(1) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, invoice_address_name VARCHAR(255) DEFAULT NULL, invoice_address_zip VARCHAR(12) DEFAULT NULL, invoice_address_country VARCHAR(3) DEFAULT NULL, invoice_address_city VARCHAR(255) DEFAULT NULL, invoice_address_street_and_other VARCHAR(255) DEFAULT NULL, birth_day DATETIME DEFAULT NULL, password_avaible_until DATETIME NOT NULL, used_password TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apartment_guest ADD CONSTRAINT FK_EB70C9F86C10C5DA FOREIGN KEY (reservationItem_id) REFERENCES reservation_item (id)');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_translations ADD CONSTRAINT FK_78AB76C9232D562B FOREIGN KEY (object_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment_item ADD CONSTRAINT FK_3E98B26AB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE payment_item ADD CONSTRAINT FK_3E98B26AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation_item_payment_item ADD CONSTRAINT FK_12AE1B50B265CB4 FOREIGN KEY (payment_item_id) REFERENCES payment_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_item_payment_item ADD CONSTRAINT FK_12AE1B5075FAE9DB FOREIGN KEY (reservation_item_id) REFERENCES reservation_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B6452FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id)');
        $this->addSql('ALTER TABLE reservation_item ADD CONSTRAINT FK_922E876B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation_item ADD CONSTRAINT FK_922E876ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD240AD6E0D FOREIGN KEY (gift_service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE related_services ADD CONSTRAINT FK_D5B1AFC4614D7A45 FOREIGN KEY (service_source) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE related_services ADD CONSTRAINT FK_D5B1AFC478A82ACA FOREIGN KEY (service_target) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE condition_services ADD CONSTRAINT FK_54743370614D7A45 FOREIGN KEY (service_source) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE condition_services ADD CONSTRAINT FK_5474337078A82ACA FOREIGN KEY (service_target) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_translations ADD CONSTRAINT FK_191BAF62232D562B FOREIGN KEY (object_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1B265CB4 FOREIGN KEY (payment_item_id) REFERENCES payment_item (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment_guest DROP FOREIGN KEY FK_EB70C9F86C10C5DA');
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783AED5CA9E6');
        $this->addSql('ALTER TABLE page_translations DROP FOREIGN KEY FK_78AB76C9232D562B');
        $this->addSql('ALTER TABLE payment_item DROP FOREIGN KEY FK_3E98B26AB83297E7');
        $this->addSql('ALTER TABLE payment_item DROP FOREIGN KEY FK_3E98B26AA76ED395');
        $this->addSql('ALTER TABLE reservation_item_payment_item DROP FOREIGN KEY FK_12AE1B50B265CB4');
        $this->addSql('ALTER TABLE reservation_item_payment_item DROP FOREIGN KEY FK_12AE1B5075FAE9DB');
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B645A76ED395');
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B645B83297E7');
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B6452FC0CB0F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552B5CA896');
        $this->addSql('ALTER TABLE reservation_item DROP FOREIGN KEY FK_922E876B83297E7');
        $this->addSql('ALTER TABLE reservation_item DROP FOREIGN KEY FK_922E876ED5CA9E6');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD240AD6E0D');
        $this->addSql('ALTER TABLE related_services DROP FOREIGN KEY FK_D5B1AFC4614D7A45');
        $this->addSql('ALTER TABLE related_services DROP FOREIGN KEY FK_D5B1AFC478A82ACA');
        $this->addSql('ALTER TABLE condition_services DROP FOREIGN KEY FK_54743370614D7A45');
        $this->addSql('ALTER TABLE condition_services DROP FOREIGN KEY FK_5474337078A82ACA');
        $this->addSql('ALTER TABLE service_translations DROP FOREIGN KEY FK_191BAF62232D562B');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1A76ED395');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1B265CB4');
        $this->addSql('DROP TABLE apartment_guest');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE page_translations');
        $this->addSql('DROP TABLE payment_item');
        $this->addSql('DROP TABLE reservation_item_payment_item');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_item');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE related_services');
        $this->addSql('DROP TABLE condition_services');
        $this->addSql('DROP TABLE service_translations');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
