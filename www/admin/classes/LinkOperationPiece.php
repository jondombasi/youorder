<?php

/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 26/02/2017
 * Time: 19:16
 */
class LinkOperationPiece
{
    private $_id;
    private $_id_operation;
    private $_id_piece;

    public function __construct($sql, $id = null)
    {
        $this->_sql = $sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM operation_piece WHERE id=" . $this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);
            $this->_id           = $ligne[0]->id;
            $this->_id_operation = $ligne[0]->operation_id;
            $this->_id_piece     = $ligne[0]->piece_id;
        }
    }

    public function setLinkOperationPiece($id_piece , $id_operation) {
        $result = $this->_sql->exec("INSERT INTO operation_piece (operation_id, piece_id) 
        VALUES ('$id_operation', '$id_piece')");
    }
}