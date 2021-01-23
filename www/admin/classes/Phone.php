<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/10/2017
 * Time: 15:15
 */

class Phone
{
    private $_id_phone;
    private $_marque;
    private $_modele;
    private $_quantite;
    private $_nbPages;
    private $_nbRes;

    public function __construct($sql, $id = null) {
        $this->_sql=$sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM phone WHERE id=".$this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_id_phone    = $ligne[0]->id;
            $this->_marque      = $ligne[0]->marque;
            $this->_modele      = $ligne[0]->modele;
            $this->_quantite    = $ligne[0]->quantite;
        }
    }

    public function getMarque() {
        return $this->_marque;
    }

    public function getModele() {
        return $this->_modele;
    }

    public function getQuantite(){
        return $this->_quantite;
    }

    public function getNbPages() {
        return $this->_nbPages;
    }

    public function getNbRes() {
        return $this->_nbRes;
    }

    public function getPagination($nbmess, $modele)
    {
        //compter le nb de pages et de résultats
        $req_sup = "";

        if ($modele != "") {
            $req_sup = " AND modele LIKE '%" . $modele . "%'";
        }

        $req = "SELECT count(*) as NB FROM phone WHERE is_available != 0 " . $req_sup;
        $result = $this->_sql->query($req);
        $ligne = $result->fetch();
        if ($ligne != "") {
            $this->_nbRes = $ligne["NB"];
        } else {
            $this->_nbRes = 0;
        }
        $this->_nbPages = $this->_nbRes / $nbmess;
        $this->_nbPages = ceil($this->_nbPages);
    }

    public function getAll($page, $nbmess, $modele) {
        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($modele!="") {
            $req_sup.=" AND modele LIKE '%".$modele."%'";
        }

        $result = $this->_sql->query("SELECT * FROM phone WHERE is_available != 0 ".$req_sup." ".$req_limit);

        // Récupération des résultats sélectionnés dans le tableau $_action
        $_listeNumber = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeNumber ;
    }

    public function setPhone($marque, $modele, $quantite)
    {
        $result = $this->_sql->exec("INSERT INTO phone (marque, modele, quantite) VALUES (". $this->_sql->quote($marque) . "," . $this->_sql->quote($modele) . "," . $this->_sql->quote($quantite) .")");
        $id_phone = $this->_sql->lastInsertId();

        return $id_phone;
    }


    public function setQuantite($quantite)
    {
        $this->_quantite = $quantite;
    }

    public function updateQuantiy ($id_phone, $quantite) {
        $result = $this->_sql->exec("UPDATE phone SET quantite=" . $this->_sql->quote($quantite) . "WHERE id=" . $this->_sql->quote($id_phone));
    }

    public function updatePhone($id_phone, $marque, $modele, $quantite) {
        $result = $this->_sql->exec("UPDATE phone SET marque=" . $this->_sql->quote($marque) . ", model=" . $this->_sql->quote($modele) . ", quantite=" . $this->_sql->quote($quantite) . " WHERE id=" . $this->_sql->quote($id_phone));
    }
}