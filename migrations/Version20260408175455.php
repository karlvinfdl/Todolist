<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408175455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projects (id_projects INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, color VARCHAR(7) DEFAULT \'#3B82F6\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, id_users INT NOT NULL, INDEX IDX_5C93B3A4FA06E4D9 (id_users), PRIMARY KEY (id_projects)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tasks (id_tasks INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(20) DEFAULT \'a_faire\' NOT NULL, priority VARCHAR(10) DEFAULT \'moyenne\' NOT NULL, due_date DATE DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, id_projects INT NOT NULL, INDEX IDX_50586597C485F31 (id_projects), PRIMARY KEY (id_tasks)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id_users INT AUTO_INCREMENT NOT NULL, email VARCHAR(150) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id_users)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4FA06E4D9 FOREIGN KEY (id_users) REFERENCES users (id_users) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597C485F31 FOREIGN KEY (id_projects) REFERENCES projects (id_projects) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4FA06E4D9');
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_50586597C485F31');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
