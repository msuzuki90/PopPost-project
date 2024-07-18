<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718111945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, author_id INT NOT NULL, text VARCHAR(500) NOT NULL, created DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, message VARCHAR(510) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit_card (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, last4 INT DEFAULT NULL, expiration_month INT DEFAULT NULL, expiration_year INT DEFAULT NULL, brand VARCHAR(255) DEFAULT NULL, INDEX IDX_11D627EEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE micro_post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, text VARCHAR(500) NOT NULL, created DATETIME NOT NULL, picture VARCHAR(255) DEFAULT NULL, INDEX IDX_2AEFE017F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE micro_post_user (micro_post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_19DCF74D11E37CEA (micro_post_id), INDEX IDX_19DCF74DA76ED395 (user_id), PRIMARY KEY(micro_post_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date DATE NOT NULL, total VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, pdf TINYINT(1) NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, id_order_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_845CA2C1DD4481AD (id_order_id), INDEX IDX_845CA2C14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, session_id VARCHAR(255) NOT NULL, payment_status VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, price INT NOT NULL, invoice VARCHAR(255) DEFAULT NULL, customer_stripe_id VARCHAR(255) DEFAULT NULL, subscription_id VARCHAR(255) DEFAULT NULL, payment_method_id VARCHAR(255) DEFAULT NULL, success_page_expired TINYINT(1) NOT NULL, next_payment DATE DEFAULT NULL, INDEX IDX_6D28840DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, picture VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price_id_stripe VARCHAR(255) NOT NULL, stock INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, ban_hammer DATETIME DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Followers (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_6F97F043AD8644E (user_source), INDEX IDX_6F97F04233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_profile (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, bio VARCHAR(1024) DEFAULT NULL, website_url VARCHAR(255) DEFAULT NULL, twitter_username VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D95AB405A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES micro_post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE credit_card ADD CONSTRAINT FK_11D627EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE micro_post ADD CONSTRAINT FK_2AEFE017F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE micro_post_user ADD CONSTRAINT FK_19DCF74D11E37CEA FOREIGN KEY (micro_post_id) REFERENCES micro_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE micro_post_user ADD CONSTRAINT FK_19DCF74DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C1DD4481AD FOREIGN KEY (id_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE Followers ADD CONSTRAINT FK_6F97F043AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Followers ADD CONSTRAINT FK_6F97F04233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE credit_card DROP FOREIGN KEY FK_11D627EEA76ED395');
        $this->addSql('ALTER TABLE micro_post DROP FOREIGN KEY FK_2AEFE017F675F31B');
        $this->addSql('ALTER TABLE micro_post_user DROP FOREIGN KEY FK_19DCF74D11E37CEA');
        $this->addSql('ALTER TABLE micro_post_user DROP FOREIGN KEY FK_19DCF74DA76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C1DD4481AD');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C14584665A');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE Followers DROP FOREIGN KEY FK_6F97F043AD8644E');
        $this->addSql('ALTER TABLE Followers DROP FOREIGN KEY FK_6F97F04233D34C1');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405A76ED395');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE credit_card');
        $this->addSql('DROP TABLE micro_post');
        $this->addSql('DROP TABLE micro_post_user');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE Followers');
        $this->addSql('DROP TABLE user_profile');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
