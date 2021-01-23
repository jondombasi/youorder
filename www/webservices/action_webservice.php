<?php
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "fr_FR"); 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-type: application/json');
setlocale(LC_TIME, "fr_FR");
error_reporting(E_ALL);
ini_set("display_errors", 1);
$sql_serveur	= "localhost";
$sql_user		= "youorder";
$sql_passwd		= "75LrhfPSOqCv";
$sql_bdd		= "youorder";
$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );


require_once($_SERVER['DOCUMENT_ROOT'].'/admin/PHPMailer/class.phpmailer.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Commercant.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Client.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Livreur.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Utilisateur.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Commande.php');
	
if(isset($_GET["action"])){$action=$_GET["action"];}else{$action="";}
if(isset($_GET["id_livreur"])){$id_livreur=$_GET["id_livreur"];}else{$id_livreur=0;}
if(isset($_GET["id_commande"])){$id_commande=$_GET["id_commande"];}else{$id_commande="";}
if(isset($_GET["id_commercant"])){$id_commercant=$_GET["id_commercant"];}else{$id_commercant=0;}
if(isset($_GET["id_vehicule"])){$id_vehicule=$_GET["id_vehicule"];}else{$id_vehicule=0;}
if(isset($_GET["id_planning"])){$id_planning=$_GET["id_planning"];}else{$id_planning=0;}
if(isset($_GET["statut"])){$statut=$_GET["statut"];}else{$statut="";}
if(isset($_GET["raison_refus"])){$raison_refus=$_GET["raison_refus"];}else{$raison_refus=0;}
if(isset($_GET["comm_refus"])){$comm_refus=$_GET["comm_refus"];}else{$comm_refus="";}
if(isset($_GET["email"])){$email=$_GET["email"];}else{$email="";}
if(isset($_GET["password"])){$password=$_GET["password"];}else{$password="";}
if(isset($_GET["id_connexion"])){$id_connexion=$_GET["id_connexion"];}else{$id_connexion="";}
if(isset($_GET["date_planning"])){$date_planning=$_GET["date_planning"];}else{$date_planning=date("Y-m-d");}
if(isset($_GET["date_debut"])){$date_debut=$_GET["date_debut"];}else{$date_debut="";}
if(isset($_GET["date_fin"])){$date_fin=$_GET["date_fin"];}else{$date_fin="";}
if(isset($_GET["device_token"])){$device_token=$_GET["device_token"];}else{$device_token="";}
if(isset($_GET["erreur"])){$erreur=$_GET["erreur"];}else{$erreur="";}

//position
$lat       = isset($_GET['lat']) ? $_GET['lat'] : '0';
$lat       = (float)str_replace(",", ".", $lat); // to handle European locale decimals
$lng      = isset($_GET['lng']) ? $_GET['lng'] : '0';
$lng      = (float)str_replace(",", ".", $lng);

if ($raison_refus=="") $raison_refus=0;

switch ($action) {
	case "get_commandes":
		$req = "SELECT * FROM commandes WHERE date_debut BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() + INTERVAL 30 DAY";
        $result = $sql->query($req);
        $listeCommande = $result->fetchAll(PDO::FETCH_OBJ);
        echo json_encode($listeCommande);
		break;

    case "get_commandes_histo":
        $req = "SELECT * FROM commandes WHERE statut IN ('signé', 'echec') AND livreur=".$sql->quote($id_livreur);
        $result = $sql->query($req);
        $listeCommandeHisto = $result->fetchAll(PDO::FETCH_OBJ);
        echo json_encode($listeCommandeHisto);
        break;

	case "get_clients":
		$req = "SELECT c.* FROM clients c LEFT JOIN commandes co ON co.client=c.id WHERE co.livreur=".$sql->quote($id_livreur)." OR co.statut='ajouté' GROUP BY c.id";
        $result = $sql->query($req);
        $listeClient = $result->fetchAll(PDO::FETCH_OBJ);
        echo json_encode($listeClient);
		break;

	case "get_restaurants":
        //$req = "SELECT r.* FROM restaurants r LEFT JOIN commandes co ON co.restaurant=r.id LEFT JOIN livreurs_planning p ON p.id_commercant=r.id WHERE (co.livreur=".$sql->quote($id_livreur)." OR co.statut='ajouté') OR p.id_livreur=".$sql->quote($id_livreur)." GROUP BY r.id ";
		//$req = "SELECT r.* FROM restaurants r LEFT JOIN commandes co ON co.restaurant=r.id WHERE co.date_debut BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() + INTERVAL 30 DAY GROUP BY r.id ";
        $req = "SELECT * FROM restaurants";
        $result = $sql->query($req);
        $listeResto = $result->fetchAll(PDO::FETCH_OBJ);
        echo json_encode($listeResto);
		break;

    case "get_planning":
        //$req = "SELECT p.*, r.nom, r.adresse, r.latitude, r.longitude, r.numero FROM livreurs_planning p LEFT JOIN restaurants r ON p.id_commercant=r.id WHERE p.id_livreur=".$sql->quote($id_livreur)." AND p.date_debut BETWEEN '".$date_planning." 00:00:00' AND '".$date_planning." 23:59:59'";
        $req = "SELECT p.*, r.nom, r.adresse, r.latitude, r.longitude, r.numero FROM livreurs_planning p LEFT JOIN restaurants r ON p.id_commercant=r.id WHERE p.id_livreur=".$sql->quote($id_livreur)." AND p.date_debut BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() + INTERVAL 30 DAY";
        $result = $sql->query($req);
        $listePlanning = $result->fetchAll(PDO::FETCH_OBJ);
        echo json_encode($listePlanning);
        break;

	case "change_statut":
        $Commande=new Commande($sql, $id_commande);
        $Commercant=new Commercant($sql, $Commande->getRestaurant());
        $Client=new Client($sql, $Commande->getClient());

		$result = $sql->exec("UPDATE commandes SET statut=".$sql->quote($statut).", date_statut=NOW(), raison_refus=".$sql->quote($raison_refus).", comm_refus=".$sql->quote($comm_refus).", livreur=".$sql->quote($id_livreur)." WHERE id = ".$sql->quote($id_commande));


        if ($statut=="récupéré" && $Commercant->getSmsClient()=="on") {

            //on vérifie si le sms a déjà été envoyé
            $result = $sql->query("SELECT * FROM sms_copies WHERE id_client=".$sql->quote($Commande->getClient())." AND id_commande=".$sql->quote($id_commande)." AND statut=".$sql->quote($statut));
            $ligne = $result->fetch();
            if (!$ligne) {
                // URL for sending request
                $postUrl = "https://api.infobip.com/sms/1/text/advanced";

                //echo substr_replace($Client->getNumero(),"33",0,1);
                $to=str_replace(" ","", substr_replace($Client->getNumero(),"33",0,1));;
                $from="You Order";
                $text=$Commercant->getSmsClientTxt()." www.you-order.eu/suivi_commande/suivi_commande.php?id=".$id_commande;

                // creating an object for sending SMS
                $destination = array("to" => $to);
                $message = array("from" => $from,
                        "destinations" => array($destination),
                        "text" => $text);
                $postData = array("messages" => array($message));
                // encoding object
                $postDataJson = json_encode($postData);

                $ch = curl_init();
                $header = array("Content-Type:application/json", "Accept:application/json");

                curl_setopt($ch, CURLOPT_URL, $postUrl);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "MGMOBILE2:M202714e");
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);

                // response of the POST request
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $responseBody = json_decode($response);
                curl_close($ch);

                $result = $sql->exec("INSERT INTO sms_copies (texte, id_client, id_commande, statut, date) VALUES (".$sql->quote($text).", ".$sql->quote($Commande->getClient()).", ".$sql->quote($id_commande).", ".$sql->quote($statut).", NOW())");
            }
        }

        //ajouter la ligne correspondant au statut dans la table commande_historique
        $result = $sql->query("SELECT * FROM commandes_historique WHERE id_commande=".$sql->quote($id_commande)." AND statut=".$sql->quote($statut));
        $ligne = $result->fetch();
        if ($ligne) {
            $result = $sql->exec("UPDATE commandes_historique SET id_user=".$sql->quote($id_livreur).", id_livreur=".$sql->quote($id_livreur).", date=NOW() WHERE id_commande = ".$sql->quote($id_commande)." AND statut=".$sql->quote($statut));
        }
        else {
        	$result = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, date, id_user, id_livreur) VALUES (".$sql->quote($id_commande).",".$sql->quote($statut).", NOW(),".$sql->quote($id_livreur).", ".$sql->quote($id_livreur).")");
        }

        //récupérer les données concernant la commande
        $result = $sql->query("SELECT * FROM commandes WHERE id=".$sql->quote($id_commande));
        $_listeCommande = $result->fetchAll(PDO::FETCH_OBJ);

        //envoyer l'email correspondant au statut a tous les admin et planners
        $Commercant=new Commercant($sql, $_listeCommande[0]->restaurant);
        $Client=new Client($sql, $_listeCommande[0]->client);
        $Utilisateur=new Utilisateur($sql);
        $Livreur=new Livreur($sql, $id_livreur);

        switch ($statut) {
            case "réservé":
                $sujet = "Commande réservée";
                $body = 'Bonjour,<br/><br/>
                        La commande du commerçant <b>'.$Commercant->getNom().'</b> est réservée par '.$Livreur->getPrenom().' '.$Livreur->getNom().'<br/>
                        Détails : <br/>
                        - Adresse de livraison : '.$Client->getAdresse().'<br/>
                        - Créneau de livraison : '.date("d/m/Y", strtotime($_listeCommande[0]->date_debut)).' '.date("H:i", strtotime($_listeCommande[0]->date_debut)).' / '.date("H:i", strtotime($_listeCommande[0]->date_fin)).'<br/><br/>
                        <a href="http://you-order.eu/admin/commandes_visu.php?id='.$id_commande.'">Visualiser la commande</a><br/><br/>
                        Merci,<br/>
                        L\'équipe youOrder';
                break;

            case "récupéré":
                $sujet="Votre livraison ".$Commercant->getNom();
                $body = 'Bonjour, <br/><br/>
                        Votre commande de <b>'.$Commercant->getNom().'</b> est en cours de livraison : <br/>
                        <a href="http://www.youorder.fr/mobile/?id='.$id_commande.'">Suivre la commande</a><br/><br/>
                        Merci,<br/>
                        L\'équipe YouOrder';
                break;

            case "signé":
                $sujet = "Commande livrée";
                $body = 'Bonjour, <br/><br/>
                        La commande du commerçant <b>'.$Commercant->getNom().'</b> est signée<br/><br/>
                        <a href="http://www.youorder.fr/mobile/?id='.$id_commande.'">Suivre la signature</a><br/><br/>
                        Merci,<br/>
                        L\'équipe YouOrder';
                break;

            case "echec":
                $sujet = 'Echec de la commande';
                $body = 'Bonjour, <br/><br/>
                        La commande du commerçant <b>'.$Commercant->getNom().'</b> n\'a pas été livrée<br/><br/>
                        <a href="http://www.youorder.fr/mobile/?id='.$id_commande.'">Visualiser la commande</a><br/><br/>
                        Merci,<br/>
                        L\'équipe YouOrder';
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
            //$mail->AddBCC("contact@mgmobile.fr","");
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

        echo json_encode("ok");
		break;

    case "connexion":
        $array = array(); 
        $result = $sql->query("SELECT * FROM livreurs WHERE email=".$sql->quote($email)." AND password=".$sql->quote($password)." AND statut IN ('ON', 'OFF')");
        $ligne = $result->fetch();
        if ($ligne) {
            $array["statut"]="ok";
            $array["id_livreur"]=$ligne["id"];
            $array["telephone"]=$ligne["telephone"];
            $array["nom"]=$ligne["nom"];
            $array["prenom"]=$ligne["prenom"];
            $array["photo"]=$ligne["photo"];
            $array["statut_connexion"]=($ligne["statut"]=='ON') ? "connecte" : "deconnecte";
            $result = $sql->exec("UPDATE livreurs SET date_connexion=NOW() WHERE id = ".$sql->quote($ligne["id"]));
        }
        else {
            $array["statut"]="erreur";
        }
        echo json_encode($array);
        break;

    case "commencer_service":
        if ($id_vehicule=="") $id_vehicule=0;

        $result = $sql->query("SELECT * FROM livreurs_planning WHERE id=".$sql->quote($id_planning));
        $ligne = $result->fetch();
        if ($ligne) {
            if (date("Y-m-d H:i:s")<$ligne["date_debut"]) {
                $true_date=$ligne["date_debut"];
            }
            else {
                $true_date=date("Y-m-d H:i:s");
            }
        }

        $result = $sql->exec("INSERT INTO livreurs_connexion (id_livreur, id_commercant, id_vehicule, id_planning, date_connexion, date_deconnexion, type) VALUES (".$sql->quote($id_livreur).", ".$sql->quote($id_commercant).", ".$sql->quote($id_vehicule).", ".$sql->quote($id_planning).", ".$sql->quote($true_date).", ".$sql->quote($true_date).", 'appli')");
        $result = $sql->exec("UPDATE livreurs SET statut='ON', date_connexion=NOW() WHERE id = ".$sql->quote($id_livreur));

        //Something to write to txt log
        $log  = "UPDATE: ".$_SERVER['REMOTE_ADDR'].' - '.date("d/m/Y H:i:s").PHP_EOL.
        "Type: Debut".PHP_EOL.
        "Livreur: ".$id_livreur.PHP_EOL.
        "Commercant: ".$id_commercant.PHP_EOL.
        "Vehicule: ".$id_vehicule.PHP_EOL.
        "Id Planning: ".$id_planning.PHP_EOL.
        "-------------------------".PHP_EOL;

        //Save string to log, use FILE_APPEND to append.
        file_put_contents('./log_service'.date("dmY").'.txt', $log, FILE_APPEND);
        break;

    case "finir_service":
        //on vérifie que les données de connexion existe bien ou on récupère le dernier shift non mis a jour
        //$result = $sql->query("SELECT * FROM livreurs_connexion WHERE id_planning=".$sql->quote($id_planning)." ORDER BY date_connexion DESC LIMIT 1");
        $result = $sql->query("SELECT c.*, p.date_debut, p.date_fin, l.nom as nom_livreur, l.prenom as prenom_livreur, r.nom as nom_resto FROM livreurs_connexion c INNER JOIN livreurs_planning p ON c.id_planning=p.id INNER JOIN livreurs l ON c.id_livreur=l.id INNER JOIN restaurants r ON c.id_commercant=r.id WHERE (c.id_planning=".$sql->quote($id_planning).") OR (c.id_livreur=".$sql->quote($id_livreur)." AND c.date_connexion=c.date_deconnexion) ORDER BY c.date_connexion DESC LIMIT 1 ");
        $ligne = $result->fetch();
        if ($ligne) {
            $id_connexion=$ligne["id"];
            $id_planning=$ligne["id_planning"];
            $date_fin_planning=$ligne["date_fin"];
            $nom_livreur=$ligne["prenom_livreur"]." ".$ligne["nom_livreur"];
            $nom_resto=$ligne["nom_resto"];
        }

        //on vérifie si le livreur a fais des heures supplémentaire
        if ($date_fin_planning<date("Y-m-d H:i:s")) {
            //si oui, on met a jour avec la date de fin théorique
            $result = $sql->exec("UPDATE livreurs_connexion SET date_deconnexion=".$sql->quote($date_fin_planning)." WHERE id=".$sql->quote($id_connexion));

            $tps_retard=(strtotime(date("Y-m-d H:i:s"))-strtotime($date_fin_planning));
            $duree_h_retard = gmdate("H",$tps_retard);
            $duree_m_retard = gmdate("i",$tps_retard);
            $duree_aff_retard=($duree_h_retard>0) ? $duree_h_retard."h".$duree_m_retard : $duree_m_retard." min";

            //on envoie un email pour prévenir des heures supplémentaires
            $body = 'Bonjour, <br/><br/>
                    Un livreur a fait des heures supplémentaires : <br/>
                    - Nom du livreur : '.$nom_livreur.'<br/>
                    - Nom du commercant : '.$nom_resto.'<br/>
                    - Date de fin prévue : '.date("d/m/Y - H:i:s", strtotime($date_fin_planning)).'<br/>
                    - Date de deconnexion : '.date("d/m/Y - H:i:s").' (soit '.$duree_aff_retard.' supplémentaires)<br/>
                    --> <a href="https://www.you-order.eu/admin/livreurs_fiche2.php?id='.$id_livreur.'">Lien vers la fiche du livreur</a><br/>';

            $mail = new PHPMailer();
            $mail->From = "contact@youorder.fr";
            $mail->Sender = "contact@youorder.fr";
            $mail->FromName = "YouOrder";
            $mail->Subject = "Heures supplémentaires";
            $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
            $mail->MsgHTML($body);
            $mail->CharSet = 'UTF-8';    
            $mail->AddReplyTo("contact@youorder.fr","YouOrder");
            $mail->AddAddress("ops@youorder.fr", "");
            $mail->AddAddress("myriam@mgmobile.fr", "");
            $mail->send();
        }
        else {
            //si non, on met a jour avec la date actuelle
            $result = $sql->exec("UPDATE livreurs_connexion SET date_deconnexion=NOW() WHERE id=".$sql->quote($id_connexion));
        }       

        $result = $sql->exec("UPDATE livreurs SET statut='OFF' WHERE id = ".$sql->quote($id_livreur));

        //Something to write to txt log
        $log  = "UPDATE: ".$_SERVER['REMOTE_ADDR'].' - '.date("d/m/Y H:i:s").PHP_EOL.
        "Type: Fin".PHP_EOL.
        "Livreur: ".$id_livreur.PHP_EOL.
        "Commercant: ".$id_commercant.PHP_EOL.
        "Vehicule: ".$id_vehicule.PHP_EOL.
        "Id Planning: ".$id_planning.PHP_EOL.
        "-------------------------".PHP_EOL;

        //Save string to log, use FILE_APPEND to append.
        file_put_contents('./log_service'.date("dmY").'.txt', $log, FILE_APPEND);
        break;

    case "logs_service":
        //Something to write to txt log
        $log  = "ERREUR CONNEXION: ".$_SERVER['REMOTE_ADDR'].' - '.date("d/m/Y H:i:s").PHP_EOL.
        "Type erreur:".$erreur.PHP_EOL.
        "Livreur: ".$id_livreur.PHP_EOL.
        "Commercant: ".$id_commercant.PHP_EOL.
        "Vehicule: ".$id_vehicule.PHP_EOL.
        "Id Planning: ".$id_planning.PHP_EOL.
        "Position: ".$lat."/".$lng.PHP_EOL.
        "-------------------------".PHP_EOL;

        //Save string to log, use FILE_APPEND to append.
        file_put_contents('./log_erreurs'.date("dmY").'.txt', $log, FILE_APPEND);
        break;

    case "info_dashboard":
        $array = array();   
        $result = $sql->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(date_fin, date_debut)))) AS total_hours FROM livreurs_planning WHERE id_livreur=".$sql->quote($id_livreur)." AND date_debut BETWEEN '".date('Y-m-d')." 00:00:00' AND '".date('Y-m-d')." 23:59:59'");
        $ligne = $result->fetch();
        if ($ligne && $ligne["total_hours"]!=null && $ligne["total_hours"]!="") {
            //array_push($array, $ligne["total_hours"]);
            if (date("i", strtotime($ligne["total_hours"]))>0) {
                $array["nb_heures"]=date("H\hi", strtotime($ligne["total_hours"]));
            }
            else {
                $array["nb_heures"]=date("H", strtotime($ligne["total_hours"]));
            }
        }
        else {
            //array_push($array, "00:00:00");
            $array["nb_heures"]="00:00:00";
        }

        $result = $sql->query("SELECT COUNT(*) AS NB FROM commandes WHERE livreur=".$sql->quote($id_livreur)." AND statut='réservé' AND date_debut BETWEEN '".date('Y-m-d')." 00:00:00' AND '".date('Y-m-d')." 23:59:59'");
        $ligne = $result->fetch();
        if ($ligne) {
            //array_push($array, $ligne["NB"]);
            $array["nb_commandes"]=$ligne["NB"];
        }
        else {
            //array_push($array, "0");
            $array["nb_commandes"]="0";
        }

        $result = $sql->query("SELECT p.id, p.id_livreur, p.date_debut, p.date_fin, p.id_vehicule, p.id_commercant, r.nom as nom_resto, r.latitude, r.longitude, v.nom as nom_vehicule, v.immatriculation, v.marque, v.volume FROM livreurs_planning p LEFT JOIN restaurants r ON p.id_commercant=r.id LEFT JOIN vehicules v ON p.id_vehicule=v.id WHERE id_livreur=".$sql->quote($id_livreur)." AND (NOW() BETWEEN date_debut AND date_fin OR date_debut>=NOW()) ORDER BY date_debut ASC LIMIT 1");
        $ligne = $result->fetch();
        if ($ligne) { 
            $array["id_planning"]=$ligne["id"];
            if ($ligne["id_commercant"]==null || $ligne["id_commercant"]=="") {
                //array_push($array, "Non affecté");
                //array_push($array, " ");
                $array["nom_commercant"]="Non affecté";
                $array["id_commercant"]=0;
                $array["plage_horaire"]="";
                $array["longitude"]=0;
                $array["latitude"]=0;
            }
            else {
                //array_push($array, $ligne["nom_resto"]);
                $array["nom_commercant"]=$ligne["nom_resto"];
                $array["id_commercant"]=$ligne["id_commercant"];
                if (date("Y-m-d")==date("Y-m-d", strtotime($ligne["date_debut"]))) {
                    //array_push($array, "Entre ".date("H\hi", strtotime($ligne["date_debut"]))." et ".date("H\hi", strtotime($ligne["date_fin"])));
                    $array["plage_horaire"]="Entre ".date("H\hi", strtotime($ligne["date_debut"]))." et ".date("H\hi", strtotime($ligne["date_fin"]));
                }
                else {
                    //array_push($array, strftime("%A",strtotime($ligne["date_debut"])));
                    $array["plage_horaire"]=strftime("%A %d/%m",strtotime($ligne["date_debut"]));
                }
                $array["longitude"]=$ligne["longitude"];
                $array["latitude"]=$ligne["latitude"];
            }
            if ($ligne["id_vehicule"]==null || $ligne["id_vehicule"]=="" || $ligne["id_vehicule"]==0) {
                //array_push($array, "Non affecté");
                $array["vehicule"]="Non affecté";
                $array["id_vehicule"]="0";
            }
            else {
                //array_push($array, $ligne["nom_vehicule"]." ".$ligne["immatriculation"]." \"".$ligne["marque"]."\" ".$ligne["volume"]."L");
                $array["vehicule"]=$ligne["nom_vehicule"]." ".$ligne["immatriculation"]." \"".$ligne["marque"]."\" ".$ligne["volume"]."L";
                $array["id_vehicule"]=$ligne["id_vehicule"];
            }
        }
        else {
            $array["id_planning"]=0;
            $array["id_commercant"]=0;
            $array["nom_commercant"]="Non affecté";
            $array["plage_horaire"]="";
            $array["longitude"]=0;
            $array["latitude"]=0;
            $array["vehicule"]="Non affecté";
            $array["id_vehicule"]="0";
        }

        $result = $sql->query("SELECT statut FROM livreurs WHERE id=".$sql->quote($id_livreur));
        $ligne = $result->fetch();
        if ($ligne) {
            //array_push($array, $ligne["NB"]);
            $array["statut_connexion"]=($ligne["statut"]=='ON') ? "connecte" : "deconnecte";
        }

        echo json_encode($array);
        break;

    case "get_historique":
        $result = $sql->query("SELECT v.id, v.id_vehicule, v.etat, v.date, l.nom, l.prenom FROM vehicules_historique v LEFT JOIN livreurs l ON v.id_livreur=l.id WHERE v.id_vehicule=".$sql->quote($id_vehicule));
        $vehiculeHisto = $result->fetchAll(PDO::FETCH_OBJ);
        echo json_encode($vehiculeHisto);
        
        break;

    case "get_stats":
        $array = array();
        $cpt_retard=0;
        $tps_retard_week=0;
        $tps_retard_last=0;
        $date_debut.=" 00:00:00";
        $date_fin.=" 23:59:59";
        $date_debut_last=date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_debut." -7 days"))))." 00:00:00";
        $date_fin_last=date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_fin." -7 days"))))." 23:59:59";
        $cpt_recup_week=0;
        $cpt_signe_week=0;
        $cpt_echec_week=0;
        $cpt_recup_last=0;
        $cpt_signe_last=0;
        $cpt_echec_last=0;

        //récupérer le nb d'heures affectées au livreur et calculer le pourcentage par rapport aux heures effectuées
        $result = $sql->query("SELECT nb_heures, note, (SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(date_deconnexion, date_connexion)))) FROM livreurs_connexion WHERE id_livreur=".$sql->quote($id_livreur)." AND date_connexion BETWEEN ".$sql->quote($date_debut)." AND ".$sql->quote($date_fin).") as nb_heures_effectuees FROM livreurs WHERE id=".$sql->quote($id_livreur));
        $liste_stats = $result->fetchAll(PDO::FETCH_OBJ);
        $array["nb_heures_totales"]=$liste_stats[0]->nb_heures;
        $array["note_livreur"]=$liste_stats[0]->note;
        if ($liste_stats[0]->nb_heures_effectuees==null || $liste_stats[0]->nb_heures_effectuees=="") {
            $array["nb_heures_effectuees"]="0";
            $array["pc_heures"]="0";
        }
        else {
            //on récupère les heures/minutes/secondes
            list($hh,$mm,$ss) = explode(':',$liste_stats[0]->nb_heures_effectuees);
            if($mm>0) {
                $array["nb_heures_effectuees"]=$hh."h".$mm;
            }
            else {
                $array["nb_heures_effectuees"]=$hh."h";
            }
             $array["pc_heures"]=($hh/$array["nb_heures_totales"])*100;
            /*if (date("i", strtotime($liste_stats[0]->nb_heures_effectuees))>0) {
                $array["nb_heures_effectuees"]=date("H\hi", strtotime($liste_stats[0]->nb_heures_effectuees));
            }
            else {
                $array["nb_heures_effectuees"]=date("H", strtotime($liste_stats[0]->nb_heures_effectuees));
            }
            $array["pc_heures"]=(date("H", strtotime($liste_stats[0]->nb_heures_effectuees))/$array["nb_heures_totales"])*100;*/
        }

        //faire un tableau contenant toutes les dates de la semaine selectionnée
        for ($i=0;$i<7;$i++) {
            $array[date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_debut." +".$i." days"))))]["nb_heures"]="00h00";
        }

        //remplir le tableau précédent avec le nb d'heure par jour s'il y en a eu, sinon ça reste a 0
        $result2 = $sql->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(date_deconnexion, date_connexion)))) as nb_heures, DATE(date_connexion) as date FROM livreurs_connexion WHERE date_connexion BETWEEN ".$sql->quote($date_debut)." AND ".$sql->quote($date_fin)." AND id_livreur=".$sql->quote($id_livreur)." GROUP BY DATE(date_connexion)");
        while ($ligne = $result2->fetch()) {
            $array[$ligne["date"]]["nb_heures"]=date("H\hi", strtotime($ligne["nb_heures"]));
        }

        //calculer le nb de retard et le temps de retard de la semaine choisie
        //$result3 = $sql->query("SELECT p.*, c.date_connexion, c.date_deconnexion FROM livreurs_planning p LEFT JOIN livreurs_connexion c ON p.id=c.id_planning WHERE p.id_livreur=".$sql->quote($id_livreur)." AND p.date_debut BETWEEN ".$sql->quote($date_debut)." AND ".$sql->quote($date_fin)."  AND p.date_debut<NOW()");
        $result3 = $sql->query("SELECT ANY_VALUE(p.id) as id_planning, ANY_VALUE(p.date_debut) as date_debut ,ANY_VALUE(p.date_fin) as date_fin, ANY_VALUE(c.date_connexion) as date_connexion, ANY_VALUE(c.date_deconnexion) as date_deconnexion FROM livreurs_planning p LEFT JOIN livreurs_connexion c ON p.id=c.id_planning WHERE p.id_livreur=".$sql->quote($id_livreur)." AND p.date_debut BETWEEN ".$sql->quote($date_debut)." AND ".$sql->quote($date_fin)." AND p.date_debut<NOW() GROUP BY p.id ORDER BY date_connexion");
        while ($ligne3 = $result3->fetch()) {
            if ($ligne3["date_connexion"]=="" || $ligne3["date_connexion"]==null) {
                $cpt_retard++;
                //ne pas compter dans les heures de retard si c'est une abscence
                //$tps_retard_week+=(strtotime($ligne3["date_fin"])-strtotime($ligne3["date_debut"]));
            }
            else {
                if (strtotime($ligne3["date_connexion"])-strtotime($ligne3["date_debut"])>0) {
                    $cpt_retard++;
                    $tps_retard_week+=(strtotime($ligne3["date_connexion"])-strtotime($ligne3["date_debut"]));
                }
            }
        }

        $duree_h_week = gmdate("H",$tps_retard_week);
        $duree_m_week = gmdate("i",$tps_retard_week);
        $duree_aff_week=($duree_h_week>0) ? $duree_h_week."h".$duree_m_week : $duree_m_week." min";
        $array["stats_cumul_retard"]=$cpt_retard;
        $array["stats_retard"]=$duree_aff_week;

        //calculer le retard accumulé de la semaine précédent celle selectionnée
        $result4 = $sql->query("SELECT p.*, c.date_connexion, c.date_deconnexion FROM livreurs_planning p LEFT JOIN livreurs_connexion c ON p.id=c.id_planning WHERE p.id_livreur=".$sql->quote($id_livreur)." AND p.date_debut BETWEEN ".$sql->quote($date_debut_last)." AND ".$sql->quote($date_fin_last)." AND p.date_debut<NOW()");
        while ($ligne4 = $result4->fetch()) {
            if ($ligne4["date_connexion"]=="" || $ligne4["date_connexion"]==null) {
                //$tps_retard_last+=(strtotime($ligne4["date_fin"])-strtotime($ligne4["date_debut"]));
            }
            else {
                if (strtotime($ligne4["date_connexion"])-strtotime($ligne4["date_debut"])>0) {
                    $tps_retard_last+=(strtotime($ligne4["date_connexion"])-strtotime($ligne4["date_debut"]));
                }
            }
        }

        if ($tps_retard_week>=$tps_retard_last) {
            $duree_h_last = gmdate("H",$tps_retard_week-$tps_retard_last);
            $duree_m_last = gmdate("i",$tps_retard_week-$tps_retard_last);
            $duree_aff_last=($duree_h_last>0) ? $duree_h_last."h".$duree_m_last : $duree_m_last." min";
            $array["stats_retard_last"]=$duree_aff_last." de plus";
        }
        else {
            $duree_h_last = gmdate("H",$tps_retard_last-$tps_retard_week);
            $duree_m_last = gmdate("i",$tps_retard_last-$tps_retard_week);
            $duree_aff_last=($duree_h_last>0) ? $duree_h_last."h".$duree_m_last : $duree_m_last." min";
            $array["stats_retard_last"]=$duree_aff_last." de moins";
        }

        //calculer le nb de commandes par statut de la semaine selectionnée
        $result5 = $sql->query("SELECT * FROM commandes_historique WHERE id_livreur=".$sql->quote($id_livreur)." AND date BETWEEN ".$sql->quote($date_debut)." AND ".$sql->quote($date_fin));
        while ($ligne5 = $result5->fetch()) {
            switch ($ligne5["statut"]) {
                case "récupéré":
                    $cpt_recup_week++;
                    break;

                case "signé":
                    $cpt_signe_week++;
                    break;

                case "echec":
                    $cpt_echec_week++;
                    break;
            }
        }

        $array["commandes_recup_week"]=$cpt_recup_week;
        $array["commandes_signe_week"]=$cpt_signe_week;
        $array["commandes_echec_week"]=$cpt_echec_week;

        //calculer le nb de commandes par statut de la semaine passée
        $result6 = $sql->query("SELECT * FROM commandes_historique WHERE id_livreur=".$sql->quote($id_livreur)." AND date BETWEEN ".$sql->quote($date_debut_last)." AND ".$sql->quote($date_fin_last));
        while ($ligne6 = $result6->fetch()) {
            switch ($ligne6["statut"]) {
                case "récupéré":
                    $cpt_recup_last++;
                    break;

                case "signé":
                    $cpt_signe_last++;
                    break;

                case "echec":
                    $cpt_echec_last++;
                    break;
            }
        }

        $array["commandes_recup_last"]=$cpt_recup_last;
        $array["commandes_signe_last"]=$cpt_signe_last;
        $array["commandes_echec_last"]=$cpt_echec_last;

        echo json_encode($array);
        break;

    case "get_moyenne":
        $array = array(); 

        //faire un tableau contenant toutes les dates de la semaine dernière
        for ($i=0;$i<7;$i++) {
            $array[$i]["jour"]=date("Y-m-d", strtotime(date("Y-m-d", strtotime(" -".$i." days"))));
            $array[$i]["moyenne"]=0;
        }

        //calculer la note moyenne par jour
        $result = $sql->query("SELECT DATE(date) as date, AVG(note) as note_moyenne FROM livreurs_notes WHERE id_livreur=".$sql->quote($id_livreur)." AND date BETWEEN NOW() - INTERVAL 7 DAY AND NOW() GROUP BY DATE(date)");
        $listeMoyenne = $result->fetchAll(PDO::FETCH_OBJ);
        foreach($listeMoyenne as $ligne) {
            for ($i=0;$i<7;$i++) {
                if ($array[$i]["jour"]==$ligne->date) $array[$i]["moyenne"]=intVal($ligne->note_moyenne);
                //$array[$i][$ligne->date]=intVal($ligne->note_moyenne);
            }
        }

        echo json_encode($array);
        break;

    case "set_token":
        $result = $sql->exec("UPDATE livreurs SET device_id=".$sql->quote($device_token)." WHERE id = ".$sql->quote($id_livreur));
        echo json_encode("ok");
        break;

    case "check_service":
        $result = $sql->query("SELECT * FROM livreurs_planning WHERE id_livreur=".$sql->quote($id_livreur)." AND NOW() BETWEEN date_debut - INTERVAL 5 MINUTE AND date_fin");
        $ligne = $result->fetch();
        if ($ligne) {
            //echo json_encode("ok");
            $reponse="ok";
        }
        else {
            $result = $sql->query("SELECT * FROM livreurs_planning WHERE id_livreur=".$sql->quote($id_livreur)." AND (NOW() BETWEEN date_debut AND date_fin OR date_debut>=NOW()) ORDER BY date_debut ASC LIMIT 1");
            $ligne = $result->fetch();
            if ($ligne) {
                //echo json_encode("Merci de recommencer au prochain service le ".strftime("%d %B à %Hh%M", strtotime($ligne["date_debut"])));
                $reponse=utf8_encode("Merci de recommencer au prochain service le ".strftime("%d %B &agrave; %Hh%M", strtotime($ligne["date_debut"])));
                $envoi=file('http://www.you-order.eu/webservices/action_webservice.php?action=logs_service&erreur=trop_tot&id_livreur='.urlencode($id_livreur)."&id_commercant=".urlencode($ligne["id_commercant"])."&id_vehicule=".urlencode($ligne["id_vehicule"])."&id_planning=".urlencode($ligne["id"]));
                //echo $reponse;
            }
            else {
                $reponse="ko";
                $envoi=file('http://www.you-order.eu/webservices/action_webservice.php?action=logs_service&erreur=no_service&id_livreur='.urlencode($id_livreur));
            }
        }
        echo json_encode($reponse);
        break;

    case "get_notifications":
        $result = $sql->query("SELECT * FROM notifications_push WHERE (destinataire LIKE '%".$id_livreur.",%' OR destinataire='tous') AND statut='send' ORDER BY date_envoi DESC");
        $notifications = $result->fetchAll(PDO::FETCH_OBJ);
        echo json_encode($notifications);
        break;
}

function newChaine( $chrs = "") {
    if( $chrs == "" ) $chrs = 4;
    $chaine = ""; 

    $list = "23456789abcdefghjkmnpqrstuvwxyz";
    mt_srand((double)microtime()*1000000);
    $newstring="";

    while( strlen( $newstring )< $chrs ) {
        $newstring .= $list[mt_rand(0, strlen($list)-1)];
    }
    return $newstring;
}

?>
