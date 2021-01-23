<?php
class Vehicule {
    // déclaration des propriétés
    private $_id_vehicule;
    private $_type;
    private $_nom;
    private $_immatriculation;
    private $_kilometrage;
    private $_marque;
    private $_volume;
    private $_etat;
    private $_nbPages;
    private $_nbRes;
    private $_nbPagesPlanning;
    private $_nbResPlanning;

    private $_sql;

    private $_listeVehicule=array();
    private $_listePlanning=array();

    // déclaration des méthodes
    public function __construct($sql, $id = null) {
        $this->_sql=$sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM vehicules WHERE id=".$this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_type            = $ligne[0]->type;
            $this->_nom             = $ligne[0]->nom;
            $this->_immatriculation = $ligne[0]->immatriculation;
            $this->_kilometrage     = $ligne[0]->kilometrage;
            $this->_marque          = $ligne[0]->marque;
            $this->_volume          = $ligne[0]->volume;
            $this->_etat            = $ligne[0]->etat;
        }
    }

    public function getType() {
        return $this->_type;
    }

    public function getNom() {
        return $this->_nom;
    }

    public function getImmatriculation() {
        return $this->_immatriculation;
    }

    public function getKilometrage() {
        return $this->_kilometrage;
    }

    public function getMarque() {
        return $this->_marque;
    }

    public function getVolume() {
        return $this->_volume;
    }

    public function getEtat() {
        return $this->_etat;
    }

    public function getNbPages() {
        return $this->_nbPages;
    }

    public function getNbRes() {
        return $this->_nbRes;
    }

    public function getNbPagesPlanning() {
        return $this->_nbPagesPlanning;
    }

    public function getNbResPlanning() {
        return $this->_nbResPlanning;
    }

    public function getPagination($nbmess, $type, $immatriculation) {
        //compter le nb de pages et de résultats
        $req_sup="";
        if ($type=="Scooter") {
            $req_sup=" AND type='Scooter'";
        }
        else if ($type=="autre") {
            $req_sup=" AND type!='Scooter'";
        }
        if ($immatriculation!="") {
            $req_sup=" AND immatriculation LIKE '%".$immatriculation."%'";
        }

        $req = "SELECT count(*) as NB FROM vehicules WHERE etat!='supprime' ".$req_sup;
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

    public function getPaginationPlanning($nbmess, $id_vehicule) {
        //compter le nb de pages et de résultats
        $req = "SELECT count(*) as NB FROM vehicules_historique WHERE id_vehicule=".$this->_sql->quote($id_vehicule);
        $result = $this->_sql->query($req);
        $ligne = $result->fetch();
        if($ligne!=""){
            $this->_nbResPlanning = $ligne["NB"];
        }else{
            $this->_nbResPlanning = 0;
        }
        $this->_nbPagesPlanning = $this->_nbResPlanning/$nbmess;
        $this->_nbPagesPlanning = ceil($this->_nbPagesPlanning);
    }

    public function getAll($page, $nbmess, $type, $immatriculation, $working = null) {
        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($type=="Scooter") {
            $req_sup.=" AND type='Scooter'";
        }
        else if ($type=="autre") {
            $req_sup.=" AND type!='Scooter'";
        }
        if ($immatriculation!="") {
            $req_sup.=" AND immatriculation LIKE '%".$immatriculation."%'";
        }
        if ($working) {
            $req_sup.=" AND etat ='ok'";
        }

        $result = $this->_sql->query("SELECT * FROM vehicules WHERE etat!='supprime' ".$req_sup." ".$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeVehicule
        $_listeVehicule = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeVehicule;
    }

    public function getAllFree($date_debut, $date_fin, $id_vehicule) {
        $req_sup="";
        if ($id_vehicule!="" && $id_vehicule!=0) {
            $req_sup=" OR v.id=".$this->_sql->quote($id_vehicule);
        }

        $result = $this->_sql->query("SELECT v.* FROM vehicules v LEFT JOIN livreurs_planning p ON v.id=p.id_vehicule AND (".$this->_sql->quote($date_debut)."<p.date_fin AND ".$this->_sql->quote($date_fin).">p.date_debut) WHERE v.etat='ok' AND (p.id IS NULL OR (NOT (".$this->_sql->quote($date_debut)."<p.date_fin AND ".$this->_sql->quote($date_fin).">p.date_debut)) ".$req_sup.") GROUP BY v.id ORDER BY v.id");
        // Récupération des résultats sélectionnés dans le tableau $_listeVehicule
        $_listeVehicule = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeVehicule;
    }

    public function setVehicule($id_vehicule, $type, $type_autre, $nom, $immatriculation, $kilometrage, $marque, $volume, $etat) {
        if ($type=="Autre") {
            $type=$type_autre;
        }

        if ($id_vehicule=="") {
            $result = $this->_sql->exec("INSERT INTO vehicules (type, nom, immatriculation, kilometrage, marque, volume, etat) VALUES (".$this->_sql->quote($type).", ".$this->_sql->quote($nom).", ".$this->_sql->quote($immatriculation).", ".$this->_sql->quote($kilometrage).",".$this->_sql->quote($marque).", ".$this->_sql->quote($volume).", ".$this->_sql->quote($etat).")");
            $id_vehicule=$this->_sql->lastInsertId();
        }
        else {
            $result = $this->_sql->exec("UPDATE vehicules SET type=".$this->_sql->quote($type).", nom=".$this->_sql->quote($nom).", immatriculation=".$this->_sql->quote($immatriculation).", kilometrage=".$this->_sql->quote($kilometrage).", marque=".$this->_sql->quote($marque).", volume=".$this->_sql->quote($volume).", etat=".$this->_sql->quote($etat)." WHERE id=".$this->_sql->quote($id_vehicule));
        }
        return $id_vehicule;
    }

    public function getPlanning($id_vehicule,$date_debut, $date_fin, $nbmess=null, $page=null) {
        $req_limit="";
        if ($date_fin!="") {
            $req_sup=" AND vp.h_debut BETWEEN ".$this->_sql->quote($date_debut." 00:00:00")." AND ".$this->_sql->quote($date_fin." 23:59:59");
        }
        else {
            $req_sup=" AND vp.h_debut <".$this->_sql->quote($date_debut);
        }

        if ($page!==null && $nbmess!==null) {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        $result = $this->_sql->query("SELECT vp.*, l.nom as nom_livreur, l.prenom as prenom_livreur, a.nom as nom_admin, a.prenom as prenom_admin FROM vehicules_planning vp LEFT JOIN utilisateurs l ON vp.id_livreur=l.id LEFT JOIN utilisateurs a ON vp.id_admin=a.id WHERE id_vehicule=".$this->_sql->quote($id_vehicule)." ".$req_sup." ORDER BY h_debut DESC ".$req_limit);
        $_listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listePlanning;
    }

    public function setPlanning($h_debut, $h_fin, $id_livreur, $id_vehicule, $id_admin, $etat) {
        $result = $this->_sql->exec("INSERT INTO vehicules_planning (id_vehicule, id_livreur, id_admin, date, h_debut, h_fin, etat) VALUES (".$this->_sql->quote($id_vehicule).", ".$this->_sql->quote($id_livreur).", ".$this->_sql->quote($id_admin).", NOW(), ".$this->_sql->quote($h_debut).", ".$this->_sql->quote($h_fin).", ".$this->_sql->quote($etat).")");
    }

    public function checkVehicule($id_vehicule, $id_livreur, $date_debut, $date_fin) {
        $check_vehicule=false;
        $req_sup=" ";
        /*if ($id_livreur!="" && $livreur!=0) {
            $req_sup=" AND id_livreur!=".$this->_sql->quote($id_livreur);
        }*/
        $result = $this->_sql->query("SELECT * FROM livreurs_connexion WHERE id_vehicule=".$this->_sql->quote($id_vehicule)." ".$req_sup." AND ".$this->_sql->quote($date_debut)."<date_deconnexion AND ".$this->_sql->quote($date_fin).">date_connexion");
        $ligne = $result->fetch();
        if ($ligne) {
            $check_vehicule=true;
        }
        $result = $this->_sql->query("SELECT * FROM livreurs_planning WHERE id_vehicule=".$this->_sql->quote($id_vehicule)." ".$req_sup." AND ".$this->_sql->quote($date_debut)."<date_fin AND ".$this->_sql->quote($date_fin).">date_debut");
        $ligne = $result->fetch();
        if ($ligne) {
            $check_vehicule=true;
        }

        return $check_vehicule;
    }

    public function changeEtat($id_vehicule, $etat, $id_admin, $commentaire) {
        //récupérer le dernier livreur qui a utilisé le véhicule
        $result = $this->_sql->query("SELECT * FROM livreurs_planning WHERE id_vehicule=".$this->_sql->quote($id_vehicule)." AND date_debut<=NOW() ORDER BY date_debut DESC ");
        $liste_livreurs = $result->fetchAll(PDO::FETCH_OBJ);
        $id_livreur=$liste_livreurs[0]->id_livreur;

        if ($id_livreur=="") {
            $id_livreur=0;
        }

        $result = $this->_sql->exec("INSERT INTO vehicules_historique (id_vehicule, id_livreur, id_admin, etat, commentaire, date) VALUES (".$this->_sql->quote($id_vehicule).", ".$this->_sql->quote($id_livreur).", ".$this->_sql->quote($id_admin).", ".$this->_sql->quote($etat).", ".$this->_sql->quote($commentaire).", NOW())");
    }

    public function getHistorique($id_vehicule, $nbmess, $page) {
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        $result = $this->_sql->query("SELECT v.date, v.etat, v.commentaire, v.id_operation, u.nom as nom_admin, u.prenom as prenom_admin, l.nom as nom_livreur, l.prenom as prenom_livreur FROM vehicules_historique v LEFT JOIN utilisateurs u ON v.id_admin=u.id LEFT JOIN livreurs l ON v.id_livreur=l.id WHERE v.id_vehicule=".$this->_sql->quote($id_vehicule)." ORDER BY date DESC ".$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeVehiculeHisto
        $_listeVehiculeHisto = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeVehiculeHisto;
    }

    public function getHistoriqueOperation($id_operation, $nbmess, $page) {
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        $result = $this->_sql->query("SELECT v.date, v.etat, v.commentaire, v.id_operation, u.nom as nom_admin, u.prenom as prenom_admin, l.nom as nom_livreur, l.prenom as prenom_livreur FROM vehicules_historique v LEFT JOIN utilisateurs u ON v.id_admin=u.id LEFT JOIN livreurs l ON v.id_livreur=l.id WHERE v.id_operation=".$this->_sql->quote($id_operation)." ORDER BY date DESC ".$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeVehiculeHisto
        $_listeVehiculeHisto = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeVehiculeHisto;
    }

    public function checkImmatriculation($immatriculation) {
        $result = $this->_sql->query("SELECT * FROM vehicules WHERE immatriculation=".$this->_sql->quote($immatriculation));
        $ligne = $result->fetch();
        if ($ligne) {
            return true;
        }
        return false;
    }

    public function setHistoriqueOp ($id_vehicule, $id_admin, $id_operation, $etat, $commentaire) {
        $id_livreur = 0;
        $result = $this->_sql->exec("INSERT INTO vehicules_historique (id_vehicule, id_livreur, id_admin, id_operation, etat, commentaire, date) 
        VALUES (".$this->_sql->quote($id_vehicule).", ".$this->_sql->quote($id_livreur).", ".$this->_sql->quote($id_admin).", ".$this->_sql->quote($id_operation).", ".$this->_sql->quote($etat).", ".$this->_sql->quote($commentaire).", NOW())");
    }


    public function verifVehicule($immatriculation) {
        $result = $this->_sql->query("SELECT id FROM vehicules WHERE immatriculation=".$this->_sql->quote($immatriculation));
        $ligne = $result->fetch();
        if($ligne){
            return $ligne["id"];
        }
        return false;
    }
}
?>