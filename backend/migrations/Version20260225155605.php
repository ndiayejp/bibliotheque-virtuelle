<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225155605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE books (title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, isbn VARCHAR(13) NOT NULL, description LONGTEXT DEFAULT NULL, cover_url VARCHAR(500) DEFAULT NULL, total_copies INT NOT NULL, available_copies INT NOT NULL, borrowed_by_user_id VARCHAR(255) DEFAULT NULL, borrowed_at DATETIME DEFAULT NULL, due_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4A1B2A92CC1CF4E6 (isbn), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (email VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, password_hash VARCHAR(255) NOT NULL, role VARCHAR(30) NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE books');
        $this->addSql('DROP TABLE users');
    }
}
