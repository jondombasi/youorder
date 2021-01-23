<?php
        require_once("inc_connexion.php");
  
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    //echo "SELECT id FROM utilisateurs WHERE email=".$sql->quote($email)." AND password = ".$sql->quote($password)."<br>";
    $resulta = $sql->query("SELECT id,nom,prenom FROM utilisateurs WHERE (role='livreur' OR  role='admin') and email=".$sql->quote($email)." AND password = ".$sql->quote($password)); //
    $ligne = $resulta->fetch();
    if($ligne!=""){
        //$prenom = $ligne["prenom"] ;
        //$nom = $ligne["nom"];
        echo json_encode($ligne);
    }else{
        $id = "-1";
        echo $id;
    }
    
    /*
    echo $resulta;
     echo "<pre>";
     var_dump($ligne);
     echo "</pre>";
    */
?>