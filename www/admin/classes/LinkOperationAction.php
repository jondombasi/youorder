<?php

/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 26/02/2017
 * Time: 19:05
 */
class LinkOperationAction
{
    private $_id;
    private $_id_operation;
    private $_id_action;

    public function __construct($sql, $id = null)
    {
        $this->_sql = $sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM operation_action WHERE id=" . $this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);
            $this->_id           = $ligne[0]->id;
            $this->_id_operation = $ligne[0]->operation_id;
            $this->_id_action    = $ligne[0]->action_id;
        }
    }

    public function setLinkOperationAction($id_action , $id_operation) {
        $result = $this->_sql->exec("INSERT INTO operation_action (operation_id, action_id) 
        VALUES ('$id_operation', $id_action)");
    }
}