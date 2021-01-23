<?php

/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 18/07/2017
 * Time: 11:36
 */
class Etat
{
    private $_id;
    private $_libelle;

    // déclaration des méthodes
    public function __construct($sql, $id = null)
    {
        $this->_sql = $sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM livreur_etat WHERE id=" . $this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_id      = $ligne[0]->id;
            $this->_libelle = $ligne[0]->libelle;
        }
    }

    /**
     * @return mixed
     */
    public function getLibelle() {
        return $this->_libelle;
    }

    public function getAll() {
        $result = $this->_sql->query("SELECT * FROM livreur_etat");

        // Récupération des résultats sélectionnés dans le tableau $_action
        $id_etat = $result->fetchAll(PDO::FETCH_OBJ);
        return $id_etat;
    }

}