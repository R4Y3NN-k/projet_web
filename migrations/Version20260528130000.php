<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add profile photo and bio to provider
 */
final class Version20260528130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add profile photo and bio fields to provider';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE provider ADD profile_photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE provider ADD bio LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE provider DROP profile_photo');
        $this->addSql('ALTER TABLE provider DROP bio');
    }
}
