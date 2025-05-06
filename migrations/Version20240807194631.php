<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807194631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add foreign key constraints to various tables';
    }

    public function up(Schema $schema): void
    {
        // Add foreign key constraints only if they do not exist
        $this->addSql('ALTER TABLE commentaire_etiquette ADD CONSTRAINT IF NOT EXISTS FK_9199966D73A201E5 FOREIGN KEY (createur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire_etiquette ADD CONSTRAINT IF NOT EXISTS FK_9199966D7BD2EA57 FOREIGN KEY (etiquette_id) REFERENCES etiquette (id)');
        $this->addSql('ALTER TABLE documentpartenaire ADD CONSTRAINT IF NOT EXISTS FK_6D5B2ACF5C68DBFF FOREIGN KEY (idetablisment) REFERENCES etablisment (id)');
        $this->addSql('ALTER TABLE emailing ADD CONSTRAINT IF NOT EXISTS FK_5E9C9F966DB8C967 FOREIGN KEY (typeemail) REFERENCES ref (id)');
        $this->addSql('ALTER TABLE emailing ADD CONSTRAINT IF NOT EXISTS FK_5E9C9F9694BB011 FOREIGN KEY (id_user_creation) REFERENCES user (id)');
        $this->addSql('ALTER TABLE emailing ADD CONSTRAINT IF NOT EXISTS FK_5E9C9F965EFA2B3B FOREIGN KEY (id_image1) REFERENCES attachment (id)');
        $this->addSql('ALTER TABLE emailing ADD CONSTRAINT IF NOT EXISTS FK_5E9C9F96C7F37A81 FOREIGN KEY (id_image2) REFERENCES attachment (id)');
        $this->addSql('ALTER TABLE emailing ADD CONSTRAINT IF NOT EXISTS FK_5E9C9F96E564F0BF FOREIGN KEY (statut) REFERENCES ref (id)');
        $this->addSql('ALTER TABLE emailing ADD CONSTRAINT IF NOT EXISTS FK_5E9C9F96DD688AE0 FOREIGN KEY (id_destinataire) REFERENCES user (id)');
        $this->addSql('ALTER TABLE etablisment ADD CONSTRAINT IF NOT EXISTS FK_9AA9AB5068D3EA09 FOREIGN KEY (User_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE etiquette ADD CONSTRAINT IF NOT EXISTS FK_1E0E195A315B405 FOREIGN KEY (support_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE etiquette ADD CONSTRAINT IF NOT EXISTS FK_1E0E195A2AF5D182 FOREIGN KEY (rapporteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE fonctions ADD CONSTRAINT IF NOT EXISTS FK_AED700EFF6203804 FOREIGN KEY (statut_id) REFERENCES ref (id)');
        $this->addSql('ALTER TABLE jourdescripdate ADD CONSTRAINT IF NOT EXISTS FK_218C35CEF2488D84 FOREIGN KEY (idsejour) REFERENCES sejour (id)');
        $this->addSql('ALTER TABLE likephoto ADD CONSTRAINT IF NOT EXISTS FK_90E7287DDAB61219 FOREIGN KEY (id_sejour_attchment) REFERENCES sejour_attachment (id)');
        $this->addSql('ALTER TABLE likephoto ADD CONSTRAINT IF NOT EXISTS FK_90E7287DF7384557 FOREIGN KEY (id_produit) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE likephoto ADD CONSTRAINT IF NOT EXISTS FK_90E7287D6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE log_promotions ADD CONSTRAINT IF NOT EXISTS FK_E00D9C8F9AF8E3A3 FOREIGN KEY (id_commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE log_promotions ADD CONSTRAINT IF NOT EXISTS FK_E00D9C8F99DED506 FOREIGN KEY (id_client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE log_promotions ADD CONSTRAINT IF NOT EXISTS FK_E00D9C8F305C84E6 FOREIGN KEY (id_promotion_id) REFERENCES promotions (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT IF NOT EXISTS FK_140AB620F1A30136 FOREIGN KEY (idtypepage) REFERENCES typepage (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT IF NOT EXISTS FK_140AB620F6A1BE49 FOREIGN KEY (idproduit) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT IF NOT EXISTS FK_140AB62099B4A8EA FOREIGN KEY (pagesuivante) REFERENCES page (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT IF NOT EXISTS FK_140AB62093006792 FOREIGN KEY (pageprecedente) REFERENCES page (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT IF NOT EXISTS FK_24CC0DF2F6203804 FOREIGN KEY (statut_id) REFERENCES ref (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT IF NOT EXISTS FK_24CC0DF255EF9D79 FOREIGN KEY (creerPar_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT IF NOT EXISTS FK_24CC0DF2F4A6DB99 FOREIGN KEY (idSejour_id) REFERENCES sejour (id)');
        $this->addSql('ALTER TABLE panierproduit ADD CONSTRAINT IF NOT EXISTS FK_656FE9BA8DC06011 FOREIGN KEY (idProduit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE panierproduit ADD CONSTRAINT IF NOT EXISTS FK_656FE9BAB97B92A FOREIGN KEY (idPanier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE photonsumeriques ADD CONSTRAINT IF NOT EXISTS FK_F1D7175B88794CE8 FOREIGN KEY (id_sejour_id) REFERENCES sejour (id)');
        $this->addSql('ALTER TABLE photonsumeriques ADD CONSTRAINT IF NOT EXISTS FK_F1D7175B79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE photonsumeriques ADD CONSTRAINT IF NOT EXISTS FK_F1D7175BA3E01CD2 FOREIGN KEY (id_sejour_attachement_id) REFERENCES sejour_attachment (id)');
        $this->addSql('ALTER TABLE photonsumeriques ADD CONSTRAINT IF NOT EXISTS FK_F1D7175BAABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT IF NOT EXISTS FK_462CE4F5D86C7917 FOREIGN KEY (idcart) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT IF NOT EXISTS FK_462CE4F559F022DF FOREIGN KEY (Iduser) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT IF NOT EXISTS FK_29A5EC27550607C2 FOREIGN KEY (idsjour) REFERENCES sejour (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT IF NOT EXISTS FK_29A5EC278CDE5729 FOREIGN KEY (type) REFERENCES typeproduit (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT IF NOT EXISTS FK_29A5EC275E5C27E9 FOREIGN KEY (iduser) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT IF NOT EXISTS FK_29A5EC276AF640F0 FOREIGN KEY (idConditionnement_id) REFERENCES typeproduitconditionnement (id)');
        $this->addSql('ALTER TABLE produit_attachement ADD CONSTRAINT IF NOT EXISTS FK_EE045DE5F7384557 FOREIGN KEY (id_produit) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit_attachement ADD CONSTRAINT IF NOT EXISTS FK_EE045DE5880ED496 FOREIGN KEY (id_attachement) REFERENCES attachment (id)');
        $this->addSql('ALTER TABLE promo_parents ADD CONSTRAINT IF NOT EXISTS FK_F524A558139DF194 FOREIGN KEY (promotion_id) REFERENCES promotions (id)');
        $this->addSql('ALTER TABLE promo_parents ADD CONSTRAINT IF NOT EXISTS FK_F524A558727ACA70 FOREIGN KEY (parent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE promo_sejour ADD CONSTRAINT IF NOT EXISTS FK_8F703F65139DF194 FOREIGN KEY (promotion_id) REFERENCES promotions (id)');
        $this->addSql('ALTER TABLE promo_sejour ADD CONSTRAINT IF NOT EXISTS FK_8F703F6584CF0CF FOREIGN KEY (sejour_id) REFERENCES sejour (id)');
        $this->addSql('ALTER TABLE promotions ADD CONSTRAINT IF NOT EXISTS FK_EA1B3034F6203804 FOREIGN KEY (statut_id) REFERENCES ref (id)');
        $this->addSql('ALTER TABLE ref ADD CONSTRAINT IF NOT EXISTS FK_146F3EA3B7627700 FOREIGN KEY (typeref) REFERENCES typeref (id)');
        $this->addSql('ALTER TABLE reversement ADD CONSTRAINT IF NOT EXISTS FK_6D601223977523A4 FOREIGN KEY (id_partenaire) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reversement ADD CONSTRAINT IF NOT EXISTS FK_6D601223EB53AD6B FOREIGN KEY (id_typeproduit) REFERENCES typeproduit (id)');
        $this->addSql('ALTER TABLE sejour_attachment ADD CONSTRAINT IF NOT EXISTS FK_5C987F22117DC25D FOREIGN KEY (id_attchment) REFERENCES attachment (id)');
        $this->addSql('ALTER TABLE sejour_attachment ADD CONSTRAINT IF NOT EXISTS FK_5C987F22B0C295C5 FOREIGN KEY (id_sejour) REFERENCES sejour (id)');
        $this->addSql('ALTER TABLE sejour_attachment ADD CONSTRAINT IF NOT EXISTS FK_5C987F228E90E126 FOREIGN KEY (idParent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT IF NOT EXISTS FK_694309E48D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT IF NOT EXISTS FK_694309E42012002D FOREIGN KEY (slide1) REFERENCES slide (id)');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT IF NOT EXISTS FK_694309E4B91B5197 FOREIGN KEY (slide2) REFERENCES slide (id)');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT IF NOT EXISTS FK_694309E4CE1C6101 FOREIGN KEY (slide3) REFERENCES slide (id)');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT IF NOT EXISTS FK_694309E4E564F0BF FOREIGN KEY (statut) REFERENCES ref (id)');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT IF NOT EXISTS FK_72EFEE62C53D045F FOREIGN KEY (image) REFERENCES attachment (id)');
        $this->addSql('ALTER TABLE sms_notif ADD CONSTRAINT IF NOT EXISTS FK_42C0D43632331766 FOREIGN KEY (Id_sejour) REFERENCES sejour (id)');
        $this->addSql('ALTER TABLE type_produit_photo ADD CONSTRAINT IF NOT EXISTS FK_54C654B9880ED496 FOREIGN KEY (id_attachement) REFERENCES attachment (id)');
        $this->addSql('ALTER TABLE type_produit_photo ADD CONSTRAINT IF NOT EXISTS FK_54C654B92EBC99BA FOREIGN KEY (id_typep) REFERENCES typeproduit (id)');
        $this->addSql('ALTER TABLE type_produit_photo ADD CONSTRAINT IF NOT EXISTS FK_54C654B9E815D952 FOREIGN KEY (idProduitConditionnement_id) REFERENCES typeproduitconditionnement (id)');
        $this->addSql('ALTER TABLE typeproduit ADD CONSTRAINT IF NOT EXISTS FK_F341609C5E5C27E9 FOREIGN KEY (iduser) REFERENCES user (id)');
        $this->addSql('ALTER TABLE typeproduitconditionnement ADD CONSTRAINT IF NOT EXISTS FK_AF8ED7C9652F74B5 FOREIGN KEY (idTypeProduit_id) REFERENCES typeproduit (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT IF NOT EXISTS FK_8D93D649E985CED1 FOREIGN KEY (adresslivraison) REFERENCES adress (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT IF NOT EXISTS FK_8D93D6499D504691 FOREIGN KEY (adressfactoration) REFERENCES adress (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT IF NOT EXISTS FK_8D93D649E564F0BF FOREIGN KEY (statut) REFERENCES ref (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT IF NOT EXISTS FK_8D93D6493CE77466 FOREIGN KEY (usersecondaire) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT IF NOT EXISTS FK_8D93D649C22FC0CB FOREIGN KEY (comptebanque) REFERENCES comptebancaire (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT IF NOT EXISTS FK_8D93D6494F9A54D7 FOREIGN KEY (id_fonction_id) REFERENCES fonctions (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop the foreign key constraints
        $this->addSql('ALTER TABLE commentaire_etiquette DROP FOREIGN KEY FK_9199966D73A201E5');
        $this->addSql('ALTER TABLE commentaire_etiquette DROP FOREIGN KEY FK_9199966D7BD2EA57');
        $this->addSql('ALTER TABLE documentpartenaire DROP FOREIGN KEY FK_6D5B2ACF5C68DBFF');
        $this->addSql('ALTER TABLE emailing DROP FOREIGN KEY FK_5E9C9F966DB8C967');
        $this->addSql('ALTER TABLE emailing DROP FOREIGN KEY FK_5E9C9F9694BB011');
        $this->addSql('ALTER TABLE emailing DROP FOREIGN KEY FK_5E9C9F965EFA2B3B');
        $this->addSql('ALTER TABLE emailing DROP FOREIGN KEY FK_5E9C9F96C7F37A81');
        $this->addSql('ALTER TABLE emailing DROP FOREIGN KEY FK_5E9C9F96E564F0BF');
        $this->addSql('ALTER TABLE emailing DROP FOREIGN KEY FK_5E9C9F96DD688AE0');
        $this->addSql('ALTER TABLE etablisment DROP FOREIGN KEY FK_9AA9AB5068D3EA09');
        $this->addSql('ALTER TABLE etiquette DROP FOREIGN KEY FK_1E0E195A315B405');
        $this->addSql('ALTER TABLE etiquette DROP FOREIGN KEY FK_1E0E195A2AF5D182');
        $this->addSql('ALTER TABLE fonctions DROP FOREIGN KEY FK_AED700EFF6203804');
        $this->addSql('ALTER TABLE jourdescripdate DROP FOREIGN KEY FK_218C35CEF2488D84');
        $this->addSql('ALTER TABLE likephoto DROP FOREIGN KEY FK_90E7287DDAB61219');
        $this->addSql('ALTER TABLE likephoto DROP FOREIGN KEY FK_90E7287DF7384557');
        $this->addSql('ALTER TABLE likephoto DROP FOREIGN KEY FK_90E7287D6B3CA4B');
        $this->addSql('ALTER TABLE log_promotions DROP FOREIGN KEY FK_E00D9C8F9AF8E3A3');
        $this->addSql('ALTER TABLE log_promotions DROP FOREIGN KEY FK_E00D9C8F99DED506');
        $this->addSql('ALTER TABLE log_promotions DROP FOREIGN KEY FK_E00D9C8F305C84E6');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620F1A30136');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620F6A1BE49');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB62099B4A8EA');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB62093006792');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2F6203804');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF255EF9D79');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2F4A6DB99');
        $this->addSql('ALTER TABLE panierproduit DROP FOREIGN KEY FK_656FE9BA8DC06011');
        $this->addSql('ALTER TABLE panierproduit DROP FOREIGN KEY FK_656FE9BAB97B92A');
        $this->addSql('ALTER TABLE photonsumeriques DROP FOREIGN KEY FK_F1D7175B88794CE8');
        $this->addSql('ALTER TABLE photonsumeriques DROP FOREIGN KEY FK_F1D7175B79F37AE5');
        $this->addSql('ALTER TABLE photonsumeriques DROP FOREIGN KEY FK_F1D7175BA3E01CD2');
        $this->addSql('ALTER TABLE photonsumeriques DROP FOREIGN KEY FK_F1D7175BAABEFE2C');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F5D86C7917');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F559F022DF');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27550607C2');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC278CDE5729');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC275E5C27E9');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC276AF640F0');
        $this->addSql('ALTER TABLE produit_attachement DROP FOREIGN KEY FK_EE045DE5F7384557');
        $this->addSql('ALTER TABLE produit_attachement DROP FOREIGN KEY FK_EE045DE5880ED496');
        $this->addSql('ALTER TABLE promo_parents DROP FOREIGN KEY FK_F524A558139DF194');
        $this->addSql('ALTER TABLE promo_parents DROP FOREIGN KEY FK_F524A558727ACA70');
        $this->addSql('ALTER TABLE promo_sejour DROP FOREIGN KEY FK_8F703F65139DF194');
        $this->addSql('ALTER TABLE promo_sejour DROP FOREIGN KEY FK_8F703F6584CF0CF');
        $this->addSql('ALTER TABLE promotions DROP FOREIGN KEY FK_EA1B3034F6203804');
        $this->addSql('ALTER TABLE ref DROP FOREIGN KEY FK_146F3EA3B7627700');
        $this->addSql('ALTER TABLE reversement DROP FOREIGN KEY FK_6D601223977523A4');
        $this->addSql('ALTER TABLE reversement DROP FOREIGN KEY FK_6D601223EB53AD6B');
        $this->addSql('ALTER TABLE sejour_attachment DROP FOREIGN KEY FK_5C987F22117DC25D');
        $this->addSql('ALTER TABLE sejour_attachment DROP FOREIGN KEY FK_5C987F22B0C295C5');
        $this->addSql('ALTER TABLE sejour_attachment DROP FOREIGN KEY FK_5C987F228E90E126');
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E48D93D649');
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E42012002D');
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E4B91B5197');
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E4CE1C6101');
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E4E564F0BF');
        $this->addSql('ALTER TABLE slide DROP FOREIGN KEY FK_72EFEE62C53D045F');
        $this->addSql('ALTER TABLE sms_notif DROP FOREIGN KEY FK_42C0D43632331766');
        $this->addSql('ALTER TABLE type_produit_photo DROP FOREIGN KEY FK_54C654B9880ED496');
        $this->addSql('ALTER TABLE type_produit_photo DROP FOREIGN KEY FK_54C654B92EBC99BA');
        $this->addSql('ALTER TABLE type_produit_photo DROP FOREIGN KEY FK_54C654B9E815D952');
        $this->addSql('ALTER TABLE typeproduit DROP FOREIGN KEY FK_F341609C5E5C27E9');
        $this->addSql('ALTER TABLE typeproduitconditionnement DROP FOREIGN KEY FK_AF8ED7C9652F74B5');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E985CED1');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499D504691');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E564F0BF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493CE77466');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C22FC0CB');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494F9A54D7');
    }
}
