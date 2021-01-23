<?php
    $registration_ids= $_GET["registration_id"]; 
    $title = $_GET["title"];
    $message = $_GET["message"];
    $urlappli = $_GET["urlappli"];
    //echo $message;
    // Set POST variables
    $url = 'https://android.googleapis.com/gcm/send';
    
    $fields = array(
        'registration_ids' => array($registration_ids),
        'data' => array(
            "id" => ($id),
            "urlimage" => ($urlimage),
            "message" => ($message),
            "title" => ($title),
            "url" => ($urlappli),
            )
        );
  
    
    $headers = array(
        'Authorization: key=' . "AIzaSyCFuT98Kc668ZOEGTJP4nNusvoowbR1NAs",
        'Content-type: application/json; Charset="UTF-8"'
    );
    
    // Open connection
    $ch = curl_init();
    
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    
    // Close connection
    curl_close($ch);
    
    //$reponse = "1";
   
    //return $reponse;


	$pushStatus = "";
    

    echo $result;
?>
