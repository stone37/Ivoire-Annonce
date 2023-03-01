<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230208110313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE advert (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, sub_category_id INT DEFAULT NULL, location_id INT NOT NULL, owner_id INT NOT NULL, title VARCHAR(180) DEFAULT NULL, slug VARCHAR(180) DEFAULT NULL, description LONGTEXT DEFAULT NULL, price INT DEFAULT NULL, price_state TINYINT(1) DEFAULT NULL, type INT DEFAULT NULL, reference VARCHAR(100) DEFAULT NULL, marque VARCHAR(100) DEFAULT NULL, model VARCHAR(100) DEFAULT NULL, auto_year VARCHAR(10) DEFAULT NULL, auto_type VARCHAR(255) DEFAULT NULL, auto_state VARCHAR(255) DEFAULT NULL, kilometrage INT DEFAULT NULL, boite_vitesse VARCHAR(255) DEFAULT NULL, transmission VARCHAR(255) DEFAULT NULL, type_carburant VARCHAR(255) DEFAULT NULL, auto_color VARCHAR(255) DEFAULT NULL, nombre_porte VARCHAR(255) DEFAULT NULL, nombre_place VARCHAR(255) DEFAULT NULL, cylindre INT DEFAULT NULL, autre_information LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', surface INT DEFAULT NULL, nombre_piece VARCHAR(255) DEFAULT NULL, nombre_chambre INT DEFAULT NULL, nombre_salle_bain INT DEFAULT NULL, surface_balcon INT DEFAULT NULL, immobilier_state VARCHAR(255) DEFAULT NULL, date_construction VARCHAR(255) DEFAULT NULL, standing VARCHAR(255) DEFAULT NULL, cuisine VARCHAR(255) DEFAULT NULL, nombre_placard INT DEFAULT NULL, service_inclus LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', access LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', exterior LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', interior LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', proximite LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', state VARCHAR(255) DEFAULT NULL, brand VARCHAR(255) DEFAULT NULL, sex VARCHAR(255) DEFAULT NULL, denied_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, validated_at DATETIME DEFAULT NULL, option_advert_head_end_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', option_advert_urgent_end_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', option_advert_home_gallery_end_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', option_advert_featured_end_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_54F1F40B12469DE2 (category_id), INDEX IDX_54F1F40BF7BFE87C (sub_category_id), UNIQUE INDEX UNIQ_54F1F40B64D218E (location_id), INDEX IDX_54F1F40B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advert_picture (id INT AUTO_INCREMENT NOT NULL, advert_id INT NOT NULL, extension VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, principale TINYINT(1) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BC950FDFD07ECCB6 (advert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advert_read (id INT AUTO_INCREMENT NOT NULL, advert_id INT NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_D177002AD07ECCB6 (advert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE alert (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, INDEX IDX_17FD46C17E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ban (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, premiums_id INT DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, slug VARCHAR(100) NOT NULL, icon VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, permalink VARCHAR(255) NOT NULL, root_node INT DEFAULT NULL, left_node INT DEFAULT NULL, right_node INT DEFAULT NULL, level_depth INT DEFAULT NULL, position INT DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C1F286BC32 (permalink), INDEX IDX_64C19C1727ACA70 (parent_id), INDEX IDX_64C19C1FEEAF2D3 (premiums_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_premium (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, slug VARCHAR(100) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, discount_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, advert_id INT DEFAULT NULL, validated TINYINT(1) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, amount INT DEFAULT NULL, amount_total INT DEFAULT NULL, taxe_amount INT DEFAULT NULL, discount_amount INT DEFAULT NULL, payment_method VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6EEAA67D4C7C611F (discount_id), INDEX IDX_6EEAA67D7E3C61F9 (owner_id), INDEX IDX_6EEAA67DD07ECCB6 (advert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_request (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, discount INT DEFAULT NULL, code VARCHAR(100) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, utilisation INT DEFAULT NULL, utiliser INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_verification (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, email VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_FE22358F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailing (id INT AUTO_INCREMENT NOT NULL, destinataire VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, groupe INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange_rate (id INT AUTO_INCREMENT NOT NULL, source_currency_id INT NOT NULL, target_currency_id INT NOT NULL, ratio DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E9521FAB45BD1D6 (source_currency_id), INDEX IDX_E9521FABBF1ECE7C (target_currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, advert_id INT NOT NULL, owner_id INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_68C58ED9D07ECCB6 (advert_id), INDEX IDX_68C58ED97E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invitation (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, code VARCHAR(100) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F11D61A27E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, product_id INT NOT NULL, quantity INT DEFAULT NULL, amount INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1F1B251E82EA2E54 (commande_id), INDEX IDX_1F1B251E4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(255) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, latitude VARCHAR(255) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE login_attempt (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_8C11C1B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_data (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset_token (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6B7BA4B67E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, price INT DEFAULT NULL, discount INT DEFAULT NULL, taxe INT DEFAULT NULL, refunded TINYINT(1) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6D28840D82EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(255) DEFAULT NULL, price INT DEFAULT NULL, days INT DEFAULT NULL, amount INT DEFAULT NULL, `option` INT DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, advert_id INT NOT NULL, email VARCHAR(180) DEFAULT NULL, reason VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_C42F7784D07ECCB6 (advert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, base_currency_id INT NOT NULL, default_locale_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, facebook_address VARCHAR(255) DEFAULT NULL, twitter_address VARCHAR(255) DEFAULT NULL, instagram_address VARCHAR(255) DEFAULT NULL, youtube_address VARCHAR(255) DEFAULT NULL, linkedin_address VARCHAR(255) DEFAULT NULL, advert_activated_days INT DEFAULT NULL, active_thread TINYINT(1) DEFAULT NULL, active_ad_favorite TINYINT(1) DEFAULT NULL, active_alert TINYINT(1) DEFAULT NULL, active_advert_similar TINYINT(1) DEFAULT NULL, active_credit TINYINT(1) DEFAULT NULL, active_card_payment TINYINT(1) DEFAULT NULL, active_vignette TINYINT(1) DEFAULT NULL, active_pack TINYINT(1) DEFAULT NULL, active_parrainage TINYINT(1) DEFAULT NULL, active_register_drift TINYINT(1) DEFAULT NULL, number_advert_per_page INT DEFAULT NULL, number_user_advert_per_page INT DEFAULT NULL, number_user_advert_favorite_per_page INT DEFAULT NULL, parrain_credit_offer INT DEFAULT NULL, fiole_credit_offer INT NOT NULL, register_drift_credit_offer INT DEFAULT NULL, parrainage_number_advert_required INT DEFAULT NULL, register_drift_number_advert_required INT DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E545A0C53101778E (base_currency_id), UNIQUE INDEX UNIQ_E545A0C5743BF776 (default_locale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suggestion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, advert_id INT NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_31204C83B03A8386 (created_by_id), INDEX IDX_31204C83D07ECCB6 (advert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread_message (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, thread_id INT DEFAULT NULL, body LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_707D836F624B39D (sender_id), INDEX IDX_707D836E2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread_message_metadata (id INT AUTO_INCREMENT NOT NULL, participant_id INT DEFAULT NULL, message_id INT NOT NULL, thread_id INT NOT NULL, is_read TINYINT(1) DEFAULT NULL, INDEX IDX_31A5ABB39D1C3019 (participant_id), INDEX IDX_31A5ABB3537A1329 (message_id), INDEX IDX_31A5ABB3E2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread_metadata (id INT AUTO_INCREMENT NOT NULL, participant_id INT DEFAULT NULL, is_deleted TINYINT(1) DEFAULT NULL, last_participant_message_date DATETIME DEFAULT NULL, last_message_date DATETIME DEFAULT NULL, INDEX IDX_40A577C89D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, invitation_id INT DEFAULT NULL, wallet_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(180) DEFAULT NULL, firstname VARCHAR(180) DEFAULT NULL, lastname VARCHAR(180) DEFAULT NULL, phone VARCHAR(25) DEFAULT NULL, phone_state TINYINT(1) DEFAULT NULL, birth_day DATE DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, facebook_address VARCHAR(255) DEFAULT NULL, twitter_address VARCHAR(255) DEFAULT NULL, instagram_address VARCHAR(255) DEFAULT NULL, linkedin_address VARCHAR(255) DEFAULT NULL, youtube_address VARCHAR(255) DEFAULT NULL, business_web_site VARCHAR(255) DEFAULT NULL, business_name VARCHAR(255) DEFAULT NULL, business_city VARCHAR(255) DEFAULT NULL, business_zone VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, banned_at DATETIME DEFAULT NULL, last_login_ip VARCHAR(255) DEFAULT NULL, last_login_at DATETIME DEFAULT NULL, is_verified TINYINT(1) DEFAULT NULL, subscribed_to_newsletter TINYINT(1) DEFAULT NULL, drift TINYINT(1) DEFAULT NULL, parrainage_drift TINYINT(1) DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, notifications_read_at DATETIME DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, facebook_id VARCHAR(255) DEFAULT NULL, delete_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', premium_end DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649A35D7AF0 (invitation_id), UNIQUE INDEX UNIQ_8D93D649712520F3 (wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vignette (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, total_amount INT DEFAULT NULL, freeze_amount INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet_history (id INT AUTO_INCREMENT NOT NULL, wallet_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, amount INT DEFAULT NULL, type INT DEFAULT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_56BB555D712520F3 (wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(100) DEFAULT NULL, slug VARCHAR(100) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, INDEX IDX_A0EBC0078BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40BF7BFE87C FOREIGN KEY (sub_category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40B64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE advert_picture ADD CONSTRAINT FK_BC950FDFD07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id)');
        $this->addSql('ALTER TABLE advert_read ADD CONSTRAINT FK_D177002AD07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id)');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C17E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1FEEAF2D3 FOREIGN KEY (premiums_id) REFERENCES category_premium (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D4C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DD07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id)');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE22358F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FAB45BD1D6 FOREIGN KEY (source_currency_id) REFERENCES currency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FABBF1ECE7C FOREIGN KEY (target_currency_id) REFERENCES currency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9D07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id)');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED97E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A27E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE login_attempt ADD CONSTRAINT FK_8C11C1B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE password_reset_token ADD CONSTRAINT FK_6B7BA4B67E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784D07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE settings ADD CONSTRAINT FK_E545A0C53101778E FOREIGN KEY (base_currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE settings ADD CONSTRAINT FK_E545A0C5743BF776 FOREIGN KEY (default_locale_id) REFERENCES locale (id)');
        $this->addSql('ALTER TABLE thread ADD CONSTRAINT FK_31204C83B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE thread ADD CONSTRAINT FK_31204C83D07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id)');
        $this->addSql('ALTER TABLE thread_message ADD CONSTRAINT FK_707D836F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE thread_message ADD CONSTRAINT FK_707D836E2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE thread_message_metadata ADD CONSTRAINT FK_31A5ABB39D1C3019 FOREIGN KEY (participant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE thread_message_metadata ADD CONSTRAINT FK_31A5ABB3537A1329 FOREIGN KEY (message_id) REFERENCES thread_message (id)');
        $this->addSql('ALTER TABLE thread_message_metadata ADD CONSTRAINT FK_31A5ABB3E2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE thread_metadata ADD CONSTRAINT FK_40A577C89D1C3019 FOREIGN KEY (participant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649A35D7AF0 FOREIGN KEY (invitation_id) REFERENCES invitation (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE wallet_history ADD CONSTRAINT FK_56BB555D712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC0078BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advert_picture DROP FOREIGN KEY FK_BC950FDFD07ECCB6');
        $this->addSql('ALTER TABLE advert_read DROP FOREIGN KEY FK_D177002AD07ECCB6');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DD07ECCB6');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9D07ECCB6');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784D07ECCB6');
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C83D07ECCB6');
        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40B12469DE2');
        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40BF7BFE87C');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1FEEAF2D3');
        $this->addSql('ALTER TABLE zone DROP FOREIGN KEY FK_A0EBC0078BAC62AF');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E82EA2E54');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D82EA2E54');
        $this->addSql('ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FAB45BD1D6');
        $this->addSql('ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FABBF1ECE7C');
        $this->addSql('ALTER TABLE settings DROP FOREIGN KEY FK_E545A0C53101778E');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D4C7C611F');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649A35D7AF0');
        $this->addSql('ALTER TABLE settings DROP FOREIGN KEY FK_E545A0C5743BF776');
        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40B64D218E');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E4584665A');
        $this->addSql('ALTER TABLE thread_message DROP FOREIGN KEY FK_707D836E2904019');
        $this->addSql('ALTER TABLE thread_message_metadata DROP FOREIGN KEY FK_31A5ABB3E2904019');
        $this->addSql('ALTER TABLE thread_message_metadata DROP FOREIGN KEY FK_31A5ABB3537A1329');
        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40B7E3C61F9');
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C17E3C61F9');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D7E3C61F9');
        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE22358F675F31B');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED97E3C61F9');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A27E3C61F9');
        $this->addSql('ALTER TABLE login_attempt DROP FOREIGN KEY FK_8C11C1B7E3C61F9');
        $this->addSql('ALTER TABLE password_reset_token DROP FOREIGN KEY FK_6B7BA4B67E3C61F9');
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C83B03A8386');
        $this->addSql('ALTER TABLE thread_message DROP FOREIGN KEY FK_707D836F624B39D');
        $this->addSql('ALTER TABLE thread_message_metadata DROP FOREIGN KEY FK_31A5ABB39D1C3019');
        $this->addSql('ALTER TABLE thread_metadata DROP FOREIGN KEY FK_40A577C89D1C3019');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649712520F3');
        $this->addSql('ALTER TABLE wallet_history DROP FOREIGN KEY FK_56BB555D712520F3');
        $this->addSql('DROP TABLE advert');
        $this->addSql('DROP TABLE advert_picture');
        $this->addSql('DROP TABLE advert_read');
        $this->addSql('DROP TABLE alert');
        $this->addSql('DROP TABLE ban');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_premium');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE contact_request');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE email_verification');
        $this->addSql('DROP TABLE emailing');
        $this->addSql('DROP TABLE exchange_rate');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE locale');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE login_attempt');
        $this->addSql('DROP TABLE newsletter_data');
        $this->addSql('DROP TABLE password_reset_token');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE suggestion');
        $this->addSql('DROP TABLE thread');
        $this->addSql('DROP TABLE thread_message');
        $this->addSql('DROP TABLE thread_message_metadata');
        $this->addSql('DROP TABLE thread_metadata');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE vignette');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE wallet_history');
        $this->addSql('DROP TABLE zone');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
