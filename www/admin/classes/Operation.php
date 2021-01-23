<?php

/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 26/02/2017
 * Time: 14:25
 */
class Operation
{
    //private $_id;
    private $_commentaire;
    private $_date;
    private $_pieces;

    private $LinkOperationAction;
    private $LinkOperationPiece;

    // déclaration des méthodes
    public function __construct($sql, $id = null)
    {
        $this->_sql = $sql;
        $this->LinkOperationAction = new LinkOperationAction($sql);
        $this->LinkOperationPiece  = new LinkOperationPiece($sql);

        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM operation WHERE id=" . $this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $id_op = $ligne[0]->id;
            $this->_commentaire = $ligne[0]->commentaire;
            $this->_date        = $ligne[0]->date;
        }
    }

    /**
     * @return mixed
     */
    public function getCommentaire()
    {
        return $this->_commentaire;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * @return mixed
     */
    public function getActions($id_operation)
    {
        $resultActions = $this->_sql->query("SELECT a.libelle FROM operation_action o" .
            " LEFT JOIN actions a ON o.action_id=a.id WHERE o.operation_id= " . $id_operation);
        $actions = $resultActions->fetchAll(PDO::FETCH_OBJ);

        return $actions;
    }

    /**
     * @return mixed
     */
    public function getPieces($id_operation)
    {
        $resultActions = $this->_sql->query("SELECT p.code FROM operation_piece o" .
            " LEFT JOIN piece p ON o.piece_id=p.id WHERE o.operation_id= " . $id_operation);
        $pieces = $resultActions->fetchAll(PDO::FETCH_OBJ);

        return $pieces;
    }

    public function getNbPages() {
        return $this->_nbPages;
    }

    public function getNbRes() {
        return $this->_nbRes;
    }



    public function getPagination($nbmess, $commentaire)
    {
        //compter le nb de pages et de résultats
        $req_sup="";

        if ($commentaire!="") {
            $req_sup=" AND commentaire LIKE '%".$commentaire."%'";
        }



        $req = "SELECT count(*) as NB FROM operation WHERE etat!='supprime' " . $req_sup;
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



    public function getAll($page, $nbmess, $commentaire, $immatriculation) {

        $req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($commentaire!="") {
            $req_sup.=" AND commentaire LIKE '%".$commentaire."%'";
        }

        $result = $this->_sql->query("SELECT * FROM operation WHERE etat != 'supprime' ORDER BY date".$req_sup." ".$req_limit);

        // Récupération des résultats sélectionnés dans le tableau $_action
        $operations = $result->fetchAll(PDO::FETCH_OBJ);
        return $operations;
    }

    public function setOperation($id_vehicule, $id_admin, $commentaire, $actions, $pieces) {

        $result = $this->_sql->exec("INSERT INTO operation (id_vehicule, date) 
        VALUES (".$this->_sql->quote($id_vehicule).", NOW())");

        $id_operation = $this->_sql->lastInsertId();

        $Vehicule = new Vehicule($this->_sql, $id_vehicule);
        $Vehicule->setHistoriqueOp($id_vehicule, $id_admin, $id_operation, $Vehicule->getEtat(), $commentaire);

        foreach ($actions as $action) {
            $this->LinkOperationAction->setLinkOperationAction($action, $id_operation);
        }
        if($pieces != ""){
            foreach ($pieces as $piece) {
                $this->LinkOperationPiece->setLinkOperationPiece($piece, $id_operation);
                $p = new Materiel($this->_sql, $piece);
                $p->updateQuantiy($piece, $p->getQuantite() - 1);
            }
        }

        return $id_vehicule;
    }

}