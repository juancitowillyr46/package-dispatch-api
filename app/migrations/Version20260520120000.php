<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260520120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users, couriers, packages, dispatches and dispatch history tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');

        $this->addSql('CREATE TABLE couriers (id VARCHAR(36) NOT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN couriers.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN couriers.updated_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('CREATE TABLE shipment_packages (id VARCHAR(36) NOT NULL, tracking_code VARCHAR(50) NOT NULL, recipient_name VARCHAR(255) NOT NULL, recipient_address VARCHAR(500) NOT NULL, weight_kg DOUBLE PRECISION NOT NULL, description VARCHAR(500) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN shipment_packages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN shipment_packages.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3DE23FC6253AA25D ON shipment_packages (tracking_code)');

        $this->addSql('CREATE TABLE dispatches (id VARCHAR(36) NOT NULL, package_id VARCHAR(36) NOT NULL, courier_id VARCHAR(36) DEFAULT NULL, status VARCHAR(20) NOT NULL, reference_number VARCHAR(50) NOT NULL, origin_address VARCHAR(500) NOT NULL, destination_address VARCHAR(500) NOT NULL, notes VARCHAR(500) DEFAULT NULL, assigned_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, picked_up_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN dispatches.assigned_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN dispatches.picked_up_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN dispatches.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN dispatches.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN dispatches.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_74DA5A3AF44CABFF ON dispatches (package_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_74DA5A3A8BF1AE50 ON dispatches (reference_number)');
        $this->addSql('CREATE INDEX idx_dispatches_status_created_at ON dispatches (status, created_at)');
        $this->addSql('CREATE INDEX idx_dispatches_courier_status ON dispatches (courier_id, status)');

        $this->addSql('CREATE TABLE dispatch_history (id VARCHAR(36) NOT NULL, dispatch_id VARCHAR(36) NOT NULL, previous_status VARCHAR(20) DEFAULT NULL, new_status VARCHAR(20) NOT NULL, changed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN dispatch_history.changed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX idx_dispatch_history_dispatch_changed_at ON dispatch_history (dispatch_id, changed_at)');

        $this->addSql('ALTER TABLE dispatches ADD CONSTRAINT FK_dispatches_package FOREIGN KEY (package_id) REFERENCES shipment_packages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dispatches ADD CONSTRAINT FK_dispatches_courier FOREIGN KEY (courier_id) REFERENCES couriers (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dispatch_history ADD CONSTRAINT FK_dispatch_history_dispatch FOREIGN KEY (dispatch_id) REFERENCES dispatches (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE dispatch_history DROP CONSTRAINT FK_dispatch_history_dispatch');
        $this->addSql('ALTER TABLE dispatches DROP CONSTRAINT FK_dispatches_package');
        $this->addSql('ALTER TABLE dispatches DROP CONSTRAINT FK_dispatches_courier');
        $this->addSql('DROP TABLE dispatch_history');
        $this->addSql('DROP TABLE dispatches');
        $this->addSql('DROP TABLE shipment_packages');
        $this->addSql('DROP TABLE couriers');
        $this->addSql('DROP TABLE users');
    }
}
