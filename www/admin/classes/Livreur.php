<?php
class Livreur {
    // déclaration des propriétés
    private $_id_livreur;
    private $_nom;
    private $_prenom;
    private $_email;
    private $_password;
    private $_telephone;
    private $_nbheures;
    private $_situation;
    private $_etat;
    private $_photo;
    private $_note;
    private $_dateConnexion;
    private $_longitude;
    private $_latitude;
    private $_device_id;
    private $_nbPages;
    private $_nbRes;
    private $_nbPagesCommande;
    private $_nbResCommande;
    private $_nbNote;

    private $_sql;

    private $_listeLivreur  = array();
    private $_listeNote     = array();
    private $_listeCommande = array();

    // déclaration des méthodes
    public function __construct($sql, $id = null) {
        $this->_sql=$sql;
       	if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM livreurs WHERE id=".$this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_id_livreur      = $ligne[0]->id;
            $this->_nom             = $ligne[0]->nom;
            $this->_prenom          = $ligne[0]->prenom;
            $this->_email           = $ligne[0]->email;
            $this->_password        = $ligne[0]->password;
            $this->_telephone       = $ligne[0]->telephone;
            $this->_nbheures        = $ligne[0]->nb_heures;
            $this->_situation       = $ligne[0]->situation;
            $this->_etat            = $ligne[0]->etat;
            $this->_photo           = $ligne[0]->photo;
            $this->_note            = $ligne[0]->note;
            $this->_dateConnexion   = $ligne[0]->date_connexion;
            $this->_longitude       = $ligne[0]->longitude;
            $this->_latitude        = $ligne[0]->latitude;
            $this->_device_id       = $ligne[0]->device_id;
        }
    }

    public function getNom() {
        return $this->_nom;
    }

    public function getPrenom() {
        return $this->_prenom;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function getTelephone() {
        return $this->_telephone;
    }

    public function getNbHeures() {
        return $this->_nbheures;
    }

    public function getSituation() {
        return $this->_situation;
    }

    public function getEtat() {
        return $this->_etat;
    }

    public function getPhoto() {
        return $this->_photo;
    }

    public function getNote() {
        return $this->_note;
    }

    public function getDateConnexion() {
        return $this->_dateConnexion;
    }

    public function getLongitude() {
        return $this->_longitude;
    }

    public function getLatitude() {
        return $this->_latitude;
    }

    public function getDeviceId() {
        return $this->_device_id;
    }

    public function getNbPages() {
        return $this->_nbPages;
    }

    public function getNbRes() {
        return $this->_nbRes;
    }

    public function getNbPagesCommande() {
        return $this->_nbPagesCommande;
    }

    public function getNbResCommande() {
        return $this->_nbResCommande;
    }

    public function setPhoto($id_livreur, $url) {
        $this->_photo=$url;
        $result = $this->_sql->exec("UPDATE livreurs SET photo=".$this->_sql->quote($url)." WHERE id=".$this->_sql->quote($id_livreur));
    }

    public function getPagination($nbmess, $nom, $statut, $numero) {
        //compter le nb de pages et de résultats
        $req_sup="";
        if ($nom!="") {
            $req_sup.=" AND l.nom LIKE '%".$nom."%'";
        }
        if ($statut!="") {
            $req_sup.=" AND l.statut ='".$statut."'";
        }
        if ($numero!="") {
            $req_sup.=" AND l.telephone LIKE '%".$numero."%'";
        }
        if ($_SESSION["restaurateur"]) {
            $req_sup.=" AND NOW() BETWEEN p.date_debut AND p.date_fin";
        }

        $req = "SELECT count(DISTINCT l.id) as NB FROM livreurs l LEFT JOIN livreurs_planning p ON l.id=p.id_livreur WHERE l.statut!='supprime' ".$req_sup.$_SESSION["req_livreur"];
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

    public function getPaginationCommande($nbmess, $id_livreur, $commercant, $statut, $periode) {
        //compter le nb de pages et de résultats
        $req_sup="";
        if ($commercant!="") {
            $req_sup.=" AND restaurant LIKE '%".$commercant."%'";
        }
        if ($statut!="") {
            $req_sup.=" AND statut = '".$statut."'";
        }
        if($periode!=""){
            $periode_ = explode(" - ",$periode);
            $datedebut = $periode_[0];
            $datefin = $periode_[1];

            $req_sup .= " AND date_debut >= '".date("Y-m-d H:i:s",strtotime($datedebut))."' AND date_debut <= '".date("Y-m-d H:i:s",strtotime($datefin))."'"; 
        }

        $req = "SELECT count(*) as NB FROM commandes WHERE livreur=".$this->_sql->quote($id_livreur)." ".$req_sup.$_SESSION["req_commande"];
        $result = $this->_sql->query($req);
        $ligne = $result->fetch();
        if($ligne!=""){
            $this->_nbResCommande = $ligne["NB"];
        }else{
            $this->_nbResCommande = 0;
        }
        $this->_nbPagesCommande = $this->_nbResCommande/$nbmess;
        $this->_nbPagesCommande = ceil($this->_nbPagesCommande);
    }

    public function getAll($page, $nbmess, $nom, $statut, $numero) {
        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($nom!="") {
            $req_sup.=" AND l.nom LIKE '%".$nom."%'";
        }
        if ($statut!="") {
            $req_sup.=" AND l.statut ='".$statut."'";
        }
        if ($numero!="") {
            $req_sup.=" AND l.telephone LIKE '%".$numero."%'";
        }
        if ($_SESSION["restaurateur"]) {
            $req_sup.=" AND NOW() BETWEEN p.date_debut AND p.date_fin";
        }

        $result = $this->_sql->query("SELECT l.* FROM livreurs l LEFT JOIN livreurs_planning p ON l.id=p.id_livreur WHERE l.statut!='supprime'  ".$req_sup.$_SESSION["req_livreur"]." GROUP BY l.id".$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeLivreur
        $_listeLivreur = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeLivreur;
    }

    public function getAllFree($date_debut, $date_fin, $id_livreur) {
        $req_sup="";{
        if ($id_livreur!="" && $id_livreur!=0)
            $req_sup=" OR l.id=".$this->_sql->quote($id_livreur);
        }

        $result = $this->_sql->query("SELECT l.* FROM livreurs l LEFT JOIN livreurs_planning p ON l.id=p.id_livreur AND (".$this->_sql->quote($date_debut)."<p.date_fin AND ".$this->_sql->quote($date_fin).">p.date_debut) WHERE l.statut IN ('ON', 'OFF') AND (p.id IS NULL OR (NOT (".$this->_sql->quote($date_debut)."<p.date_fin AND ".$this->_sql->quote($date_fin).">p.date_debut)) ".$req_sup.") GROUP BY l.id ORDER BY l.id");
        // Récupération des résultats sélectionnés dans le tableau $_listeVehicule
        $_listeLivreur = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeLivreur;
    }

    public function getAllCommande($page, $nbmess, $id_livreur, $commercant, $statut, $periode) {
        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }
        if ($commercant!="") {
            $req_sup.=" AND c.restaurant LIKE '%".$commercant."%'";
        }
        if ($statut!="") {
            $req_sup.=" AND c.statut ='".$statut."'";
        }
        if($periode!=""){
            $periode_ = explode(" - ",$periode);
            $datedebut = $periode_[0];
            $datefin = $periode_[1];

            $req_sup .= " AND c.date_debut >= '".date("Y-m-d H:i:s",strtotime($datedebut))."' AND c.date_debut <= '".date("Y-m-d H:i:s",strtotime($datefin))."'"; 
        }

        $result = $this->_sql->query("SELECT c.id, c.date_debut, c.date_fin, c.statut, c.distance, c.duree, cl.nom as nom_client, cl.prenom as prenom_client, cl.adresse as adresse_client, cl.numero as numero_client, cl.email as email_client, r.nom as nom_resto, r.adresse as adresse_resto, r.numero as numero_resto FROM commandes c INNER JOIN clients cl ON c.client=cl.id INNER JOIN restaurants r ON c.restaurant=r.id WHERE c.livreur=".$this->_sql->quote($id_livreur)." ".$req_sup.$_SESSION["req_resto"]." ORDER BY c.date_ajout DESC ".$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeCommande
        $_listeCommande = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeCommande;
    }

    public function getNbNote($id_livreur) {
        $result = $this->_sql->query("SELECT count(*) as NB FROM livreurs_notes WHERE id_livreur=".$this->_sql->quote($id_livreur));
        // Récupération des résultats sélectionnés dans le tableau $_listeLivreur
        $_listeNote = $result->fetchAll(PDO::FETCH_OBJ);
        $this->_nbNote=$_listeNote[0]->NB;
        return $this->_nbNote;
    }

    public function getAllNote($id_livreur) {
        $result = $this->_sql->query("SELECT ln.*, c.nom, c.prenom FROM livreurs_notes ln LEFT JOIN clients c ON ln.id_client=c.id WHERE ln.id_livreur=".$this->_sql->quote($id_livreur));
        // Récupération des résultats sélectionnés dans le tableau $_listeLivreur
        $_listeNote = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeNote;

    }

    public function setLivreur($id_livreur, $nom, $prenom, $email, $password, $telephone, $nbheures, $situation, $etat) {
        if ($id_livreur=="") {
            $result = $this->_sql->exec("INSERT INTO livreurs (nom, prenom, email, password, telephone, nb_heures, situation, etat) VALUES (".$this->_sql->quote($nom).", ".$this->_sql->quote($prenom).", ".$this->_sql->quote($email).", ".$this->_sql->quote($password).", ".$this->_sql->quote($telephone).", ".$this->_sql->quote($nbheures).", ".$this->_sql->quote($situation).", ".$this->_sql->quote($etat).")");
            $id_livreur=$this->_sql->lastInsertId();
        }
        else {
            $result = $this->_sql->exec("UPDATE livreurs SET nom=".$this->_sql->quote($nom).", prenom=".$this->_sql->quote($prenom).", email=".$this->_sql->quote($email).", password=".$this->_sql->quote($password).", telephone=".$this->_sql->quote($telephone).", nb_heures=".$this->_sql->quote($nbheures).", situation=".$this->_sql->quote($situation).", etat=".$this->_sql->quote($etat)." WHERE id=".$this->_sql->quote($id_livreur));
        }
        return $id_livreur;
    }

    public function getPlanning($id_livreur, $date_debut, $date_fin, $id_vehicule=null, $id_commercant=null) {
        $req_sup="";
        if ($id_livreur!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_livreur);
        }
        if ($id_vehicule!==null && $id_vehicule!="") {
            $req_sup.=" AND l.id_vehicule=".$this->_sql->quote($id_vehicule);
        }
        if ($id_commercant!==null && $id_commercant!="") {
            $req_sup.=" AND l.id_commercant=".$this->_sql->quote($id_commercant);
        }
        if ($date_debut==$date_fin) {
            $heure_fin="23:59:59";
        }
        else {
            $heure_fin="00:00:00";
        }

        $result = $this->_sql->query("SELECT l.*, r.nom as nom_resto, r.adresse as adresse_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.immatriculation as nom_vehicule FROM livreurs_planning l LEFT JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN vehicules v ON l.id_vehicule=v.id WHERE l.date_debut BETWEEN ".$this->_sql->quote($date_debut." 00:00:00")." AND ".$this->_sql->quote($date_fin." ".$heure_fin)." ".$req_sup.$_SESSION["req_resto"]." ORDER BY l.date_debut");
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }


    //liste de tous les shifts (Hormis youOrder Garage)
    public function getPlanningOne($id_livreur, $date_debut, $date_fin, $id_vehicule=null, $id_commercant=null) {
        /*$req_sup="";
        if ($id_livreur!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_livreur);
        }
        if ($id_vehicule!==null && $id_vehicule!="") {
            $req_sup.=" AND l.id_vehicule=".$this->_sql->quote($id_vehicule);
        }
        if ($id_commercant!==null && $id_commercant!="") {
            $req_sup.=" AND l.id_commercant=".$this->_sql->quote($id_commercant);
        }
        if ($date_debut==$date_fin) {
            $heure_fin="23:59:59";
        }
        else {
            $heure_fin="00:00:00";
        }*/
        $exceptions = $this->_sql->query("SELECT lc.id_livreur FROM livreurs_connexion lc WHERE DATE(date_connexion) =  DATE( NOW() ) AND TIME(date_connexion) < '14:00:00' ");
        $array_exceptions = $exceptions->fetchAll(PDO::FETCH_OBJ);


        $all_id = "";
        for ($i= 0; isset($array_exceptions[$i]); $i++) {
            if ($i != (count($array_exceptions) -1)) {
                $all_id .= $array_exceptions[$i]->id_livreur . ", ";
            }
            else {
                $all_id .= $array_exceptions[$i]->id_livreur;
            }
        }
        if (!empty($all_id))
            $add_exception = "AND li.id_livreur NOT in (" . $all_id . ")";
        else
            $add_exception = '';

        $result = $this->_sql->query("SELECT li.*, r.nom as nom_resto, r.adresse as adresse_resto, l.nom as nom_livreur, l.prenom as prenom_livreur, v.immatriculation as immat_vehicule
                                      FROM livreurs_planning li
                                      LEFT JOIN restaurants r ON li.id_commercant=r.id
                                      LEFT JOIN livreurs l ON li.id_livreur=l.id
                                      LEFT JOIN vehicules v ON li.id_vehicule=v.id
                                      WHERE DATE(date_debut) = DATE( NOW() ) AND TIME(date_debut) < '14:00:00' 
                                      AND r.nom != 'You Order Garage' " . $add_exception .$_SESSION["req_resto"]."
                                      ORDER BY li.date_debut");
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }

    public function getPlanningTwo($id_livreur, $date_debut, $date_fin, $id_vehicule=null, $id_commercant=null) {
        $exceptions = $this->_sql->query("SELECT lc.id_livreur FROM livreurs_connexion lc WHERE DATE(date_connexion) =  DATE( NOW() ) AND TIME(date_connexion) > '13:40:00' ");
        $array_exceptions = $exceptions->fetchAll(PDO::FETCH_OBJ);


        $all_id = "";
        for ($i= 0; isset($array_exceptions[$i]); $i++) {
            if ($i != (count($array_exceptions) -1)) {
                $all_id .= $array_exceptions[$i]->id_livreur . ", ";
            }
            else {
                $all_id .= $array_exceptions[$i]->id_livreur;
            }
        }
        if (!empty($all_id))
            $add_exception = "AND li.id_livreur NOT in (" . $all_id . ")";
        else
            $add_exception = '';



        $result = $this->_sql->query("SELECT li.*, r.nom as nom_resto, r.adresse as adresse_resto, l.nom as nom_livreur, l.prenom as prenom_livreur, v.immatriculation as immat_vehicule
                                      FROM livreurs_planning li
                                      LEFT JOIN restaurants r ON li.id_commercant=r.id
                                      LEFT JOIN livreurs l ON li.id_livreur=l.id
                                      LEFT JOIN vehicules v ON li.id_vehicule=v.id
                                      WHERE DATE(date_debut) = DATE( NOW() )
                                      AND TIME(date_debut) >= '14:00:00' AND r.nom != 'You Order Garage'  " . $add_exception .$_SESSION["req_resto"]."
                                      ORDER BY li.date_debut");
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }


    //liste de tous les Shifts affectés à youOrder Garage Seulement
    public function getDispoOne($id_livreur, $date_debut, $date_fin, $id_vehicule=null, $id_commercant=null) {
        $req_sup="";
        if ($id_livreur!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_livreur);
        }
        if ($id_vehicule!==null && $id_vehicule!="") {
            $req_sup.=" AND l.id_vehicule=".$this->_sql->quote($id_vehicule);
        }
        if ($id_commercant!==null && $id_commercant!="") {
            $req_sup.=" AND l.id_commercant=".$this->_sql->quote($id_commercant);
        }
        if ($date_debut==$date_fin) {
            $heure_fin="23:59:59";
        }
        else {
            $heure_fin="00:00:00";
        }

        $result = $this->_sql->query("SELECT l.*, r.nom as nom_resto, r.adresse as adresse_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.immatriculation as immat_vehicule FROM livreurs_planning l LEFT JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN vehicules v ON l.id_vehicule=v.id WHERE DATE(date_debut) = DATE( NOW() ) AND TIME(date_debut) < '14:00:00' AND r.nom = 'You Order Garage'  ORDER BY l.date_debut");
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }

    public function getDispoTwo($id_livreur, $date_debut, $date_fin, $id_vehicule=null, $id_commercant=null) {
        $req_sup="";
        if ($id_livreur!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_livreur);
        }
        if ($id_vehicule!==null && $id_vehicule!="") {
            $req_sup.=" AND l.id_vehicule=".$this->_sql->quote($id_vehicule);
        }
        if ($id_commercant!==null && $id_commercant!="") {
            $req_sup.=" AND l.id_commercant=".$this->_sql->quote($id_commercant);
        }
        if ($date_debut==$date_fin) {
            $heure_fin="23:59:59";
        }
        else {
            $heure_fin="00:00:00";
        }

        $result = $this->_sql->query("SELECT l.*, r.nom as nom_resto, r.adresse as adresse_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.immatriculation as immat_vehicule FROM livreurs_planning l LEFT JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN vehicules v ON l.id_vehicule=v.id WHERE DATE(date_debut) = DATE( NOW() ) AND TIME(date_debut) >= '14:00:00' AND r.nom = 'You Order Garage'  AND li.statut = 'OFF' ORDER BY l.date_debut");
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }

    public function getAttente() {

        $result = $this->_sql->query("SELECT l.* FROM livreurs l LEFT JOIN livreurs_planning lp ON l.id = lp.id_livreur LEFT JOIN restaurants r ON lp.id_commercant = r.id WHERE l.etat = 'en attente'".$_SESSION["req_resto"]. "GROUP BY l.id");
        $_liste = $result->fetchAll(PDO::FETCH_OBJ);
        return $_liste;
    }

    //liste de tous les livreurs ayant un shift (liste deroulante)
    public function getAllOne($id_livreur)
    {
        $req_sup = "";
        $req_limit = "";

        if ($id_livreur!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_livreur);
        }


        $result = $this->_sql->query("SELECT l.*, li.nom as nom_livreur, li.prenom as prenom_livreur
FROM livreurs_planning l 
LEFT JOIN restaurants r ON l.id_commercant=r.id 
LEFT JOIN livreurs li ON l.id_livreur=li.id 
LEFT JOIN vehicules v ON l.id_vehicule=v.id 
WHERE DATE(date_debut) = DATE( NOW() )
AND TIME(date_debut) < '14:00:00'".$_SESSION["req_resto"]."
ORDER BY li.prenom".$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeLivreur
        $_listeLivreur = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeLivreur;


    }

    public function getAllTwo($id_livreur)
    {
        $req_sup = "";
        $req_limit = "";

        if ($id_livreur!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_livreur);
        }


        $result = $this->_sql->query("SELECT l.*, li.nom as nom_livreur, li.prenom as prenom_livreur FROM livreurs_planning l  LEFT JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN vehicules v ON l.id_vehicule=v.id WHERE DATE(date_debut) = DATE( NOW() ) AND TIME(date_debut) >= '14:00:00' ".$_SESSION["req_resto"]." ORDER BY li.prenom".$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeLivreur
        $_listeLivreur = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeLivreur;
    }



    public function getPlanningPresence($id_livreur, $date_debut, $date_fin) {
        $req_sup="";
        if ($date_fin!="") {
            if ($date_debut==$date_fin) {
                $heure_fin="23:59:59";
            }
            else {
                $heure_fin="00:00:00";
            }
            $req_sup=" AND l.date_connexion BETWEEN ".$this->_sql->quote($date_debut. " 00:00:00")." AND ".$this->_sql->quote($date_fin." ".$heure_fin);
        }
        else {
            $req_sup=" AND l.date_connexion <".$this->_sql->quote($date_debut);
        }

        $result = $this->_sql->query("SELECT l.*, r.nom as nom_resto, r.adresse as adresse_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.nom as nom_vehicule FROM livreurs_connexion l LEFT JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN vehicules v ON l.id_vehicule=v.id WHERE l.id_livreur=".$this->_sql->quote($id_livreur)." ".$req_sup." ORDER BY date_connexion");
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }

    
    public function getHoursPresence($id_livreur, $date_debut, $date_fin) {
        $req_sup="";
        if ($date_fin!="") {
            if ($date_debut==$date_fin) {
                $heure_fin="23:59:59";
            }
            else {
                $heure_fin="00:00:00";
            }
            $req_sup=" AND date_connexion BETWEEN ".$this->_sql->quote($date_debut." 00:00:00")." AND ".$this->_sql->quote($date_fin." ".$heure_fin);
        }
        else {
            $req_sup=" AND date_connexion <".$this->_sql->quote($date_debut);
        }

        $result         = $this->_sql->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(date_deconnexion, date_connexion)))) AS total_hours FROM livreurs_connexion WHERE type='appli' AND id_livreur=".$this->_sql->quote($id_livreur)." ".$req_sup);
        $_listeHours    = $result->fetchAll(PDO::FETCH_OBJ);
        if ($_listeHours[0]->total_hours==null || $_listeHours[0]->total_hours=="") {
            return "0h00";
        }
        else {
            return date("H\hi", strtotime($_listeHours[0]->total_hours));
        }
        
    }

    public function getHoursSup($id_livreur, $date_debut, $date_fin) {
        $req_sup="";
        if ($date_fin!="") {
            if ($date_debut==$date_fin) {
                $heure_fin="23:59:59";
            }
            else {
                $heure_fin="00:00:00";
            }
            $req_sup=" AND date_connexion BETWEEN ".$this->_sql->quote($date_debut." 00:00:00")." AND ".$this->_sql->quote($date_fin." ".$heure_fin);
        }
        else {
            $req_sup=" AND date_connexion <".$this->_sql->quote($date_debut);
        }

        $result = $this->_sql->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(date_deconnexion, date_connexion)))) AS total_hours FROM livreurs_connexion WHERE type='manuel' AND id_livreur=".$this->_sql->quote($id_livreur)." ".$req_sup);
        $_listeHours = $result->fetchAll(PDO::FETCH_OBJ);
        if ($_listeHours[0]->total_hours==null || $_listeHours[0]->total_hours=="") {
            return "0h00";
        }
        else {
            return date("H\hi", strtotime($_listeHours[0]->total_hours));
        }
    }

    public function getHoursAffecte($id_livreur, $date_debut, $date_fin) {
        $req_sup="";
        if ($date_fin!="") {
            if ($date_debut==$date_fin) {
                $heure_fin="23:59:59";
            }
            else {
                $heure_fin="00:00:00";
            }
            $req_sup=" AND date_debut BETWEEN ".$this->_sql->quote($date_debut. " 00:00:00")." AND ".$this->_sql->quote($date_fin." ".$heure_fin);
        }
        else {
            $req_sup=" AND date_debut <".$this->_sql->quote($date_debut);
        }

        $result = $this->_sql->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(date_fin, date_debut)))) AS total_hours FROM livreurs_planning WHERE id_livreur=".$this->_sql->quote($id_livreur)." ".$req_sup);
        $_listeHours = $result->fetchAll(PDO::FETCH_OBJ);
        //return date("H\hi", strtotime($_listeHours[0]->total_hours));
        return $_listeHours[0]->total_hours;
        /*$result_heure=explode(":", $_listeHours[0]->total_hours);
        return $result_heure[0]."h".$result_heure[1];*/
    }

    public function setPlanning($id_livreur, $id_commercant, $id_vehicule, $date_debut, $date_fin, $recurrence) {
        if ($id_vehicule=="") {
            $id_vehicule=0;
        }
        if ($id_livreur=="") {
            $id_livreur=0;
        }
        $result = $this->_sql->exec("INSERT INTO livreurs_planning (id_livreur, id_commercant, id_vehicule, date_debut, date_fin, recurrence) VALUES (".$this->_sql->quote($id_livreur).", ".$this->_sql->quote($id_commercant).", ".$this->_sql->quote($id_vehicule).", ".$this->_sql->quote($date_debut).", ".$this->_sql->quote($date_fin).", ".$this->_sql->quote($recurrence).")");
        if ($_SESSION["restaurateur"]) {
            $result = $this->_sql->exec("INSERT INTO notifications (id_commande, id_commercant, type, date) VALUES (0,".$this->_sql->quote($id_commercant).", 'planning_ajout', NOW())");
        }
    }


    public function replacePlanning($id_p1, $id_livreur1, $id_livreur2, $id_adminL){
        if (isset($id_livreur1) && !is_null($id_livreur1)
            && isset($id_livreur2) && !is_null($id_livreur2)) {

            $now = new DateTime("now");
            $date = $now->format('Y-m-d H:i:s');
            $planning2  = $this->_sql->exec("UPDATE livreurs_planning SET id_livreur=" . $this->_sql->quote($id_livreur1) . ", last_update=" . $this->_sql->quote($date) . ", last_id_livreur=" . $this->_sql->quote($id_livreur2) . ", id_adminL=" . $this->_sql->quote($id_adminL) ." WHERE id_livreur=" . $this->_sql->quote($id_livreur2) . "AND DATE(date_debut)= DATE( NOW() )") ;
            $planning1  = $this->_sql->exec("UPDATE livreurs_planning SET id_livreur=" . $this->_sql->quote($id_livreur2) . ", last_update=" . $this->_sql->quote($date) . ", last_id_livreur=" . $this->_sql->quote($id_livreur1) . ", id_adminL=" . $this->_sql->quote($id_adminL) ." WHERE id=" . $this->_sql->quote($id_p1));

            return 1;
        }
        return 0;

    }

    public function setPlanningPresence($id_livreur, $id_commercant, $id_vehicule, $date_debut, $date_fin, $type) {
        if ($id_vehicule=="") $id_vehicule=0;
        $result = $this->_sql->exec("INSERT INTO livreurs_connexion (id_planning, id_livreur, id_commercant, id_vehicule, date_connexion, date_deconnexion, type) VALUES ('0' ,".$this->_sql->quote($id_livreur).", ".$this->_sql->quote($id_commercant).", ".$this->_sql->quote($id_vehicule).", ".$this->_sql->quote($date_debut).", ".$this->_sql->quote($date_fin).", ".$this->_sql->quote($type).")");
        return "INSERT INTO livreurs_connexion (id_planning, id_livreur, id_commercant, id_vehicule, date_connexion, date_deconnexion, type) VALUES ('0' ,".$this->_sql->quote($id_livreur).", ".$this->_sql->quote($id_commercant).", ".$this->_sql->quote($id_vehicule).", ".$this->_sql->quote($date_debut).", ".$this->_sql->quote($date_fin).", ".$this->_sql->quote($type).")";
    }

    public function setLivreurOnline($id_planning, $id_livreur, $id_commercant, $id_vehicule, $date_debut, $date_fin, $type) {
        if ($id_vehicule=="") $id_vehicule=0;
        try {
            $result = $this->_sql->exec("INSERT INTO livreurs_connexion (id_planning, id_livreur, id_commercant, id_vehicule, date_connexion, date_deconnexion, type) VALUES (".$this->_sql->quote($id_planning) . ", ".$this->_sql->quote($id_livreur).", ".$this->_sql->quote($id_commercant).", ".$this->_sql->quote($id_vehicule).", ".$this->_sql->quote($date_debut->format('Y-m-d H:i:s')).", ".$this->_sql->quote($date_fin->format('Y-m-d H:i:s')).", ".$this->_sql->quote($type).")");
            $result2 = $this->_sql->exec("UPDATE livreurs SET statut = 'ON' WHERE id =".$this->_sql->quote($id_livreur));
            $status = "ok";
        } catch (PDOException $e) {
            $status = "error";
        }

        return $status;
    }

    public function setLivreurAttente($id_livreur) {
        try {
            $result = $this->_sql->exec("UPDATE livreurs SET etat = 'en attente' WHERE id =".$this->_sql->quote($id_livreur));
            $result2 = $this->_sql->exec("UPDATE livreurs_planning SET id_livreur = '0' WHERE id_livreur =".$this->_sql->quote($id_livreur). " AND date_debut >= DATE_FORMAT(NOW(), \"%Y-%m-%d 00:00:00\") AND date_fin <= DATE_FORMAT(NOW(), \"%Y-%m-%d 23:59:59\");");
            $status = "ok";
        } catch (PDOException $e) {
            $status = "error";
        }

        return $status;
    }

    public function getPlanningFiche($id) {
        $result = $this->_sql->query("SELECT l.*, r.nom as nom_resto, r.adresse as adresse_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.nom as nom_vehicule FROM livreurs_planning l LEFT JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN vehicules v ON l.id_vehicule=v.id WHERE l.id=".$this->_sql->quote($id));
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }

    public function updatePlanningFiche($id, $id_vehicule, $id_livreur, $id_commercant, $id_adminV, $date_debut, $date_fin) {

        if ($id_vehicule=="") {
            $id_vehicule=0;
        }
        if ($id_livreur=="") {
            $id_livreur=0;
        }
        $now = new DateTime("now");
        $date = $now->format('Y-m-d H:i:s');
        $result = $this->_sql->exec("UPDATE livreurs_planning SET id_livreur=".$this->_sql->quote($id_livreur).", id_vehicule=".$this->_sql->quote($id_vehicule).", id_commercant=".$this->_sql->quote($id_commercant).", id_adminV=".$this->_sql->quote($id_adminV).", date_debut=".$this->_sql->quote($date_debut).", date_fin=".$this->_sql->quote($date_fin).", attribution_vehicule=".$this->_sql->quote($date)." WHERE id=".$this->_sql->quote($id));

        if ($_SESSION["restaurateur"]) {
            $result = $this->_sql->exec("INSERT INTO notifications (id_commande, id_commercant, type, date) VALUES (0,".$this->_sql->quote($id_commercant).", 'planning_modif', NOW())");
        }
    }

    public function deletePlanningFiche($id) {
        $result = $this->_sql->exec("DELETE FROM livreurs_planning WHERE id=".$this->_sql->quote($id));
    }

    public function checkLivreur($id_livreur, $id_vehicule, $date_debut, $date_fin, $type) {
        $check_livreur=false;
        $req_sup=" ";
        /*if ($id_vehicule!="" && $id_vehicule!=0) {
            $req_sup=" AND id_vehicule!=".$this->_sql->quote($id_vehicule);
        }*/
        $result = $this->_sql->query("SELECT * FROM livreurs_connexion WHERE id_livreur=".$this->_sql->quote($id_livreur)." ".$req_sup." AND ".$this->_sql->quote($date_debut)."< date_deconnexion AND ".$this->_sql->quote($date_fin)."> date_connexion");
        $ligne = $result->fetch();
        if ($ligne) {
            $check_livreur=true;
        }
        $result = $this->_sql->query("SELECT * FROM livreurs_planning WHERE id_livreur=".$this->_sql->quote($id_livreur)." ".$req_sup." AND ".$this->_sql->quote($date_debut)."< date_fin AND ".$this->_sql->quote($date_fin).">date_debut");
        $ligne = $result->fetch();
        if ($ligne) {
            if ($type=="insert" || ($type=="update" && $ligne["id_livreur"]!=$id_livreur)) {
                $check_livreur=true;
            }            
        }
        return $check_livreur;        
    }

    public function getListeLivreur($id_livreur, $date_debut, $date_fin, $id_vehicule=null, $id_commercant=null) {
        $req_sup="";
        if ($id_livreur!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_livreur);
        }
        if ($id_vehicule!==null && $id_vehicule!="") {
            $req_sup.=" AND l.id_vehicule=".$this->_sql->quote($id_vehicule);
        }
        if ($id_commercant!==null && $id_commercant!="") {
            $req_sup.=" AND l.id_commercant=".$this->_sql->quote($id_commercant);
        } 
        if ($date_debut==$date_fin) {
            $heure_fin="23:59:59";
        }
        else {
            $heure_fin="00:00:00";
        }

        $result = $this->_sql->query("SELECT ANY_VALUE(l.id_livreur), li.id as id_livreur, li.statut, li.nb_heures, li.telephone, li.nom as nom_livreur, li.prenom as prenom_livreur FROM livreurs_planning l LEFT JOIN livreurs li ON l.id_livreur=li.id WHERE l.id_livreur!=0 AND l.date_debut BETWEEN ".$this->_sql->quote($date_debut." 00:00:00")." AND ".$this->_sql->quote($date_fin." ".$heure_fin)." ".$req_sup." GROUP BY l.id_livreur");
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }

    public function getListeLivreurNb($id_livreur, $date_debut, $date_fin, $id_vehicule=null, $id_commercant=null) {
        $req_sup="";
        if ($id_livreur!="") {
            $req_sup.=" AND id_livreur=".$this->_sql->quote($id_livreur);
        }
        if ($id_vehicule!==null && $id_vehicule!="") {
            $req_sup.=" AND id_vehicule=".$this->_sql->quote($id_vehicule);
        }
        if ($id_commercant!==null && $id_commercant!="") {
            $req_sup.=" AND id_commercant=".$this->_sql->quote($id_commercant);
        }
        if ($date_debut==$date_fin) {
            $heure_fin="23:59:59";
        }
        else {
            $heure_fin="00:00:00";
        }

        $result = $this->_sql->query("SELECT COUNT(DISTINCT id_livreur) as NB FROM livreurs_planning WHERE id_livreur!=0 AND date_debut BETWEEN ".$this->_sql->quote($date_debut." 00:00:00")." AND ".$this->_sql->quote($date_fin." ".$heure_fin)." ".$req_sup);
        $_listePlanningNb = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanningNb[0]->NB;
    }

    public function dupliquer($id_livreur, $date_debut, $date_fin, $date_debut_new) {
        $week_diff=datediff('ww', $date_debut, $date_debut_new, false);
        $continu=true;

        $result = $this->_sql->query("SELECT * FROM livreurs_planning WHERE id_livreur=".$this->_sql->quote($id_livreur)." AND date_debut BETWEEN ".$this->_sql->quote($date_debut." 00:00:00")." AND ".$this->_sql->quote($date_fin." 23:59:59"));
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);

        foreach ($_listePlanning as $planning) {
            $newdate_debut = strtotime('+ '.$week_diff." week" ,strtotime($planning->date_debut)) ;
            $newdate_debut = date('Y-m-d', $newdate_debut)." ".date("H:i", strtotime($planning->date_debut));
            $newdate_fin = strtotime('+ '.$week_diff." week" ,strtotime($planning->date_fin)) ;
            $newdate_fin = date('Y-m-d', $newdate_fin)." ".date("H:i", strtotime($planning->date_fin));

            if ($this->checkLivreur($id_livreur, 0, $newdate_debut, $newdate_fin, "insert")) {
                $continu=false;
            }
        }

        if ($continu) {
            foreach ($_listePlanning as $planning) {
                $newdate_debut = strtotime('+ '.$week_diff." week" ,strtotime($planning->date_debut)) ;
                $newdate_debut = date('Y-m-d', $newdate_debut)." ".date("H:i", strtotime($planning->date_debut));
                $newdate_fin = strtotime('+ '.$week_diff." week" ,strtotime($planning->date_fin)) ;
                $newdate_fin = date('Y-m-d', $newdate_fin)." ".date("H:i", strtotime($planning->date_fin));

                $result = $this->_sql->exec("INSERT INTO livreurs_planning (id_livreur, id_commercant, id_vehicule, date_debut, date_fin, recurrence) VALUES (".$this->_sql->quote($planning->id_livreur).", ".$this->_sql->quote($planning->id_commercant).", ".$this->_sql->quote($planning->id_vehicule).", ".$this->_sql->quote($newdate_debut).", ".$this->_sql->quote($newdate_fin).", ".$this->_sql->quote($planning->recurrence).")");
            }
            echo "ok";
        }
        else {
            echo "ko";
        }
    }

    public function getFullPlanning($date_debut, $date_fin) {
        $req_sup="";

        if ($date_debut==$date_fin) {
            $heure_fin=" 23:59:59";
        }
        else {
            $heure_fin=" 00:00:00";
        }

        $result = $this->_sql->query("SELECT l.id, li.nom as nom_livreur, li.prenom as prenom_livreur, r.nom as nom_resto, v.nom as nom_vehicule, l.date_debut, l.date_fin, ANY_VALUE(c.date_connexion) as date_connexion, ANY_VALUE(c.date_deconnexion) as date_deconnexion FROM livreurs_planning l LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN livreurs_connexion c ON l.id=c.id_planning INNER JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN vehicules v ON v.id=l.id_vehicule WHERE l.id_livreur!=0 AND date_debut BETWEEN '".$date_debut." 00:00:00' AND '".$date_fin.$heure_fin."' GROUP BY l.id ORDER BY l.date_debut");
        $_listeFullPlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeFullPlanning;
    }

    public function verifLivreur($nom_livreur, $prenom_livreur) {
        $result = $this->_sql->query("SELECT id FROM livreurs WHERE nom=".$this->_sql->quote($nom_livreur)." AND prenom=".$this->_sql->quote($prenom_livreur));
        $ligne = $result->fetch();
        if($ligne){
            return $ligne["id"];
        }
        return false;
    }


    public function setHistoriquePlanning($id, $id_vehicule, $id_livreur, $id_commercant, $id_admin, $date_debut, $date_fin, $last_update){
        if ($id_vehicule=="") {
            $id_vehicule=0;
        }

        if ($id_livreur=="") {
            $id_livreur=0;
        }
        $result = $this->_sql->exec("UPDATE livreurs_planning SET id_livreur=".$this->_sql->quote($id_livreur).", id_vehicule=".$this->_sql->quote($id_vehicule).", id_commercant=".$this->_sql->quote($id_commercant).", id_admin=".$this->_sql->quote($id_admin).", date_debut=".$this->_sql->quote($date_debut).", date_fin=".$this->_sql->quote($date_fin).", last_update=".$this->_sql->quote($last_update)." WHERE id=".$this->_sql->quote($id). "AND last_update= NOW()");

    }


    public function getHistoriquePlanningOne($id_planning){

        $req_sup = "";
        $req_limit = "";

        if ($id_planning!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_planning);
        }

        $result = $this->_sql->query("SELECT li.*, r.nom as nom_resto, r.adresse AS adresse_resto, l.nom as nom_livreur, l.prenom as prenom_livreur, ol.nom as lastnom_livreur, ol.prenom as lastprenom_livreur, u.nom as nom_admin, u.prenom as prenom_admin
        FROM livreurs_planning li
        LEFT JOIN livreurs l ON li.id_livreur=l.id
        LEFT JOIN livreurs ol ON li.last_id_livreur=ol.id
        LEFT JOIN restaurants r ON li.id_commercant=r.id
        LEFT JOIN utilisateurs u ON li.id_adminL=u.id
        WHERE DATE(li.last_update) = DATE( NOW() )
        AND DATE(date_debut) = DATE( NOW() )
        AND li.last_id_livreur != '0'
        AND TIME(li.date_debut) < '14:00:00' ".$_SESSION["req_resto"]."
        ORDER BY TIME(li.last_update) DESC ");

        // Récupération des résultats sélectionnés dans le tableau $_listeLivreur
        $_historiquePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_historiquePlanning;
    }

    public function getHistoriquePlanningTwo($id_planning){

        $req_sup = "";
        $req_limit = "";

        if ($id_planning!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_planning);
        }

        $result = $this->_sql->query("SELECT li.*, r.nom as nom_resto, l.nom as nom_livreur, l.prenom as prenom_livreur, ol.nom as lastnom_livreur, ol.prenom as lastprenom_livreur, u.nom as nom_admin, u.prenom as prenom_admin
        FROM livreurs_planning li
        LEFT JOIN livreurs l ON li.id_livreur=l.id
        LEFT JOIN livreurs ol ON li.last_id_livreur=ol.id
        LEFT JOIN restaurants r ON li.id_commercant=r.id
        LEFT JOIN utilisateurs u ON li.id_adminL=u.id
        WHERE DATE(li.last_update) = DATE( NOW() )
        AND DATE(date_debut) = DATE( NOW() )
        AND li.last_id_livreur != '0'
        AND TIME(li.date_debut) >= '14:00:00' ".$_SESSION["req_resto"]."
        ORDER BY TIME(li.last_update) DESC ");

        // Récupération des résultats sélectionnés dans le tableau $_listeLivreur
        $_historiquePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_historiquePlanning;
    }


    public function getAttributionVehiculeOne($id_planning){

        $req_sup = "";
        $req_limit = "";

        if ($id_planning!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_planning);
        }

        $result = $this->_sql->query("SELECT li.*, l.nom as nom_livreur, l.prenom as prenom_livreur, v.immatriculation, u.nom as nom_admin, u.prenom as prenom_admin, r.nom as nom_resto
        FROM livreurs_planning li
        LEFT JOIN livreurs l ON li.id_livreur=l.id
        LEFT JOIN vehicules v ON li.id_vehicule=v.id
        LEFT JOIN utilisateurs u ON li.id_adminV=u.id
        LEFT JOIN restaurants r ON li.id_commercant=r.id
        WHERE DATE(li.date_debut) = DATE( NOW() )
        AND id_vehicule != '0'
        AND TIME(li.date_debut) < '14:00:00' ".$_SESSION["req_resto"]."
        ORDER BY TIME(li.attribution_vehicule) DESC");

        $_attributionVehicule = $result->fetchAll(PDO::FETCH_OBJ);
        return $_attributionVehicule;
    }

    public function getAttributionVehiculeTwo($id_planning){

        $req_sup = "";
        $req_limit = "";

        if ($id_planning!="") {
            $req_sup.=" AND l.id_livreur=".$this->_sql->quote($id_planning);
        }

        $result = $this->_sql->query("SELECT li.*, l.nom as nom_livreur, l.prenom as prenom_livreur, v.immatriculation, u.nom as nom_admin, u.prenom as prenom_admin, r.nom AS nom_resto
        FROM livreurs_planning li
        LEFT JOIN livreurs l ON li.id_livreur=l.id
        LEFT JOIN vehicules v ON li.id_vehicule=v.id
        LEFT JOIN utilisateurs u ON li.id_adminV=u.id
        LEFT JOIN restaurants r ON li.id_commercant=r.id
        WHERE DATE(li.date_debut) = DATE( NOW() )
        AND id_vehicule != '0'
        AND TIME(li.date_debut) >= '14:00:00' ".$_SESSION["req_resto"]."
        ORDER BY TIME(li.attribution_vehicule) DESC");

        $_attributionVehicule = $result->fetchAll(PDO::FETCH_OBJ);
        return $_attributionVehicule;
    }


    public function getConnexionOne($page, $nbmess, $nom, $statut){

        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

        $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($nom!="") {
            $req_sup.=" AND l.nom LIKE '%".$nom."%'";
        }
        if ($statut!="") {
            $req_sup.=" AND l.statut ='".$statut."'";
        }

        $result = $this->_sql->query("SELECT l.nom as nom_livreur, l.prenom AS prenom_livreur, lc.date_connexion 
FROM livreurs_connexion lc 
LEFT JOIN livreurs l          ON l.id=lc.id_livreur
LEFT JOIN livreurs_planning lp ON lp.id = lc.id_planning
LEFT JOIN restaurants r ON r.id = lc.id_commercant
WHERE DATE (lc.date_connexion) = DATE( NOW() )
AND TIME(lp.date_debut) < '14:00:00' ".$_SESSION["req_resto"]."
ORDER BY TIME(lc.date_connexion) DESC ".$req_sup);
        $_listeLivreur = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeLivreur;

    }

    public function getConnexionTwo($page, $nbmess, $nom, $statut){

        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($nom!="") {
            $req_sup.=" AND l.nom LIKE '%".$nom."%'";
        }
        if ($statut!="") {
            $req_sup.=" AND l.statut ='".$statut."'";
        }

        $result = $this->_sql->query("SELECT l.nom as nom_livreur, l.prenom AS prenom_livreur, lc.date_connexion 
                                        FROM livreurs_connexion lc 
                                        LEFT JOIN livreurs l          ON l.id=lc.id_livreur
                                        LEFT JOIN livreurs_planning lp ON lp.id = lc.id_planning
                                        LEFT JOIN restaurants r ON r.id = lc.id_commercant
                                        WHERE DATE (lc.date_connexion) = DATE( NOW() )
                                        AND TIME(lp.date_debut) >= '14:00:00' ".$_SESSION["req_resto"]."
                                        ORDER BY TIME(lc.date_connexion) DESC".$req_sup);
        $_listeLivreur = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeLivreur;

    }


    public function getShiftByCommercant($id_commercant, $today = false){

        if($today == true){
            $result = $this->_sql->query("SELECT lp.id AS matricule, lp.id_livreur, r.nom, l.prenom AS 'prenom_livreur', l.nom AS 'nom_livreur', lp.date_debut AS 'debut', lp.date_fin AS 'fin', lc.date_connexion AS 'connexion', TIMESTAMPDIFF(MINUTE, lp.date_debut, lc.date_connexion) AS 'retard', TIMEDIFF(lc.date_deconnexion, lc.date_connexion) AS 'travail'
                                              FROM livreurs_planning lp
                                              LEFT JOIN restaurants r ON r.id = lp.id_commercant
                                              LEFT JOIN livreurs l ON l.id = lp.id_livreur
                                              LEFT JOIN livreurs_connexion lc ON lc.id_planning = lp.id
                                              WHERE r.id =".$this->_sql->quote($id_commercant)."
                                              AND DATE(lp.date_debut) = DATE ( NOW() )
                                              ORDER BY lp.date_debut");

            $_getShift = $result->fetchAll(PDO::FETCH_OBJ);
            return $_getShift;

        }

        $result = $this->_sql->query("SELECT lp.id AS matricule, lp.id_livreur, r.nom, l.prenom AS 'prenom_livreur', l.nom AS 'nom_livreur', lp.date_debut AS 'debut', lp.date_fin AS 'fin', lc.date_connexion AS 'connexion', TIMESTAMPDIFF(MINUTE, lp.date_debut, lc.date_connexion) AS 'retard', TIMEDIFF(lc.date_deconnexion, lc.date_connexion) AS 'travail'
                                      FROM livreurs_planning lp
                                      LEFT JOIN restaurants r ON r.id = lp.id_commercant
                                      LEFT JOIN livreurs l ON l.id = lp.id_livreur
                                      LEFT JOIN livreurs_connexion lc ON lc.id_planning = lp.id
                                      WHERE r.id =".$this->_sql->quote($id_commercant)."
                                      AND lp.date_debut BETWEEN DATE_FORMAT(NOW(), '%Y-%m-01') AND LAST_DAY(NOW())
                                      ORDER BY lp.date_debut");

        $_getShift = $result->fetchAll(PDO::FETCH_OBJ);
        return $_getShift;
    }

    public function getShiftByLivreur($id_livreur, $today = false){

        if ($today == true){
            $result1 = $this->_sql->query("SELECT lp.*, r.nom as nom_resto, r.adresse as adresse_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.immatriculation as nom_vehicule
                                            FROM livreurs_planning lp
                                            LEFT JOIN restaurants r ON lp.id_commercant=r.id
                                            LEFT JOIN livreurs li ON lp.id_livreur=li.id
                                            LEFT JOIN vehicules v ON lp.id_vehicule=v.id
                                            WHERE li.id =".$this->_sql->quote($id_livreur)."
                                            AND DATE(lp.date_debut) = DATE ( NOW() )
                                            ORDER BY lp.date_debut");

            $_getShift = $result1->fetchAll(PDO::FETCH_OBJ);
            return $_getShift;
        }

        $result = $this->_sql->query("SELECT lp.*, r.nom as nom_resto, r.adresse as adresse_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.immatriculation as nom_vehicule
                                        FROM livreurs_planning lp
                                        LEFT JOIN restaurants r ON lp.id_commercant=r.id
                                        LEFT JOIN livreurs li ON lp.id_livreur=li.id
                                        LEFT JOIN vehicules v ON lp.id_vehicule=v.id
                                        WHERE li.id =".$this->_sql->quote($id_livreur)."
                                        AND lp.date_debut BETWEEN DATE_FORMAT(NOW(), '%Y-%m-01') AND LAST_DAY(NOW())
                                        ORDER BY lp.date_debut");

        $_getShift = $result->fetchAll(PDO::FETCH_OBJ);
        return $_getShift;
    }



    public function getPresence($page, $nbmess, $id_livreur, $today = false){

        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($today == true){
            $result1 = $this->_sql->query("SELECT    lc.id_planning AS matricule, 
                                                r.nom AS 'libelle_commercant',
                                                r.adresse AS 'adresse',
                                                l.prenom AS 'prenom_livreur',
                                                l.nom AS 'nom_livreur',
                                                v.immatriculation AS 'nom_vehicule',
                                                lp.date_debut AS 'debut',
                                                lp.date_fin AS 'fin',
                                                lc.date_connexion AS 'connexion',
                                                TIMESTAMPDIFF(MINUTE, lp.date_debut, lc.date_connexion) AS 'retard',
                                                TIMEDIFF(lc.date_deconnexion, lc.date_connexion) AS 'travail'
                                      FROM livreurs_connexion lc
                                      LEFT JOIN restaurants r ON r.id = lc.id_commercant
                                      LEFT JOIN livreurs l ON l.id = lc.id_livreur
                                      LEFT JOIN livreurs_planning lp ON lp.id = lc.id_planning 
                                      LEFT JOIN vehicules v ON v.id = lc.id_vehicule
                                      WHERE l.id =".$this->_sql->quote($id_livreur)."
                                      AND DATE (lc.date_connexion) = DATE( NOW()) 
                                      ORDER BY lp.date_debut".$req_limit);
            $_getPresence = $result1->fetchAll(PDO::FETCH_OBJ);
            return $_getPresence;

        }

        $result = $this->_sql->query("SELECT    lc.id_planning AS matricule, 
                                                r.nom AS 'libelle_commercant',
                                                l.prenom AS 'prenom_livreur',
                                                l.nom AS 'nom_livreur',
                                                v.immatriculation AS 'nom_vehicule',
                                                lp.date_debut AS 'debut',
                                                lp.date_fin AS 'fin',
                                                lc.date_connexion AS 'connexion',
                                                TIMESTAMPDIFF(MINUTE, lp.date_debut, lc.date_connexion) AS 'retard',
                                                TIMEDIFF(lc.date_deconnexion, lc.date_connexion) AS 'travail'
                                      FROM livreurs_connexion lc
                                      LEFT JOIN restaurants r ON r.id = lc.id_commercant
                                      LEFT JOIN livreurs l ON l.id = lc.id_livreur
                                      LEFT JOIN livreurs_planning lp ON lp.id = lc.id_planning 
                                      LEFT JOIN vehicules v ON v.id = lc.id_vehicule
                                      WHERE l.id =".$this->_sql->quote($id_livreur)."
                                      AND lc.date_connexion BETWEEN DATE_FORMAT(NOW(), '%Y-%m-01') AND LAST_DAY(NOW())
                                      ORDER BY lp.date_debut".$req_limit);
        $_getPresence = $result->fetchAll(PDO::FETCH_OBJ);
        return $_getPresence;
    }

    public function getHoursMonth($id_livreur, $today = false) {

        if ($today == true){
            $result1 = $this->_sql->query("SELECT l.id, SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(lc.date_deconnexion, lc.date_connexion)))) AS 'total_hours'
                                              FROM livreurs_connexion lc 
                                              LEFT JOIN livreurs_planning lp ON lp.id = lc.id_planning
                                              LEFT JOIN livreurs l ON l.id = lc.id_livreur
                                              WHERE l.id=".$this->_sql->quote($id_livreur)."
                                              AND DATE(lc.date_connexion) = DATE( NOW() )
                                              AND lc.type='appli' ");

            $_listeHours    = $result1->fetchAll(PDO::FETCH_OBJ);
            if ($_listeHours[0]->total_hours==null || $_listeHours[0]->total_hours=="") {
                return "0h00";
            }
            else {
                echo $_listeHours[0]->total_hours;
            }


        }
        else {

            $result = $this->_sql->query("SELECT l.id, SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(lc.date_deconnexion, lc.date_connexion)))) AS 'total_hours'
                                              FROM livreurs_connexion lc 
                                              LEFT JOIN livreurs_planning lp ON lp.id = lc.id_planning
                                              LEFT JOIN livreurs l ON l.id = lc.id_livreur
                                              WHERE l.id=".$this->_sql->quote($id_livreur)."
                                              AND DATE(lc.date_connexion) BETWEEN DATE_FORMAT(NOW(), '%Y-%m-01') AND LAST_DAY( NOW() ) 
                                              AND lc.type='appli' ");

            $_listeHours    = $result->fetchAll(PDO::FETCH_OBJ);
            if ($_listeHours[0]->total_hours==null || $_listeHours[0]->total_hours=="") {
                return "0h00";
            }
            else {
                echo $_listeHours[0]->total_hours;
            }

        }


    }

    public function getHoursTheoriqueMonth($id_livreur) {

        $result = $this->_sql->query("SELECT l.id, SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(lp.date_fin, lp.date_debut)))) AS 'total_hours'
                                              FROM livreurs_planning lp
                                              LEFT JOIN livreurs l ON l.id = lp.id_livreur
                                              WHERE l.id=".$this->_sql->quote($id_livreur)."
                                              AND DATE(lp.date_debut) BETWEEN DATE_FORMAT(NOW(), '%Y-%m-01') AND LAST_DAY( NOW() ) ");

        $_listeHours    = $result->fetchAll(PDO::FETCH_OBJ);
        if ($_listeHours[0]->total_hours==null || $_listeHours[0]->total_hours=="") {
            return "0h00";
        }
        else {
            echo $_listeHours[0]->total_hours;
        }
    }

    public function getLicencie(){

        $result = $this->_sql->query("SELECT l.* FROM livreurs l WHERE statut='supprime'");

        $listeLicencie    = $result->fetchAll(PDO::FETCH_OBJ);
        return $listeLicencie;

    }


    public function getRecupLivreur($id_livreur) {
        $req_sup="";
        $result = $this->_sql->exec("UPDATE livreurs SET statut='OFF' WHERE id=".$this->_sql->quote($id_livreur));

    }

}
?>