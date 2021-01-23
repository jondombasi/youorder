<?php
class Client {
    // déclaration des propriétés
    private $_id_client;
    private $_nom;
    private $_prenom;
    private $_adresse;
    private $_longitude;
    private $_latitude;
    private $_numero;
    private $_email;
    private $_commentaire;
    private $_restaurant;
    private $_statut;
    private $_date_ajout;
    private $_nbPages;
    private $_nbRes;

    private $_sql;

    private $_listeClients=array();

    // déclaration des méthodes
    public function __construct($sql, $id = null) {
        $this->_sql=$sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM clients WHERE id=".$this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_nom = $ligne[0]->nom;
            $this->_prenom = $ligne[0]->prenom;
            $this->_adresse = $ligne[0]->adresse;
            $this->_longitude = $ligne[0]->longitude;
            $this->_latitude = $ligne[0]->latitude;
            $this->_numero = $ligne[0]->numero;
            $this->_email = $ligne[0]->email;
            $this->_commentaire = $ligne[0]->commentaire;
            $this->_restaurant = $ligne[0]->restaurant;
            $this->_statut = $ligne[0]->statut;
            $this->_date_ajout = $ligne[0]->date_ajout;
        }
    }

    public function getNom() {
        return $this->_nom;
    }

    public function getPrenom() {
        return $this->_prenom;
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

    public function getNumero() {
        return $this->_numero;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getCommentaire() {
        return $this->_commentaire;
    }

    public function getRestaurant() {
        return $this->_restaurant;
    }

    public function getStatut() {
        return $this->_statut;
    }

    public function getDateAjout() {
        return $this->_date_ajout;
    }

    public function getNbPages() {
        return $this->_nbPages;
    }

    public function getNbRes() {
        return $this->_nbRes;
    }

    public function getPagination($nbmess, $nom, $numero, $restaurant) {
        //compter le nb de pages et de résultats
        $req_sup="";
        if ($nom!="") {
            $req_sup.=" AND c.nom LIKE '%".$nom."%'";
        }
        if ($numero!="") {
            $req_sup.=" AND c.numero LIKE '%".$numero."%'";
        }
        if ($restaurant!="") {
            $req_sup.=" AND c.restaurant ='".$restaurant."'";
        }

        $req = "SELECT count(*) as NB FROM clients c INNER JOIN restaurants r ON r.id=c.restaurant WHERE c.statut = 1 AND r.statut = 1 ".$req_sup.$_SESSION["req_resto"];
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

    public function getAll($page, $nbmess, $nom, $numero, $restaurant) {
        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($nom!="") {
            $req_sup.=" AND c.nom LIKE '%".$nom."%'";
        }
        if ($numero!="") {
            $req_sup.=" AND c.numero LIKE '%".$numero."%'";
        }
        if ($restaurant!="") {
            $req_sup.=" AND c.restaurant ='".$restaurant."'";
        }

        $result = $this->_sql->query("SELECT c.*, r.nom as nom_resto FROM clients c INNER JOIN restaurants r ON r.id=c.restaurant WHERE c.statut = 1 and r.statut = 1 ".$req_sup.$_SESSION["req_resto"].$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeVehicule
        $_listeClients = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeClients;
    }

    public function setClient($id_client, $nom, $prenom, $adresse, $latitude, $longitude, $numero, $email, $commentaire, $restaurant) {
        if ($id_client=="") {
            $result = $this->_sql->exec("INSERT INTO clients (nom, prenom, adresse, latitude, longitude, numero, email, commentaire, restaurant, date_ajout) VALUES (".$this->_sql->quote($nom).", ".$this->_sql->quote($prenom).", ".$this->_sql->quote($adresse).", ".$this->_sql->quote($latitude).", ".$this->_sql->quote($longitude).", ".$this->_sql->quote($numero).", ".$this->_sql->quote($email).", ".$this->_sql->quote($commentaire).", ".$this->_sql->quote($restaurant).", NOW())");
            $id_client=$this->_sql->lastInsertId();
        }
        else {
            $result = $this->_sql->exec("UPDATE clients SET nom=".$this->_sql->quote($nom).", prenom=".$this->_sql->quote($prenom).", adresse=".$this->_sql->quote($adresse).", latitude=".$this->_sql->quote($latitude).", longitude=".$this->_sql->quote($longitude).", numero=".$this->_sql->quote($numero).", email=".$this->_sql->quote($email).", commentaire=".$this->_sql->quote($commentaire).", restaurant=".$this->_sql->quote($restaurant)." WHERE id=".$this->_sql->quote($id_client));
        }
        return $id_client;
    }

    public function verifClient($nom_client, $prenom_client, $telephone_client, $id_commercant) {
        $result = $this->_sql->query("SELECT id FROM clients WHERE nom=".$this->_sql->quote($nom_client)." AND prenom=".$this->_sql->quote($prenom_client)." AND numero=".$this->_sql->quote($telephone_client)." AND restaurant=".$this->_sql->quote($id_commercant));
        $ligne = $result->fetch();
        if($ligne){
            return $ligne["id"];
        }
        return false;
    }
}
?>