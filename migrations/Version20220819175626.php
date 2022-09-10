<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220819175626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annee (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, libelle VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, observations LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE annee_has_classe (id INT AUTO_INCREMENT NOT NULL, annee_id INT DEFAULT NULL, classe_id INT DEFAULT NULL, scolarite DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, observations LONGTEXT NOT NULL, INDEX IDX_9BDD4BB0543EC5F0 (annee_id), INDEX IDX_9BDD4BB08F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, parcours_id INT DEFAULT NULL, code VARCHAR(100) NOT NULL, libelle VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, observations LONGTEXT NOT NULL, INDEX IDX_8F87BF966E38C0DB (parcours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(100) NOT NULL, nom VARCHAR(100) NOT NULL, prenoms VARCHAR(100) NOT NULL, naissance DATE NOT NULL, genre VARCHAR(10) NOT NULL, description LONGTEXT DEFAULT NULL, observations LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parcours (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, libelle VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, observations LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scolarite (id INT AUTO_INCREMENT NOT NULL, eleve_id INT DEFAULT NULL, ahc_id INT DEFAULT NULL, scolarite_personne DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, observations LONGTEXT NOT NULL, INDEX IDX_276250ABA6CC7B2 (eleve_id), INDEX IDX_276250AB6BBB051F (ahc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE versement (id INT AUTO_INCREMENT NOT NULL, scolarite_id INT DEFAULT NULL, date_versement DATE NOT NULL, code VARCHAR(100) NOT NULL, libelle VARCHAR(100) NOT NULL, montant DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, observations LONGTEXT NOT NULL, INDEX IDX_716E9367AA6B2AB6 (scolarite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annee_has_classe ADD CONSTRAINT FK_9BDD4BB0543EC5F0 FOREIGN KEY (annee_id) REFERENCES annee (id)');
        $this->addSql('ALTER TABLE annee_has_classe ADD CONSTRAINT FK_9BDD4BB08F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF966E38C0DB FOREIGN KEY (parcours_id) REFERENCES parcours (id)');
        $this->addSql('ALTER TABLE scolarite ADD CONSTRAINT FK_276250ABA6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE scolarite ADD CONSTRAINT FK_276250AB6BBB051F FOREIGN KEY (ahc_id) REFERENCES annee_has_classe (id)');
        $this->addSql('ALTER TABLE versement ADD CONSTRAINT FK_716E9367AA6B2AB6 FOREIGN KEY (scolarite_id) REFERENCES scolarite (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annee_has_classe DROP FOREIGN KEY FK_9BDD4BB0543EC5F0');
        $this->addSql('ALTER TABLE annee_has_classe DROP FOREIGN KEY FK_9BDD4BB08F5EA509');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF966E38C0DB');
        $this->addSql('ALTER TABLE scolarite DROP FOREIGN KEY FK_276250ABA6CC7B2');
        $this->addSql('ALTER TABLE scolarite DROP FOREIGN KEY FK_276250AB6BBB051F');
        $this->addSql('ALTER TABLE versement DROP FOREIGN KEY FK_716E9367AA6B2AB6');
        $this->addSql('DROP TABLE annee');
        $this->addSql('DROP TABLE annee_has_classe');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE parcours');
        $this->addSql('DROP TABLE scolarite');
        $this->addSql('DROP TABLE versement');
    }
}
