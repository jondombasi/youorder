<?php

/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 26/02/2017
 * Time: 15:18
 */
class Action
{
    private $_id_actions;
    private $_libelle;

    // déclaration des méthodes
    public function __construct($sql, $id = null)
    {
        $this->_sql = $sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM actions WHERE id=" . $this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_id      = $ligne[0]->id;
            $this->_libelle = $ligne[0]->libelle;
        }
    }


    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->_libelle;
    }


    public function getAll() {
        $result = $this->_sql->query("SELECT * FROM actions");

        // Récupération des résultats sélectionnés dans le tableau $_action
        $actions = $result->fetchAll(PDO::FETCH_OBJ);
        return $actions;
    }


    public function setActions($id_actions, $libelle){

        if ($id_actions == ""){
            $result = $this->_sql->exec("INSERT INTO actions (id, libelle)  VALUES(" . $this->_sql->quote($id_actions) . ", ".$this->_sql->quote($libelle).")");
            $id_actions = $this->_sql->lastInsertId();
        }else {
            $result = $this->_sql->exec("UPDATE actions SET libelle=".$this->_sql->quote($libelle)." WHERE id=".$this->_sql->quote($id_actions));
        }
        return $id_actions;


    }

}
