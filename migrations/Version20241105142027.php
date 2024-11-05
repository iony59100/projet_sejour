<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105142027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre ADD service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FFED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_C509E4FFED5CA9E6 ON chambre (service_id)');
        $this->addSql('ALTER TABLE lit ADD chambre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lit ADD CONSTRAINT FK_5DDB8E9D9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('CREATE INDEX IDX_5DDB8E9D9B177F54 ON lit (chambre_id)');
        $this->addSql('ALTER TABLE personnel ADD service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DEED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_A6BCF3DEED5CA9E6 ON personnel (service_id)');
        $this->addSql('ALTER TABLE sejour ADD patient_id INT DEFAULT NULL, ADD lit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sejour ADD CONSTRAINT FK_96F520286B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE sejour ADD CONSTRAINT FK_96F52028278B5057 FOREIGN KEY (lit_id) REFERENCES lit (id)');
        $this->addSql('CREATE INDEX IDX_96F520286B899279 ON sejour (patient_id)');
        $this->addSql('CREATE INDEX IDX_96F52028278B5057 ON sejour (lit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FFED5CA9E6');
        $this->addSql('DROP INDEX IDX_C509E4FFED5CA9E6 ON chambre');
        $this->addSql('ALTER TABLE chambre DROP service_id');
        $this->addSql('ALTER TABLE lit DROP FOREIGN KEY FK_5DDB8E9D9B177F54');
        $this->addSql('DROP INDEX IDX_5DDB8E9D9B177F54 ON lit');
        $this->addSql('ALTER TABLE lit DROP chambre_id');
        $this->addSql('ALTER TABLE personnel DROP FOREIGN KEY FK_A6BCF3DEED5CA9E6');
        $this->addSql('DROP INDEX IDX_A6BCF3DEED5CA9E6 ON personnel');
        $this->addSql('ALTER TABLE personnel DROP service_id');
        $this->addSql('ALTER TABLE sejour DROP FOREIGN KEY FK_96F520286B899279');
        $this->addSql('ALTER TABLE sejour DROP FOREIGN KEY FK_96F52028278B5057');
        $this->addSql('DROP INDEX IDX_96F520286B899279 ON sejour');
        $this->addSql('DROP INDEX IDX_96F52028278B5057 ON sejour');
        $this->addSql('ALTER TABLE sejour DROP patient_id, DROP lit_id');
    }
}
