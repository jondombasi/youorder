<?php
mysqli_report(MYSQLI_REPORT_STRICT);
class Commercant {
    // déclaration des propriétés
    private $_id_commercant;
    private $_nom;
    private $_adresse;
    private $_longitude;
    private $_latitude;
    private $_contact;
    private $_numero;
    private $_type_factu;
    private $_engagement;
    private $_tarif_jour_semaine;
    private $_tarif_jour_autre;
    private $_tarif_nuit_semaine;
    private $_tarif_nuit_autre;
    private $_utilisateur;
    private $_perso_suivi;
    private $_photo;
    private $_sms_client;
    private $_sms_client_txt;
    private $_type_livraison;
    private $_nbRes;
    private $_nbPages;

    private $_sql;

    private $_listeCommercant=array();

    // déclaration des méthodes
    public function __construct($sql, $id = null) {
        $this->_sql=$sql;
       	if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM restaurants WHERE id=".$this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_id_commercant       = $ligne[0]->id;
            $this->_nom                 = $ligne[0]->nom;
            $this->_adresse             = $ligne[0]->adresse;
            $this->_longitude           = $ligne[0]->longitude;
            $this->_latitude            = $ligne[0]->latitude;
            $this->_contact             = $ligne[0]->contact;
            $this->_numero              = $ligne[0]->numero;
            $this->_utilisateur         = $ligne[0]->utilisateur;
            $this->_perso_suivi         = $ligne[0]->perso_suivi;
            $this->_photo               = $ligne[0]->photo;
            $this->_sms_client          = $ligne[0]->sms_client;
            $this->_sms_client_txt      = $ligne[0]->sms_client_txt;
            $this->_type_livraison      = $ligne[0]->type_livraison;
            $this->_type_factu          = $ligne[0]->type_factu;
            $this->_engagement          = $ligne[0]->engamement;
            $this->_tarif_jour_semaine  = $ligne[0]->tarif_jour_semaine;
            $this->_tarif_jour_autre    = $ligne[0]->tarif_jour_autre;
            $this->_tarif_nuit_semaine  = $ligne[0]->tarif_jour_semaine;
            $this->_tarif_nuit_autre    = $ligne[0]->tarif_jour_autre;
        }
    }

    public function getNom() {
        return $this->_nom;
    }

    public function getAdresse() {
        return $this->_adresse;
    }

    public function getLongitude() {
        return $this->_longitude;
    }

    public function getLatitude() {
        return $this->_latitude;
    }

    public function getContact() {
        return $this->_contact;
    }

    public function getNumero() {
        return $this->_numero;
    }

    public function getTypeFactu() {
        return $this->_type_factu;
    }

    public function getEngagement() {
        return $this->_engagement;
    }

    public function getTarifJourSemaine() {
        return $this->_tarif_jour_semaine;
    }

    public function getTarifJourAutre() {
        return $this->_tarif_jour_autre;
    }

    public function getTarifNuitSemaine() {
        return $this->_tarif_nuit_semaine;
    }

    public function getTarifNuitAutre() {
        return $this->_tarif_nuit_autre;
    }

    public function getUtilisateur() {
        return $this->_utilisateur;
    }

    public function getPersoSuivi() {
        return $this->_perso_suivi;
    }

    public function getPhoto() {
        return $this->_photo;
    }

    public function getSmsClient() {
        return $this->_sms_client;
    }

    public function getSmsClientTxt() {
        return $this->_sms_client_txt;
    }

    public function getTypeLivraison() {
        return $this->_type_livraison;
    }

    public function getNbPages() {
        return $this->_nbPages;
    }

    public function getNbRes() {
        return $this->_nbRes;
    }

    public function getPagination($nbmess, $nom) {
        //compter le nb de pages et de résultats
        $req_sup="";
        if ($nom!="") {
            $req_sup=" AND nom LIKE '%".$nom."%'";
        }

        $req = "SELECT count(*) as NB FROM restaurants r WHERE r.statut = '1' ".$req_sup.$_SESSION["req_resto"];
        $result = $this->_sql->query($req);
        $ligne = $result->fetch();
        if($ligne!=""){
            $this->_nbRes = $ligne["NB"];
        }else{
            $this->_nbRes = 0;
        }
        $this->_nbPages = $this->_nbRes/$nbmess;
        $this->_nbPages = ceil($this->_nbPages);
    }

    public function getAll($page, $nbmess, $nom=null, $order_by=null) {
        $req_sup="";
        $req_limit="";
        $req_order="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if($nom!==null && $nom!="") {
            $req_sup.=" AND r.nom LIKE '%".$nom."%'";
        }

        if ($order_by==true) {
            $req_order=" ORDER BY r.nom";
        }

        $result = $this->_sql->query("SELECT r.*,u.nom as u_nom, u.prenom as u_prenom FROM restaurants r INNER JOIN utilisateurs u ON r.utilisateur=u.id WHERE r.statut = 1 ".$req_sup.$_SESSION["req_resto"].$req_order.$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeCommercant
        $_listeCommercant = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeCommercant;
    }


    public function setCommercant($id_commercant, $nom, $adresse, $latitude, $longitude, $contact, $numero, $admin, $perso_suivi, $sms_client, $sms_client_txt, $type_livraison) {
        if ($id_commercant=="") {
            $result = $this->_sql->exec("INSERT INTO restaurants (nom, adresse, latitude, longitude, contact, numero, utilisateur, date_ajout, perso_suivi, sms_client, sms_client_txt, type_livraison) VALUES (".$this->_sql->quote($nom).", ".$this->_sql->quote($adresse).", ".$this->_sql->quote($latitude).", ".$this->_sql->quote($longitude).", ".$this->_sql->quote($contact).", ".$this->_sql->quote($numero).", ".$this->_sql->quote($admin).", NOW(), ".$this->_sql->quote($perso_suivi).", ".$this->_sql->quote($sms_client).", ".$this->_sql->quote($sms_client_txt).", ".$this->_sql->quote($type_livraison).")");
            $id_commercant=$this->_sql->lastInsertId();
        }
        else {
            $result = $this->_sql->exec("UPDATE restaurants SET nom=".$this->_sql->quote($nom).", adresse=".$this->_sql->quote($adresse).", latitude=".$this->_sql->quote($latitude).", longitude=".$this->_sql->quote($longitude).", numero=".$this->_sql->quote($numero).", contact=".$this->_sql->quote($contact).", utilisateur=".$this->_sql->quote($admin).", perso_suivi=".$this->_sql->quote($perso_suivi).", sms_client=".$this->_sql->quote($sms_client).", sms_client_txt=".$this->_sql->quote($sms_client_txt).", type_livraison=".$this->_sql->quote($type_livraison)." WHERE id=".$this->_sql->quote($id_commercant));
        }
        return $id_commercant;
    }

    public function setPhoto($id, $url) {
        $this->_photo=$url;
        $result = $this->_sql->exec("UPDATE restaurants SET photo=".$this->_sql->quote($url)." WHERE id=".$this->_sql->quote($id));
    }

    public function getAllService($nom=null, $order_by=null) {
        $req_sup="";
        $req_limit="";
        $req_order="";

        if($nom!==null && $nom!="") {
            $req_sup.=" AND r.nom LIKE '%".$nom."%'";
        }

        if ($order_by==true) {
            $req_order=" ORDER BY r.nom";
        }
        $result = $this->_sql->query("SELECT r.nom, p.id_commercant, u.nom as u_nom, u.prenom as u_prenom FROM livreurs_planning p LEFT JOIN restaurants r ON p.id_commercant=r.id INNER JOIN utilisateurs u ON r.utilisateur=u.id WHERE NOW() BETWEEN p.date_debut AND p.date_fin".$_SESSION["req_resto"]." GROUP BY p.id_commercant ORDER BY p.id_commercant".$req_sup.$req_order.$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeCommercantService
        $_listeCommercantService = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeCommercantService;
    }

    //Récupérer tous les shifts des livreurs par commerçant --> shift_commercant.php
    public function getAllLivreur($id_commercant, $nom=null, $order_by=null) {
        $req_sup="";
        $req_limit="";
        $req_order="";

        if($nom!==null && $nom!="") {
            $req_sup.=" AND r.nom LIKE '%".$nom."%'";
        }

        if ($order_by==true) {
            $req_order=" ORDER BY r.nom";
        }

        $result = $this->_sql->query("SELECT l.nom, l.prenom, l.statut, p.id_livreur, p.id_commercant FROM livreurs_planning p LEFT JOIN livreurs l ON p.id_livreur=l.id LEFT JOIN restaurants r ON r.id = p.id_commercant WHERE r.id=".$this->_sql->quote($id_commercant)." AND NOW() BETWEEN p.date_debut AND p.date_fin GROUP BY p.id_livreur ORDER BY p.id_livreur");
        // Récupération des résultats sélectionnés dans le tableau $_listeCommercantService
        $_listeCommercantService = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeCommercantService;
    }

    public function verifCommercant($nom_commercant) {
        $result = $this->_sql->query("SELECT id FROM restaurants WHERE nom=".$this->_sql->quote($nom_commercant));
        $ligne = $result->fetch();
        if($ligne){
            return $ligne["id"];
        }
        return false;
    }

    public function getAllDelete($page, $nbmess, $nom){

        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($nom!="") {
            $req_sup.=" AND nom LIKE '%".$nom."%'";
        }

        $result = $this->_sql->query("SELECT r.* FROM restaurants r WHERE statut= 0 ".$req_sup." ".$req_limit);

        $listeDelete    = $result->fetchAll(PDO::FETCH_OBJ);
        return $listeDelete;

    }


}
?>