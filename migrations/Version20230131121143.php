<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131121143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipt DROP INDEX UNIQ_5399B645B83297E7, ADD INDEX IDX_5399B645B83297E7 (reservation_id)');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552B5CA896');
        $this->addSql('DROP INDEX UNIQ_42C849552B5CA896 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP receipt_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipt DROP INDEX IDX_5399B645B83297E7, ADD UNIQUE INDEX UNIQ_5399B645B83297E7 (reservation_id)');
        $this->addSql('ALTER TABLE reservation ADD receipt_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C849552B5CA896 ON reservation (receipt_id)');
    }
}
