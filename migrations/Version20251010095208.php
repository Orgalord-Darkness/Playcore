<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251010095208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_video_game DROP FOREIGN KEY FK_2A23C2E12469DE2');
        $this->addSql('ALTER TABLE category_video_game DROP FOREIGN KEY FK_2A23C2E16230A8');
        $this->addSql('DROP TABLE category_video_game');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_video_game (category_id INT NOT NULL, video_game_id INT NOT NULL, INDEX IDX_2A23C2E12469DE2 (category_id), INDEX IDX_2A23C2E16230A8 (video_game_id), PRIMARY KEY(category_id, video_game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE category_video_game ADD CONSTRAINT FK_2A23C2E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_video_game ADD CONSTRAINT FK_2A23C2E16230A8 FOREIGN KEY (video_game_id) REFERENCES video_game (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
