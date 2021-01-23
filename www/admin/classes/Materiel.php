<?php

/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 16/02/2017
 * Time: 10:47
 */
class Materiel
{
    private $_id_piece;
    private $_code;
    private $_libelle;
    private $_quantite;
    private $_prix_ht;
    private $_nbPages;
    private $_nbRes;
    private $_etat;

    private $_sql;
    private $_listeMateriel = array();


    // déclaration des méthodes
    public function __construct($sql, $id = null)
    {
        $this->_sql = $sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM piece  WHERE id=" . $this->_sql->quote($id));
            $ligne  = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_code        = $ligne[0]->code;
            $this->_libelle     = $ligne[0]->libelle;
            $this->_quantite    = $ligne[0]->quantite;
            $this->_prix_ht     = $ligne[0]->prix_ht;
        }
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function getLibelle()
    {
        return $this->_libelle;
    }

    public function getQuantite()
    {
        return $this->_quantite;
    }

    public function getPrixHt()
    {
        return $this->_prix_ht;
    }

    public function getEtat()
    {
        return $this->_etat;
    }

    public function getNbPages()
    {
        return $this->_nbPages;
    }

    public function getNbRes()
    {
        return $this->_nbRes;
    }


    public function getPagination($nbmess, $code, $libelle)
    {
        //compter le nb de pages et de résultats
        $req_sup = "";
        if ($code != "") {
            $req_sup=" AND code LIKE '%".$code."%'";
        }

        if ($libelle != "") {
            $req_sup = " AND libelle LIKE '%" . $libelle . "%'";
        }

        $req = "SELECT count(*) as NB FROM piece WHERE etat!= 'supprime' " . $req_sup;
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

    public function getAll($page, $nbmess, $code, $libelle) {
        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($code!="") {
            $req_sup.=" AND code LIKE '%".$code."%'";
        }
        if ($libelle!="") {
            $req_sup.=" AND libelle LIKE '%".$libelle."%'";
        }

        $result = $this->_sql->query("SELECT * FROM piece WHERE etat != 'supprime'".$req_sup." ".$req_limit);

        // Récupération des résultats sélectionnés dans le tableau $_action
        $_listeMateriel = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeMateriel;
    }

    public function setQuantite($quantite)
    {
        $this->_quantite = $quantite;
    }

    public function updateQuantiy ($id_piece, $quantity) {
        $result = $this->_sql->exec("UPDATE piece SET quantite=" . $this->_sql->quote($quantity) . "WHERE id=" . $this->_sql->quote($id_piece));
    }


    public function setPiece($code, $libelle, $quantite, $prix_ht)
    {
        $result = $this->_sql->exec("INSERT INTO piece (code, libelle, quantite, prix_ht, etat) VALUES (" . $this->_sql->quote($code) . ", " . $this->_sql->quote($libelle) . ", " . $this->_sql->quote($quantite) . ", " . $this->_sql->quote($prix_ht). ", 'ok' )");
        $id_piece = $this->_sql->lastInsertId();

        return $id_piece;
    }

    public function updatePiece($id_piece, $code, $libelle, $quantite, $prix_ht) {
        $result = $this->_sql->exec("UPDATE piece SET code=" . $this->_sql->quote($code) . ", libelle=" . $this->_sql->quote($libelle) . ", quantite=" . $this->_sql->quote($quantite) . ", prix_ht=" . $this->_sql->quote($prix_ht) . " WHERE id=" . $this->_sql->quote($id_piece));
    }

    public function checkCode($code)
    {
        $result = $this->_sql->query("SELECT * FROM piece WHERE code=" . $this->_sql->quote($code));
        $ligne = $result->fetch();
        if ($ligne) {
            return true;
        }
        return false;
    }
}