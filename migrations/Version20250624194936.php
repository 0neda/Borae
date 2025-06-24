<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624194936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE evento (id INT AUTO_INCREMENT NOT NULL, criador_id INT NOT NULL, nome VARCHAR(255) NOT NULL, data_inicio DATE NOT NULL, hora_inicio TIME NOT NULL, INDEX IDX_47860B05355B1972 (criador_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evento ADD CONSTRAINT FK_47860B05355B1972 FOREIGN KEY (criador_id) REFERENCES usuario (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE evento DROP FOREIGN KEY FK_47860B05355B1972
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE evento
        SQL);
    }
}
