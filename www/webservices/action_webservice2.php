<?php
header('Content-Type: multipart/form-data');
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

if(isset($_GET["action"])){$action=$_GET["action"];}else{$action="";}

switch ($action) {
    case "save_signature":
        if(isset($_POST["signature"])){$signature=$_POST["signature"];}else{$signature="";}
        if(isset($_POST["id_commande"])){$id_commande=$_POST["id_commande"];}else{$id_commande=0;}
        if(isset($_POST["id_livreur"])){$id_livreur=$_POST["id_livreur"];}else{$id_livreur=0;}
        if(isset($_POST["statut"])){$statut=$_POST["statut"];}else{$statut="";}

        $continuUpload = true;
        if(isset($_POST)) {
            //echo $_SERVER['DOCUMENT_ROOT'];
            ############ Edit settings ##############
            $BigImageMaxSize        = 800; //Image Maximum height or width
            $DestinationDirectory   = '/home/www/you-order/www/admin/signature/'; //specify upload directory ends with / (slash)
            $Quality                = 90; //jpeg quality
            ##########################################

            //var_dump($_FILES);
            //var_dump($_POST);
            
            // check $_FILES['signature'] not empty
            if(!isset($_FILES['signature']) || !is_uploaded_file($_FILES['signature']['tmp_name'])) {
                //die('Something wrong with uploaded file, something missing!'); // output error when above checks fail.
                $continuUpload = false;
            }

            if ($continuUpload) {
                // Random number will be added after image name

                $ImageName      = str_replace(' ','-',strtolower($_FILES['signature']['name'])); //get image name
                $ImageSize      = $_FILES['signature']['size']; // get original image size
                $TempSrc        = $_FILES['signature']['tmp_name']; // Temp name of image file stored in PHP tmp folder
                $ImageType      = $_FILES['signature']['type']; //get file type, returns "image/png", image/jpeg, text/plain etc.   

                //Let's check allowed $ImageType, we use PHP SWITCH statement here
                switch(strtolower($ImageType)) {
                    case 'image/png':
                        //Create a new image from file 
                        $CreatedImage =  imagecreatefrompng($_FILES['signature']['tmp_name']);
                        break;
                    case 'image/gif':
                        $CreatedImage =  imagecreatefromgif($_FILES['signature']['tmp_name']);
                        break;          
                    case 'image/jpeg':
                    case 'image/pjpeg':
                        $CreatedImage = imagecreatefromjpeg($_FILES['signature']['tmp_name']);
                        break;
                    default:
                        //output error and exit
                        die('Unsupported File!'); 
                }

                
                //PHP getimagesize() function returns height/width from image file stored in PHP tmp folder.
                //Get first two values from image, width and height. 
                //list assign svalues to $CurWidth,$CurHeight
                list($CurWidth,$CurHeight)=getimagesize($TempSrc);
                
                //Get file extension from Image name, this will be added after random name
                $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
                $ImageExt = str_replace('.','',$ImageExt);
                
                //remove extension from filename
                $ImageName = preg_replace("/\\.[^.\\s]{3,4}$/", "", $ImageName); 

                //Construct a new name with random number and extension.
                //$NewImageName = $RandomNumber.'.'.$ImageExt;
                $NewImageName="signature_".$_POST["id_commande"]."_".date("YmdHis").".".$ImageExt;
                
                //set the Destination Image
                $DestRandImageName = $DestinationDirectory.$NewImageName; // Image with destination directory

                move_uploaded_file($TempSrc, $DestRandImageName);
                crop_white($NewImageName);

                $result = $sql->query("SELECT * FROM commandes WHERE id=".$sql->quote($_POST["id_commande"]));
                $ligne = $result->fetch();
                if ($ligne) {
                  $id_livreur=$ligne["livreur"];
                  $id_commercant=$ligne["restaurant"];
                  $id_client=$ligne["client"];
                }
                $result = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, date, id_user, id_livreur) VALUES (".$_POST["id_commande"].", 'signé', NOW(), ".$id_livreur.", ".$id_livreur.")");
                $result = $sql->exec("UPDATE commandes SET statut='signé', date_statut=NOW(), signature=".$sql->quote($NewImageName)." WHERE id=".$sql->quote($_POST["id_commande"]));
                //echo "image upload dans ".$DestRandImageName;

                //envoyer l'email correspondant au statut a tous les admin et planners
                $Commercant=new Commercant($sql, $id_commercant);
                $Client=new Client($sql, $id_client);
                $Utilisateur=new Utilisateur($sql);
                $Livreur=new Livreur($sql, $id_livreur);

                $sujet = "Commande livrée";
                $body = 'Bonjour, <br/><br/>
                        La commande du commerçant <b>'.$Commercant->getNom().'</b> est signée<br/><br/>
                        <a href="http://www.youorder.fr/mobile/?id='.$id_commande.'">Suivre la signature</a><br/><br/>
                        Merci,<br/>
                        L\'équipe YouOrder';

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

                //Envoi du SMS de notation au client
                // URL for sending request
                $postUrl = "https://api.infobip.com/sms/1/text/advanced";

                //echo substr_replace($Client->getNumero(),"33",0,1);
                $to=str_replace(" ","", substr_replace($Client->getNumero(),"33",0,1));;
                $from="You Order";
                $text="Merci pour votre confiance, a bientot ! Evaluez gratuitement votre livreur sur www.you-order.eu/suivi_commande/notation.php?id=".$id_commande;

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

                $result = $sql->exec("INSERT INTO sms_copies (texte, id_client, id_commande, statut, date) VALUES (".$sql->quote($text).", ".$sql->quote($id_client).", ".$sql->quote($id_commande).", 'signé', NOW())");      
            }
            else {
                die('Error continue'); 
            }
        }
        else {
            die('Error POST'); 
        }
        break;
}

function crop_white($lien) {
  //load the image
  $img = imagecreatefromjpeg("http://www.you-order.eu/admin/signature/".$lien);

  //find the size of the borders
  $b_top = 0;
  $b_btm = 0;
  $b_lft = 0;
  $b_rt = 0;

  //top
  for(; $b_top < imagesy($img); ++$b_top) {
    for($x = 0; $x < imagesx($img); ++$x) {
      if(imagecolorat($img, $x, $b_top) != 0xFFFFFF) {
        break 2; //out of the 'top' loop
      }
    }
  }

  //bottom
  for(; $b_btm < imagesy($img); ++$b_btm) {
    for($x = 0; $x < imagesx($img); ++$x) {
      if(imagecolorat($img, $x, imagesy($img) - $b_btm-1) != 0xFFFFFF) {
        break 2; //out of the 'bottom' loop
      }
    }
  }

  //left
  for(; $b_lft < imagesx($img); ++$b_lft) {
    for($y = 0; $y < imagesy($img); ++$y) {
      if(imagecolorat($img, $b_lft, $y) != 0xFFFFFF) {
        break 2; //out of the 'left' loop
      }
    }
  }

  //right
  for(; $b_rt < imagesx($img); ++$b_rt) {
    for($y = 0; $y < imagesy($img); ++$y) {
      if(imagecolorat($img, imagesx($img) - $b_rt-1, $y) != 0xFFFFFF) {
        break 2; //out of the 'right' loop
      }
    }
  }

  //copy the contents, excluding the border
  $newimg = imagecreatetruecolor(imagesx($img)-($b_lft+$b_rt), imagesy($img)-($b_top+$b_btm));

  imagecopy($newimg, $img, 0, 0, $b_lft, $b_top, imagesx($newimg), imagesy($newimg));

  $DestinationDirectory   = '/home/www/you-order/www/admin/signature/'; //specify upload directory ends with / (slash)  

  //Get file extension from Image name, this will be added after random name
  $explode = explode(".", $lien);
  $ImageExt=$explode[1];
  $ImageName=$explode[0];

  //Construct a new name with random number and extension.
  $NewImageName=$ImageName."_crop.".$ImageExt;

  //set the Destination Image
  $DestRandImageName = $DestinationDirectory.$NewImageName; // Image with destination directory
      

  //finally, output the image
  header("Content-Type: image/jpeg");
  imagejpeg($newimg);
  imagejpeg($newimg, $DestRandImageName);
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