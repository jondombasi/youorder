<?php
class Pagination {
    // déclaration des propriétés
	private $nbPages;
	private $nbRes;

    public function __construct($sql, $table, $nbmess, $req_sup) {
        $req = "SELECT count(*) as NB FROM ".$table." WHERE 1 ".$req_sup;
		$result = $sql->query($req);
		$ligne = $result->fetch();
		if($ligne!=""){
			$this->nbRes = $ligne["NB"];
		}else{
			$this->nbRes = 0;
		}
		$this->nbPages = $this->nbRes/$nbmess;
		$this->nbPages = ceil($this->nbPages);
    }

	public function getNbPages() {
        return $this->nbPages;
    }

    public function getNbRes() {
        return $this->nbRes;
    }
}
?>