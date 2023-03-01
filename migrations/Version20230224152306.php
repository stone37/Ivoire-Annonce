<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230224152306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert ADD category_id INT NOT NULL, ADD sub_category_id INT DEFAULT NULL, ADD enabled TINYINT(1) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C1F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_17FD46C112469DE2 ON alert (category_id)');
        $this->addSql('CREATE INDEX IDX_17FD46C1F7BFE87C ON alert (sub_category_id)');
        $this->addSql('ALTER TABLE thread_metadata ADD thread_id INT NOT NULL');
        $this->addSql('ALTER TABLE thread_metadata ADD CONSTRAINT FK_40A577C8E2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('CREATE INDEX IDX_40A577C8E2904019 ON thread_metadata (thread_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C112469DE2');
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C1F7BFE87C');
        $this->addSql('DROP INDEX IDX_17FD46C112469DE2 ON alert');
        $this->addSql('DROP INDEX IDX_17FD46C1F7BFE87C ON alert');
        $this->addSql('ALTER TABLE alert DROP category_id, DROP sub_category_id, DROP enabled, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE thread_metadata DROP FOREIGN KEY FK_40A577C8E2904019');
        $this->addSql('DROP INDEX IDX_40A577C8E2904019 ON thread_metadata');
        $this->addSql('ALTER TABLE thread_metadata DROP thread_id');
    }
}
