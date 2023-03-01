<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226143429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thread_message_metadata DROP FOREIGN KEY FK_31A5ABB3E2904019');
        $this->addSql('DROP INDEX IDX_31A5ABB3E2904019 ON thread_message_metadata');
        $this->addSql('ALTER TABLE thread_message_metadata DROP thread_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thread_message_metadata ADD thread_id INT NOT NULL');
        $this->addSql('ALTER TABLE thread_message_metadata ADD CONSTRAINT FK_31A5ABB3E2904019 FOREIGN KEY (thread_id) REFERENCES thread (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_31A5ABB3E2904019 ON thread_message_metadata (thread_id)');
    }
}
