<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/PHPMailer/class.phpmailer.php');

class Utilisateur {
    // déclaration des propriétés
    private $_id_utilisateur;
    private $_nom;
    private $_prenom;
    private $_password;
    private $_email;
    private $_numero;
    private $_role;
    private $_restaurant;
    private $_liste_resto;
    private $_statut;
    private $_photo;
    private $_affecter_commande;
    private $_visibilite_map;
    private $_planning_livreur;
    private $_dispo_api;
    private $_secret_key;
    private $_date_conn;
    private $_nbPages;
    private $_nbRes;

    private $_sql;

    private $_listeUtilisateur=array();

    // déclaration des méthodes
    public function __construct($sql, $id = null) {
        $this->_sql=$sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM utilisateurs WHERE id=".$this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_id_utilisateur->$ligne[0]->id;
            $this->_nom                 = $ligne[0]->nom;
            $this->_prenom              = $ligne[0]->prenom;
            $this->_password            = $ligne[0]->password;
            $this->_email               = $ligne[0]->email;
            $this->_numero              = $ligne[0]->numero;
            $this->_role                = $ligne[0]->role;
            $this->_restaurant          = $ligne[0]->restaurant;
            $this->_liste_resto         = $ligne[0]->liste_resto;
            $this->_statut              = $ligne[0]->statut;
            $this->_photo               = $ligne[0]->photo;
            $this->_date_conn           = $ligne[0]->date_conn;
            $this->_affecter_commande   = $ligne[0]->affecter_commande;
            $this->_visibilite_map      = $ligne[0]->visibilite_map;
            $this->_planning_livreur    = $ligne[0]->planning_livreur;
            $this->_dispo_api           = $ligne[0]->dispo_api;
            $this->_secret_key          = $ligne[0]->secret_key;
        }
    }

    public function getNom() {
        return $this->_nom;
    }

    public function getPrenom() {
        return $this->_prenom;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getNumero() {
        return $this->_numero;
    }

    public function getRole() {
        return $this->_role;
    }

    public function getRestaurant() {
        return $this->_restaurant;
    }

    public function getListeResto() {
        return $this->_liste_resto;
    }

    public function getStatut() {
        return $this->_statut;
    }

    public function getPhoto() {
        return $this->_photo;
    }

    public function getDateConn() {
        return $this->_date_conn;
    }

    public function getAffecterCommande() {
        return $this->_affecter_commande;
    }

    public function getVisibiliteMap() {
        return $this->_visibilite_map;
    }

    public function getPlanningLivreur() {
        return $this->_planning_livreur;
    }

    public function getDispoAPI() {
        return $this->_dispo_api;
    }

    public function getSecretKey() {
        return $this->_secret_key;
    }

    // déclaration des méthodes
    public function getAll($page, $nbmess, $nom, $email, $role) {
    	$req_sup="";
        $req_limit="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }

        if ($nom!="") {
            $req_sup=" AND nom LIKE '%".$nom."%'";
        }
        if ($email!="") {
            $req_sup=" AND email LIKE '%".$email."%'";
        }
        if ($role!="") {
            $req_sup=" AND role ='".$role."'";
        }

        $result = $this->_sql->query("SELECT * FROM utilisateurs WHERE role NOT IN ('livreur', 'inactif') ".$req_sup." ORDER BY id ASC ".$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeVehicule
        $_listeUtilisateur = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeUtilisateur;
    }

    function checkEmail($email, $id) {
    	$result = $this->_sql->query("SELECT * FROM utilisateurs WHERE email = '".$email."' AND id != '".$id."'");
        $ligne = $result->fetch();
        if ($ligne) {
            return true;
        }
        return false;
    }

    public function setUtilisateur($id_utilisateur, $nom, $prenom, $email, $numero, $password, $role, $restaurant, $liste_resto, $affecter_commande, $visibilite_map, $planning_livreur, $dispo_api, $secret_key) {
    	if ($restaurant=="") {
    		$restaurant=0;
    	}
        if ($id_utilisateur=="") {
			$result = $this->_sql->exec("INSERT INTO utilisateurs (nom,prenom,email,numero,password,role,restaurant,liste_resto, affecter_commande, visibilite_map, planning_livreur, dispo_api, secret_key) VALUES (".$this->_sql->quote($nom).",".$this->_sql->quote($prenom).",".$this->_sql->quote($email).",".$this->_sql->quote($numero).",".$this->_sql->quote($password).",".$this->_sql->quote($role).",".$this->_sql->quote($restaurant).",".$this->_sql->quote($liste_resto).", ".$this->_sql->quote($affecter_commande).", ".$this->_sql->quote($visibilite_map).", ".$this->_sql->quote($planning_livreur).", ".$this->_sql->quote($dispo_api).", ".$this->_sql->quote($secret_key).")");
			$id_utilisateur = $this->_sql->lastInsertId(); 

			//envoi des identifiants
			$body = "";
			$body .= 'Bonjour, <br/><br/>
						Voici vos identifiants pour accéder à l\'interface You Order et gérer vos commandes : <br/>
						Accès : <a href="https//www.you-order.eu/admin/">https://you-order.eu/admin/</a><br/>
						Identifiant : '.$email.'<b></b><br/>
						Mot de passe : '.$password.'<b></b><br/><br/>
						Merci,<br/>
						L\'équipe YouOrder';
		 
		   	// On créer une nouvelle instance de la classe
		   	$mail = new PHPMailer();
			$mail->From         = "contact@youorder.fr";
			$mail->Sender       = "contact@youorder.fr";
			$mail->FromName     = "YouOrder";
			$mail->Subject      = "Votre accès YouOrder";
			$mail->MessageID    = newChaine(6).".".newChaine(6)."@youorder.fr";
			$mail->MsgHTML($body);
			$mail->CharSet      = 'UTF-8';
			$mail->AddReplyTo("contact@youorder.fr","YouOrder");
			$mail->AddAddress($email, "");
			$mail->send();
        }
        else {
			$result = $this->_sql->exec("UPDATE utilisateurs SET nom=".$this->_sql->quote($nom).",prenom=".$this->_sql->quote($prenom).",email=".$this->_sql->quote($email).",numero=".$this->_sql->quote($numero).",password=".$this->_sql->quote($password).",role=".$this->_sql->quote($role).",restaurant=".$this->_sql->quote($restaurant)." WHERE id = ".$this->_sql->quote($id_utilisateur));
			
			if($_SESSION["admin"]){
				$result = $this->_sql->exec("UPDATE utilisateurs SET liste_resto=".$this->_sql->quote($liste_resto).", affecter_commande=".$this->_sql->quote($affecter_commande).", visibilite_map=".$this->_sql->quote($visibilite_map).", planning_livreur=".$this->_sql->quote($planning_livreur).", dispo_api=".$this->_sql->quote($dispo_api).", secret_key=".$this->_sql->quote($secret_key)." WHERE id = ".$this->_sql->quote($id_utilisateur));
			}
        }
        return $id_utilisateur;
    }

    public function sendEmail($id_utilisateur) {
    	$result = $this->_sql->query("SELECT * FROM utilisateurs WHERE id = '".$id_utilisateur."' AND role != 'inactif'");
		$ligne = $result->fetch();
		if($ligne!="") {
			$password   = $ligne["password"];
			$nom	    = $ligne["nom"];
			$prenom	    = $ligne["prenom"];
			$email	    = $ligne["email"];

			$body = "";
			$body .= 'Bonjour, <br/><br/>
						Voici vos identifiants pour accéder à l\'interface You Order et gérer vos commandes : <br/>
						Accès : <a href="https://www.you-order.eu/admin/">https://you-order.eu/admin/</a><br/>
						Identifiant : '.$email.'<b></b><br/>
						Mot de passe : '.$password.'<b></b><br/><br/>
						Merci,<br/>
						L\'équipe YouOrder';
		 
		   // On créé une nouvelle instance de la classe
		   $mail = new PHPMailer();
		   $mail->From = "contact@youorder.fr";
		   $mail->Sender = "contact@youorder.fr";
		   $mail->FromName = "YouOrder";
		   $mail->Subject = "Votre accès YouOrder";
		   $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
		   $mail->MsgHTML($body);
		   $mail->CharSet = 'UTF-8';	
		   $mail->AddReplyTo("contact@youorder.fr","YouOrder");
		   $mail->AddAddress($email, "");
		   $mail->send();
		}
    }

    public function setPhoto($id_utilisateur, $url) {
        $this->_photo=$url;
        $result = $this->_sql->exec("UPDATE utilisateurs SET photo=".$this->_sql->quote($url)." WHERE id=".$this->_sql->quote($id_utilisateur));
    }
}
?>