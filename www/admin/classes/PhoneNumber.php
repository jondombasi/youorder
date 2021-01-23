<?php

/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 11/09/2017
 * Time: 15:32
 */
class PhoneNumber
{

    private $_id_phone;
    private $_number;
    private $_phone;
    private $_nbPages;
    private $_nbRes;

    public function __construct($sql, $id = null) {
        $this->_sql=$sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM phone_number WHERE id=".$this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_id_phone    = $ligne[0]->id;
            $this->_number      = $ligne[0]->number;
            $this->_phone       = $ligne[0]->phone;
        }
    }


    public function getNumber() {
        return $this->_number;
    }

    public function getPhone() {
        return $this->_phone;
    }

    public function getNbPages()
    {
        return $this->_nbPages;
    }

    public function getNbRes()
    {
        return $this->_nbRes;
    }

    public function getPagination($nbmess, $number)
    {
        //compter le nb de pages et de résultats
        $req_sup = "";

        if ($number != "") {
            $req_sup = " AND number LIKE '%" . $number . "%'";
        }

        $req = "SELECT count(*) as NB FROM phone_number WHERE etat!= 'supprime' " . $req_sup;
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

    public function getAll($page, $nbmess, $number) {
        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($number!="") {
            $req_sup.=" AND number LIKE '%".$number."%'";
        }

        $result = $this->_sql->query("SELECT * FROM phone_number WHERE etat != 'supprime'".$req_sup." ".$req_limit);

        // Récupération des résultats sélectionnés dans le tableau $_action
        $_listeNumber = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeNumber ;
    }

    public function setNumber($number)
    {
        $result = $this->_sql->exec("INSERT INTO phone_number (number, etat) VALUES (". $this->_sql->quote($number). ",'ok')");
        $id_phone = $this->_sql->lastInsertId();

        return $id_phone;
    }

    public function updateNumber($id_phone, $number, $phone) {
        $result = $this->_sql->exec("UPDATE phone_number SET number=" . $this->_sql->quote($number) . ", phone=" . $this->_sql->quote($phone) . " WHERE id=" . $this->_sql->quote($id_phone));
    }

    public function checkCode($code)
    {
        $result = $this->_sql->query("SELECT * FROM phone_number WHERE number=" . $this->_sql->quote($code));
        $ligne = $result->fetch();
        if ($ligne) {
            return true;
        }
        return false;
    }










}