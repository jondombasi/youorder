<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/PHPMailer/class.phpmailer.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Commercant.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Client.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Livreur.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Utilisateur.php');

class Commande {
    // déclaration des propriétés
    private $_id_commande;
    private $_restaurant;
    private $_client;
    private $_commentaire;
    private $_date_debut;
    private $_date_fin;
    private $_date_ajout;
    private $_statut;
    private $_raison_refus;
    private $_comm_refus;
    private $_date_statut;
    private $_distance;
    private $_duree;
    private $_livreur;
    private $_signature;

    private $_nbRes;
    private $_nbPages;

    private $_sql;

    private $_listeCommande=array();

    // déclaration des méthodes
    public function __construct($sql, $id = null) {
        $this->_sql=$sql;
        if ($id !== null) {
            $result = $this->_sql->query("SELECT * FROM commandes WHERE id=".$this->_sql->quote($id));
            $ligne = $result->fetchAll(PDO::FETCH_OBJ);

            $this->_id_commercant   = $ligne[0]->id;
            $this->_restaurant      = $ligne[0]->restaurant;
            $this->_client          = $ligne[0]->client;
            $this->_commentaire     = $ligne[0]->commentaire;
            $this->_date_debut      = $ligne[0]->date_debut;
            $this->_date_fin        = $ligne[0]->date_fin;
            $this->_date_ajout      = $ligne[0]->date_ajout;
            $this->_statut          = $ligne[0]->statut;
            $this->_raison_refus    = $ligne[0]->raison_refus;
            $this->_comm_refus      = $ligne[0]->comm_refus;
            $this->_date_statut     = $ligne[0]->date_statut;
            $this->_distance        = $ligne[0]->distance;
            $this->_duree           = $ligne[0]->duree;
            $this->_livreur         = $ligne[0]->livreur;
            $this->_signature       = $ligne[0]->signature;
        }
    }
    
    public function getRestaurant() {
        return $this->_restaurant;
    }

    public function getClient() {
        return $this->_client;
    }

    public function getCommentaire() {
        return $this->_commentaire;
    }

    public function getDateDebut() {
        return $this->_date_debut;
    }

    public function getDateFin() {
        return $this->_date_fin;
    }

    public function getDateAjout() {
        return $this->_date_ajout;
    }

    public function getStatut() {
        return $this->_statut;
    }

    public function getRaisonRefus() {
        return $this->_raison_refus;
    }

    public function getCommRefus() {
        return $this->_comm_refus;
    }

    public function getDateStatut() {
        return $this->_date_statut;
    }

    public function getDistance() {
        return round($this->_distance/1000,0);
    }

    public function getDuree() {
        $duree_h = gmdate("H",$this->_duree);
        $duree_m = gmdate("i",$this->_duree);
        $this->_duree=($duree_h>0) ? $duree_h."h".$duree_m : $duree_m." min";
        return $this->_duree;
    }

    public function getLivreur() {
        return $this->_livreur;
    }

    public function getSignature() {
        return $this->_signature;
    }

    public function getNbPages() {
        return $this->_nbPages;
    }

    public function getNbRes() {
        return $this->_nbRes;
    }

    public function getPagination($nbmess, $id_livreur, $restaurant, $statut, $periode, $histo) {
        //compter le nb de pages et de résultats
        $req_sup="";
        if ($id_livreur!="") {
            $req_sup.=" AND c.livreur ='".$id_livreur."'";
        }
        if ($restaurant!="") {
            $req_sup.=" AND c.restaurant ='".$restaurant."'";
        }
        if ($statut!="") {
            $req_sup.=" AND c.statut ='".$statut."'";
        }
        else{
            if($histo=="0"){
                $req_sup .= " AND c.statut in ('ajouté','réservé','récupéré') ";                        
            }
            else{
                $req_sup .= " AND c.statut in ('signé','echec') ";                      
            }
        }
        if($periode!=""){
            $periode_ = explode(" - ",$periode);
            if ($periode_[0]==$periode_[1]) {
                $datedebut = $periode_[0]." 00:00:00";
                $datefin = $periode_[1]." 23:59:59";
            }
            else {
                $datedebut = $periode_[0];
                $datefin = $periode_[1];
            }

            $req_sup .= " AND c.date_debut >= '".date("Y-m-d H:i:s",strtotime($datedebut))."' AND c.date_debut <= '".date("Y-m-d H:i:s",strtotime($datefin))."'"; 
        }
        if($_SESSION["role"]=="livreur"){
            $req_sup .= " AND c.livreur IN (0,".$_SESSION["userid"].")";
        }

        $req = "SELECT count(*) as NB FROM commandes c LEFT JOIN restaurants r ON c.restaurant=r.id WHERE 1 ".$req_sup.$_SESSION["req_resto"];
        $result = $this->_sql->query($req);
        $ligne = $result->fetch();
        if($ligne!=""){
            $this->_nbRes = $ligne["NB"];
        }else{
            $this->_nbRes = 0;
        }
        $this->_nbPages = $this->_nbRes/$nbmess;
        $this->_nbPages = ceil($this->_nbPages);
    }

    public function getAll($page, $nbmess, $id_livreur, $commercant, $statut, $periode, $histo) {
        $req_sup="";
        $req_limit="";
        $tri="";

        if ($page!="" && $nbmess!="") {
            $page = $page - 1;
            $pt = ($page*$nbmess);
            if($pt<0){$pt = 1;}

            $req_limit=" LIMIT ".$pt.", ".$nbmess;
        }
        if ($id_livreur!=0) {
            $req_sup.=" AND c.livreur='".$id_livreur."'";
        }
        if ($commercant!="") {
            $req_sup.=" AND c.restaurant ='".$commercant."'";
        }
        if ($statut!="") {
            $req_sup.=" AND c.statut ='".$statut."'";
        }
        else {
            if($histo=="0"){
                $req_sup .= " AND c.statut in ('ajouté','réservé','récupéré') ";                        
            }
            else{
                $req_sup .= " AND c.statut in ('signé','echec') ";                      
            }
        }
        if($periode!=""){
            $periode_ = explode(" - ",$periode);

            if ($periode_[0]==$periode_[1]) {
                $datedebut = $periode_[0]." 00:00:00";
                $datefin = $periode_[1]." 23:59:59";
            }
            else {
                $datedebut = $periode_[0];
                $datefin = $periode_[1];
            }

            $req_sup .= " AND c.date_debut >= '".date("Y-m-d H:i:s",strtotime($datedebut))."' AND c.date_debut <= '".date("Y-m-d H:i:s",strtotime($datefin))."'"; 
        }
        if($_SESSION["role"]=="livreur"){
            $req_sup .= " AND c.livreur IN (0,".$_SESSION["userid"].")";
        }

        if($histo=="0"){
            $tri = " ORDER BY c.date_debut ASC ";                     
        }
        else{
            $tri = " ORDER BY c.date_debut DESC ";                        
        }

        $result = $this->_sql->query("SELECT c.id, c.livreur, c.date_debut, c.date_fin, c.statut, c.distance, c.duree, cl.nom as nom_client, cl.prenom as prenom_client, cl.adresse as adresse_client, cl.numero as numero_client, cl.email as email_client, cl.latitude as client_lat, cl.longitude as client_lng, r.nom as nom_resto, r.adresse as adresse_resto, r.latitude as lat_resto, r.longitude as lng_resto FROM commandes c LEFT JOIN clients cl ON c.client=cl.id LEFT JOIN restaurants r ON c.restaurant=r.id WHERE 1 ".$req_sup.$_SESSION["req_resto"].$tri.$req_limit);
        // Récupération des résultats sélectionnés dans le tableau $_listeCommande
        $_listeCommande = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeCommande;
    }

    public function setCommande($id_commande, $id_restaurant, $id_client, $id_livreur, $commentaire, $date_debut, $date_fin, $distance, $duree) {
        $Commercant=new Commercant($this->_sql, $id_restaurant);

        if ($id_livreur=="") {
            $id_livreur=0;
            $statut="ajouté";
        }
        else {
            $statut="réservé";
        }

        if ($distance=="") $distance=0;
        if ($duree=="") $duree=0;

        if ($id_commande=="") {
           $result = $this->_sql->exec("INSERT INTO commandes (restaurant, client, livreur, commentaire, date_debut, date_fin, date_ajout, statut, date_statut, distance, duree) VALUES (".$this->_sql->quote($id_restaurant).",".$this->_sql->quote($id_client).",".$this->_sql->quote($id_livreur).",".$this->_sql->quote($commentaire).",".$this->_sql->quote($date_debut).",".$this->_sql->quote($date_fin).",NOW(), '".$statut."',NOW(),'".$distance."','".$duree."')");      
            $id_commande=$this->_sql->lastInsertId();
            //insertion de la notif si commercant
            if ($_SESSION["restaurateur"]) {
                $result = $this->_sql->exec("INSERT INTO notifications (id_commande, id_commercant, type, date) VALUES (".$this->_sql->quote($id_commande).",".$this->_sql->quote($id_restaurant).", 'ajout', NOW())");
            }

            //envoyer une notif push au livreur si statut=réservé
            if ($statut=="réservé") {
                //envoi de la notif si commande modifiée
                $message="Nouvelle commande de ".$Commercant->getNom()." à livrer entre ".date("H:i", strtotime($date_debut)).' et '.date("H:i", strtotime($date_fin));
                $url="commandes.html?tab=2";
                $envoi=file('http://www.you-order.eu/admin/action_poo.php?action=send_push&id='.$id_livreur.'&message='.urlencode($message).'&url='.urlencode($url));
            }
        }
        else {
            $result = $this->_sql->exec("UPDATE commandes SET restaurant=".$this->_sql->quote($id_restaurant).",client=".$this->_sql->quote($id_client).",livreur=".$this->_sql->quote($id_livreur).",commentaire=".$this->_sql->quote($commentaire).",date_debut=".$this->_sql->quote($date_debut).", date_fin=".$this->_sql->quote($date_fin).", distance='".$distance."', duree='".$duree."' WHERE id = ".$this->_sql->quote($id_commande));
            //insertion de la notif si commande posté par un commercant
            if ($_SESSION["restaurateur"]) {
                $result = $this->_sql->exec("INSERT INTO notifications (id_commande, id_commercant, type, date) VALUES (".$this->_sql->quote($id_commande).",".$this->_sql->quote($id_restaurant).", 'modif', NOW())");
            }

            if ($statut=="réservé") {
                //envoi de la notif si commande modifiée
                $message="Modification de la commande ".$Commercant->getNom()." à livrer entre ".date("H:i", strtotime($date_debut)).' et '.date("H:i", strtotime($date_fin));
                $url="commandes.html?tab=2";
                $envoi=file('http://www.you-order.eu/admin/action_poo.php?action=send_push&id='.$id_livreur.'&message='.urlencode($message).'&url='.urlencode($url));
            }
        }

        $this->changeStatut($id_commande, "ajouté", $_SESSION["userid"], 0);
        if ($id_livreur!=0) {
            $this->changeStatut($id_commande, "réservé", $_SESSION["userid"], $id_livreur);
        }
        return $id_commande;
    }

    public function setLivreur($id_commande, $id_livreur) {
        $result = $this->_sql->exec("UPDATE commandes SET livreur=".$this->_sql->quote($id_livreur)." WHERE id = ".$this->_sql->quote($id_commande));
        if ($id_livreur==0) {
            $statut="ajouté";
        }
        else {
            $statut="réservé";
        }
        $this->changeStatut($id_commande, $statut, $_SESSION["userid"], $id_livreur);
    }

    public function changeStatut($id_commande, $statut, $id_user, $id_livreur) {
        $update_date="";
        $body = "";

        $result = $this->_sql->exec("UPDATE commandes SET statut=".$this->_sql->quote($statut)."date_statut = NOW() WHERE id = ".$this->_sql->quote($id_commande));

        //récupérer les données concernant la commande
        $result = $this->_sql->query("SELECT * FROM commandes WHERE id=".$this->_sql->quote($id_commande));
        $_listeCommande = $result->fetchAll(PDO::FETCH_OBJ);

        //ajouter la ligne correspondant au statut dans la table commande_historique
        if (!$this->checkStatut($id_commande, $statut)) {
            $result = $this->_sql->exec("INSERT INTO commandes_historique (id_commande, statut, date, id_user, id_livreur) VALUES (".$this->_sql->quote($id_commande).",".$this->_sql->quote($statut).", NOW(),".$this->_sql->quote($id_user).", ".$this->_sql->quote($id_livreur).")");

            //envoyer l'email correspondant au statut a tous les admin et planners
            $Commercant = new Commercant ($this->_sql, $_listeCommande[0]->restaurant);
            $Client     = new Client     ($this->_sql, $_listeCommande[0]->client);
            $Utilisateur= new Utilisateur($this->_sql);
            $Livreur    = new Livreur    ($this->_sql, $id_livreur);

            switch ($statut) {
                case "ajouté":
                    $sujet = "Nouvelle commande sur You Order";
                    $body = 'Bonjour, <br/><br/>
                            Nouvelle commande You Order de <b>'.$Commercant->getNom().'</b> <br/>
                            Détails : <br/>
                             - Adresse de livraison : '.$Client->getAdresse().'<br/>
                             - Créneau de livraison : '.date("d/m/Y", strtotime($_listeCommande[0]->date_debut)).' '.date("H:i", strtotime($_listeCommande[0]->date_debut)).' / '.date("H:i", strtotime($_listeCommande[0]->date_fin)).'<br/><br/>
                            <a href="https://you-order.eu/admin/commandes_visu.php?id='.$id_commande.'">Visualiser la commande</a><br/><br/>
                            Merci,<br/>
                            L\'équipe YouOrder';
                    break;

                case "réservé":
                    $sujet = "Commande réservée";
                    $body = 'Bonjour,<br/><br/>
                            La commande du commerçant <b>'.$Commercant->getNom().'</b> est réservée par '.$Livreur->getPrenom().' '.$Livreur->getNom().'<br/>
                            Détails : <br/>
                            - Adresse de livraison : '.$Client->getAdresse().'<br/>
                            - Créneau de livraison : '.date("d/m/Y", strtotime($_listeCommande[0]->date_debut)).' '.date("H:i", strtotime($_listeCommande[0]->date_debut)).' / '.date("H:i", strtotime($_listeCommande[0]->date_fin)).'<br/><br/>
                            <a href="https://you-order.eu/admin/commandes_visu.php?id='.$id_commande.'">Visualiser la commande</a><br/><br/>
                            Merci,<br/>
                            L\'équipe youOrder';
                    break;
            }

            //envoyer le mail a tous les planners
            foreach($Utilisateur->getAll("", "", "", "", "planner") as $planner) {            
                //envoyer un email a tous les planners
                $mail = new PHPMailer();
                $mail->From = "contact@youorder.fr";
                $mail->Sender = "contact@youorder.fr";
                $mail->FromName = "YouOrder";
                $mail->Subject = $sujet;
                $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
                $mail->MsgHTML($body);
                $mail->CharSet = 'UTF-8';    
                $mail->AddReplyTo("contact@youorder.fr","YouOrder");
                $mail->AddAddress($planner->email, "");
                $mail->send();
            }

            //envoyer le mail a tous les admins
            /*foreach($Utilisateur->getAll("", "", "", "", "admin") as $admin) {            
                //envoyer un email a tous les planners
                $mail = new PHPMailer();
                $mail->From = "contact@youorder.fr";
                $mail->Sender = "contact@youorder.fr";
                $mail->FromName = "YouOrder";
                $mail->Subject = "Nouvelle commande sur You Order";
                $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
                $mail->MsgHTML($body);
                $mail->CharSet = 'UTF-8';    
                $mail->AddReplyTo("contact@youorder.fr","YouOrder");
                $mail->AddAddress($admin->email, "");
                //$mail->AddBCC("contact@mgmobile.fr","");
                $mail->send();
            }*/
        }
        else {
            if ($statut!="ajouté") {
                $update_date=", date=NOW()";
            }
            $result = $this->_sql->exec("UPDATE commandes_historique SET id_user=".$this->_sql->quote($id_user).", id_livreur=".$this->_sql->quote($id_livreur)." ".$update_date." WHERE id_commande = ".$this->_sql->quote($id_commande)." AND statut=".$this->_sql->quote($statut));
            if ($statut=="ajouté" && $this->checkStatut($id_commande, "réservé")) {
                $result = $this->_sql->exec("DELETE FROM commandes_historique WHERE statut='réservé' AND id_commande = ".$this->_sql->quote($id_commande));
            }
        }
    }

    //vérifier si le statut existe déjà
    public function checkStatut($id_commande, $statut) {
        $check_statut=false;
        $result = $this->_sql->query("SELECT * FROM commandes_historique WHERE id_commande=".$this->_sql->quote($id_commande)." AND statut=".$this->_sql->quote($statut));
        $ligne = $result->fetch();
        if ($ligne) {
            $check_statut=true;
        }

        return $check_statut;
    }

    public function getAllStatut($id_commande) {
        $result = $this->_sql->query("SELECT c.*, u.nom as user_nom, u.prenom as user_prenom, l.nom as livreur_nom, l.prenom as livreur_prenom FROM commandes_historique c LEFT JOIN utilisateurs u ON c.id_user=u.id LEFT JOIN livreurs l ON c.id_livreur=l.id WHERE c.id_commande=".$this->_sql->quote($id_commande)." ORDER BY FIELD(c.statut, 'ajouté', 'réservé', 'récupéré', 'signé', 'echec')");
        // Récupération des résultats sélectionnés dans le tableau $_listeEtat
        $_listeEtat = $result->fetchAll(PDO::FETCH_OBJ);
        return $_listeEtat;
    }

    public function getNote($id_commande) {
        $result = $this->_sql->query("SELECT note FROM livreurs_notes WHERE id_commande=".$this->_sql->quote($id_commande));
        // Récupération des résultats sélectionnés dans le tableau $_listeEtat
        $_note = $result->fetchAll(PDO::FETCH_OBJ);
        return $_note[0]->note;
    }

    public function getEcoCarbonne($id_commande) {
        $result = $this->_sql->query("SELECT distance FROM commandes WHERE id=".$this->_sql->quote($id_commande));
        // Récupération des résultats sélectionnés dans le tableau $_listeEtat
        $ligne = $result->fetchAll(PDO::FETCH_OBJ);
        $distance_km = round($ligne[0]->distance/1000,0);
        $carbonne_voiture=(($distance_km*0.06981)*44)/12;
        $carbonne_electrique=(($distance_km*0.03946)*44)/12;
        return round($carbonne_voiture-$carbonne_electrique, 2);
    }

    public function getRecupCommande(){
        $result = $this->_sql->query("SELECT * FROM commandes WHERE (statut != 'signe' AND statut != 'echec' AND statut != 'supprime') ");

        $_commandeRecup = $result->fetchAll(PDO::FETCH_OBJ);
        return $_commandeRecup ;

    }

    public function validation($id_commande, $id_user, $id_livreur){

    $result = $this->_sql->exec("UPDATE commandes SET statut= 'signé', date_statut= NOW() WHERE id= ".$this->_sql->quote($id_commande));
    $result2 = $this->_sql->exec("INSERT INTO commandes_historique (id_commande, statut, date, id_user, id_livreur) 
                                         VALUES (".$this->_sql->quote($id_commande).", 'signé', NOW(),".$this->_sql->quote($id_user).", ".$this->_sql->quote($id_livreur).")");
            $id_commande = $this->_sql->lastInsertId();

        return $id_commande;
    }
}
?>