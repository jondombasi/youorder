<?php

	class MyPayPal {
		
		function GetItemTotalPrice($item){
		
			//(Item Price x Quantity = Total) Get total amount of product;
			return $item['ItemPrice'] * $item['ItemQty']; 
		}
		
		function GetProductsTotalAmount($products){
		
			$ProductsTotalAmount=0;

			foreach($products as $p => $item){
				
				$ProductsTotalAmount = $ProductsTotalAmount + $this -> GetItemTotalPrice($item);	
			}
			
			return $ProductsTotalAmount;
		}
		
		function GetGrandTotal($products, $charges){
			
			//Grand total including all tax, insurance, shipping cost and discount
			
			$GrandTotal = $this -> GetProductsTotalAmount($products);
			
			foreach($charges as $charge){
				
				$GrandTotal = $GrandTotal + $charge;
			}
			
			return $GrandTotal;
		}
		
		function SetExpressCheckout($products, $charges, $noshipping='1'){
			
			//Parameters for SetExpressCheckout, which will be sent to PayPal
			
			$padata  = 	'&METHOD=SetExpressCheckout';
			
			$padata .= 	'&RETURNURL='.urlencode(PPL_RETURN_URL);
			$padata .=	'&CANCELURL='.urlencode(PPL_CANCEL_URL);
			$padata .=	'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");
			
			foreach($products as $p => $item){
				
				$padata .=	'&L_PAYMENTREQUEST_0_NAME'.$p.'='.urlencode($item['ItemName']);
				$padata .=	'&L_PAYMENTREQUEST_0_NUMBER'.$p.'='.urlencode($item['ItemNumber']);
				$padata .=	'&L_PAYMENTREQUEST_0_DESC'.$p.'='.urlencode($item['ItemDesc']);
				$padata .=	'&L_PAYMENTREQUEST_0_AMT'.$p.'='.urlencode($item['ItemPrice']);
				$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
			}		

			/* 
			
			//Override the buyer's shipping address stored on PayPal, The buyer cannot edit the overridden address.
			
			$padata .=	'&ADDROVERRIDE=1';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTONAME=J Smith';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOSTREET=1 Main St';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOCITY=San Jose';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOSTATE=CA';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=US';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOZIP=95131';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOPHONENUM=408-967-4444';
			
			*/
						
			$padata .=	'&NOSHIPPING='.$noshipping; //set 1 to hide buyer's shipping address, in-case products that does not require shipping
						
			$padata .=	'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($this -> GetProductsTotalAmount($products));
			
			$padata .=	'&PAYMENTREQUEST_0_TAXAMT='.urlencode($charges['TotalTaxAmount']);
			$padata .=	'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($charges['ShippinCost']);
			$padata .=	'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($charges['HandalingCost']);
			$padata .=	'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($charges['ShippinDiscount']);
			$padata .=	'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($charges['InsuranceCost']);
			$padata .=	'&PAYMENTREQUEST_0_AMT='.urlencode($this->GetGrandTotal($products, $charges));
			$padata .=	'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode(PPL_CURRENCY_CODE);
			
			//paypal custom template
			
			$padata .=	'&LOCALECODE='.PPL_LANG; //PayPal pages to match the language on your website;
			$padata .=	'&LOGOIMG='.PPL_LOGO_IMG; //site logo
			$padata .=	'&CARTBORDERCOLOR=FFFFFF'; //border color of cart
			$padata .=	'&ALLOWNOTE=1';
			$padata .=	'&SOLUTIONTYPE=Sole';//accepter carte bancaire
			//$padata .=	'&LANDINGPAGE=Billing';//accepter carte bancaire
						
			############# set session variable we need later for "DoExpressCheckoutPayment" #######
			
			$_SESSION['ppl_products'] =  $products;
			$_SESSION['ppl_charges'] 	=  $charges;
			
			$httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $padata);

			echo "INSERT INTO logs_paypal (etat, id_trans, montant, MembreId, date_trans, action) VALUES ('".strtoupper($httpParsedResponseAr["ACK"])."', '".urldecode($httpParsedResponseAr["TOKEN"])."', '".$products[0]['ItemPrice']."', '".$products[0]['MembreId']."', NOW(), 'SetExpressCheckout')";

			//".$sql->quote(strtoupper($httpParsedResponseAr["ACK"])).", ".$sql->quote($httpParsedResponseAr["TOKEN"]).", ".$sql->quote($products['ItemPrice']).", ".$sql->quote($products[0]['MembreId']).", NOW(), 'SetExpressCheckout OK'
			
			//Respond according to message we receive from Paypal
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

				$paypalmode = (PPL_MODE=='sandbox') ? '.sandbox' : '';
			
				//Redirect user to PayPal store with Token received.
				
				$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
				
				//header('Location: '.$paypalurl);
			}
			else{
				
				//Show error message
				
				echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
				
				echo '<pre>';
					
					print_r($httpParsedResponseAr);
				
				echo '</pre>';
			}	
		}		
		
			
		function DoExpressCheckoutPayment(){
			
			if(!empty(_SESSION('ppl_products'))&&!empty(_SESSION('ppl_charges'))){
				
				$products=_SESSION('ppl_products');
				
				$charges=_SESSION('ppl_charges');
				
				$padata  = 	'&TOKEN='.urlencode(_GET('token'));
				$padata .= 	'&PAYERID='.urlencode(_GET('PayerID'));
				$padata .= 	'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");
				
				//set item info here, otherwise we won't see product details later	
				
				foreach($products as $p => $item){
					
					$padata .=	'&L_PAYMENTREQUEST_0_NAME'.$p.'='.urlencode($item['ItemName']);
					$padata .=	'&L_PAYMENTREQUEST_0_NUMBER'.$p.'='.urlencode($item['ItemNumber']);
					$padata .=	'&L_PAYMENTREQUEST_0_DESC'.$p.'='.urlencode($item['ItemDesc']);
					$padata .=	'&L_PAYMENTREQUEST_0_AMT'.$p.'='.urlencode($item['ItemPrice']);
					$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
					$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
				}
				
				$padata .= 	'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($this -> GetProductsTotalAmount($products));
				$padata .= 	'&PAYMENTREQUEST_0_TAXAMT='.urlencode($charges['TotalTaxAmount']);
				$padata .= 	'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($charges['ShippinCost']);
				$padata .= 	'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($charges['HandalingCost']);
				$padata .= 	'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($charges['ShippinDiscount']);
				$padata .= 	'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($charges['InsuranceCost']);
				$padata .= 	'&PAYMENTREQUEST_0_AMT='.urlencode($this->GetGrandTotal($products, $charges));
				$padata .= 	'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode(PPL_CURRENCY_CODE);
				$padata .= 	'&PAYMENTREQUEST_0_CUSTOM='.urlencode($products[0]["MembreId"]);
				
				//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
				
				$httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $padata);
					
				//vdump($httpParsedResponseAr);

				//Check if everything went ok..
				if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

					echo '<h2>Success</h2>';
					echo 'Your Transaction ID : '.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
				
					/*
					//Sometimes Payment are kept pending even when transaction is complete. 
					//hence we need to notify user about it and ask him manually approve the transiction
					*/
					
					if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"]){
						
						echo '<div style="color:green">Payment Received! Your product will be sent to you very soon!</div>';
					}
					elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"]){
						
						echo '<div style="color:red">Transaction Complete, but payment may still be pending! '.
						'If that\'s the case, You can manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
					}
					
					$this->GetTransactionDetails();
				}
				else{
						
					echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
					
					echo '<pre>';
					
						print_r($httpParsedResponseAr);
						
					echo '</pre>';
				}
			}
			else{
				
				// Request Transaction Details
				
				$this->GetTransactionDetails();
			}
		}
				
		function GetTransactionDetails(){
		
			// we can retrive transection details using either GetTransactionDetails or GetExpressCheckoutDetails
			// GetTransactionDetails requires a Transaction ID, and GetExpressCheckoutDetails requires Token returned by SetExpressCheckOut
			
			$padata = 	'&TOKEN='.urlencode(_GET('token'));
			
			$httpParsedResponseAr = $this->PPHttpPost('GetExpressCheckoutDetails', $padata, PPL_API_USER, PPL_API_PASSWORD, PPL_API_SIGNATURE, PPL_MODE);

			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

				echo "INSERT INTO logs_paypal (etat, id_trans, montant, MembreId, date_trans, action, payement_statut) VALUES ('".strtoupper($httpParsedResponseAr["ACK"])."', '".urldecode($httpParsedResponseAr["TOKEN"])."', '".urldecode($httpParsedResponseAr["AMT"])."', '".$httpParsedResponseAr["CUSTOM"]."', NOW(), 'GetTransactionDetails', '".$httpParsedResponseAr["PAYMENTREQUESTINFO_0_TRANSACTIONID"]."')";
				
				echo '<br /><b>Stuff to store in database :</b><br /><pre>';
				
				#### SAVE BUYER INFORMATION IN DATABASE ###

				$get = print_r($_GET,true);
				$post = print_r($_POST,true);
				$texte = "Variables en GET : <br/>".$get."<br/><br/>"."Variables en POST : <br/>".$post;
				//$to      = "exceptions@bluepaid.com";	//,jeremy.attia@gmail.com
				$to      = "myriam@mgmobile.fr";	//,jeremy.attia@gmail.com
				$subject = 'Erreur PAYPAL Mobile';

				// Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: Script <contact@deuscom.fr>' . "\r\n";

				//	 $statut =  mail($to, $subject, $texte, $headers);

				$amount=urldecode($httpParsedResponseAr["AMT"]);
				$MembreId=urldecode($httpParsedResponseAr["CUSTOM"]);
				$statut=urldecode(strtoupper($httpParsedResponseAr["ACK"]));
				$Jeton=urldecode(_GET('token'));

				echo $amount." / ".$MembreId." / ".$statut." / ".$Jeton;

				$continu = true;
				$NbJ_GB = 0;
				$NbJ_GS = 0;
				$NbJ_SL = 0;
				switch($amount){
					case "5.00":
					case "1.00":
						$NbJ = 31;
						$NbJ_L = 7;
						$NbJ_vieux = 0;
						$NbCr = 0;
						$Prix = "214";
						$PrixW = "95";
						break;
					case "12.00":
						$NbJ = 93;
						$NbJ_L = 31;
						$NbJ_vieux = 0;
						$NbCr = 0;
						$Prix = "216";
						$PrixW = "104";
						break;
					case "18.00":
						$NbJ = 186;
						$NbJ_L = 186;
						$NbJ_vieux = 93;
						$NbJ_GB = 93;		
						$NbCr = 0;
						$Prix = "217";
						$PrixW = "105";
						break;
					case "24.00":
						$NbJ = 366;
						$NbJ_L = 186;
						$NbJ_vieux = 0;
						$NbCr = 0;
						$Prix = "219";
						$PrixW = "106";
						break;
					case "36.00":
						$NbJ = 732;
						$NbJ_L = 366;
						$NbJ_vieux = 0;
						$NbCr = 0;
						$Prix = "223";
						$PrixW = "107";
						break;
					
					case "10.00":
					case "9.00":
						$NbJ = 93;
						$NbJ_L = 31;
						$NbJ_vieux = 31;
						$NbJ_GB = 31;
						$NbJ_GS = 31;
						$NbCr = 0;
						$Prix = "215";
						$PrixW = "96";
						break;
					case "30.00":
						$NbJ = 366;
						$NbJ_L = 186;
						$NbJ_vieux = 186;
						$NbJ_GB = 186;		
						$NbJ_GS = 366;		
						$NbCr = 0;
						$Prix = "222";
						$PrixW = "108";
						break;
					case "29.00":
						$NbJ = 366;
						$NbJ_L = 186;
						$NbJ_vieux = 186;
						$NbJ_GB = 186;		
						$NbJ_GS = 186;	
						$NbJ_SL = 31;	
						$NbCr = 0;
						$Prix = "221";
						$PrixW = "97";
						break;
					case "15.00":
						$NbJ = 186;
						$NbJ_L = 186;
						$NbJ_vieux = 0;
						$NbCr = 0;
						$Prix = "217";
						$PrixW = "98";
						break;
					case "19.00":
						$NbJ = 93;
						$NbJ_L = 93;
						$NbJ_vieux = 0;
						$NbJ_GS = 93;		
						$NbCr = 0;
						$Prix = "218";
						$PrixW = "99";
						break;
					case "27.00":
						$NbJ = 93;
						$NbJ_L = 93;
						$NbJ_vieux = 0;
						$NbCr = 0;
						$Prix = "220";
						$PrixW = "117";
						break;
					case "39.00":
						$NbJ = 0;
						$NbJ_L = 366;
						$NbJ_vieux = 0;
						$NbJ_GS = 366;		
						$NbCr = 0;
						$Prix = "227";
						$PrixW = "134";
						break;
					case "49.00":
						$NbJ = 0;
						$NbJ_L = 732;
						$NbJ_GS = 732;		
						$NbJ_vieux = 0;
						$NbCr = 0;
						$Prix = "228";
						$PrixW = "135";
						break;


					case "48.00":
						$NbJ = 0;
						$NbJ_L = 186;
						$NbJ_vieux = 366;
						$NbJ_GB = 366;		
						$NbCr = 0;
						$Prix = "224";
						$PrixW = "109";
						break;
					case "72.00":
						$NbJ = 0;
						$NbJ_L = 366;
						$NbJ_vieux = 732;
						$NbJ_GB = 732;		
						$NbCr = 0;
						$Prix = "225";
						$PrixW = "110";
						break;
					case "96.00":
						$NbJ = 0;
						$NbJ_L = 732;
						$NbJ_vieux = 732;
						$NbCr = 0;
						$Prix = "226";
						$PrixW = "118";
						break;
					case "99.00":
						$NbJ = 0;
						$NbJ_L = 0;
						$NbJ_vieux = 0;
						$NbJ_SL = 366;
						$NbCr = 0;
						$Prix = "233";
						$PrixW = "98";
						break;		
					
					default:
						echo "KO1";
						$continu = false;
				}

				if ($continu){
					if ($statut!="SUCCESSWITHWARNING" && $statut!="SUCCESS") {
						echo "KO2";
						$continu = false;
					}
				}

				if($continu){
					echo "<br/>".$NbJ." / ".$Prix." / ".$PrixW;
					
					/*$result2 = $sql->query("SELECT * FROM GC_Membres  WHERE MembreId = '".$MembreId."'");
					$ligne2 = $result2->fetch();
					if($ligne2){
						$Origine = $ligne2["Origine"];
						$Pseudo = $ligne2["Pseudo"];
						$Crediz = $ligne2["Crediz"];
						$Age = $ligne2["Age"];
						$coefBlackliste = $ligne2["coefBlackliste"];
						$commentaire = strtolower($ligne2["Commentaire"]);
						$TypeConnexion = $ligne2["TypeConnexion"];

						$daterenouv = $ligne2["DateRenouv"];
						$now = date("Y-m-d H:i:s");
						//$diff = datediff($now,$daterenouv);
						$diff = datediff_new("s",$now,$daterenouv);

						if($diff<0)
						{
							$daterenouv = $now;
						}
						$NewDateRenouv = $daterenouv;		
						
						if($TypeConnexion=="1" || $TypeConnexion=="4"){
							$Prix = $PrixW;
						}

					}else{
						$now = date("Y-m-d H:i:s");
						$daterenouv = date("Y-m-d H:i:s");
						$NewDateRenouv = date("Y-m-d H:i:s");
					}

					if(strtolower($Origine)=="l" || strtolower($Origine)=="bs" || strtolower($Origine)=="tc" || strtolower($Origine)=="pt" || strtolower($Origine)=="yb" || strtolower($Origine)=="r" || strtolower($Origine)=="jj" || strtolower($Origine)=="2bg" || strtolower($Origine)=="etn") {
						$NbJ = $NbJ_L;
					}

					if($Origine=="gs"){
						$NbJ = $NbJ_GS;
					}
					if($Origine=="gb"){
						$NbJ = $NbJ_GB;
					}
					if($Origine=="yb"){
						$NbJ = $NbJ_L;
					}
					if($Origine=="sl"){
						$NbJ = $NbJ_SL;
					}
					if(strtolower($Origine)=="g"){
						if($Age>=35){		
							$NbJ = $NbJ_vieux;
						}

						if($coefBlackliste>=2.5){
							$NbJ = $NbJ_vieux;
						}

						$pos = strpos(strtolower($commentaire),"averto");
						if ($pos !== false) {
							$NbJ = $NbJ_vieux;
						}		

						$pos = strpos(strtolower($commentaire),"pro");
						if ($pos !== false) {
							$NbJ = $NbJ_vieux;
						}		

						if($coefBlackliste>=3){
							$NbJ = $NbJ_L;
						}
						if($Age>=49){		
							$NbJ = $NbJ_L;
						}
						$NbJ = $NbJ_L;

					}

					if(strtolower($Origine)=="eg"){
						$NbJ = $NbJ_L;		
					}

					if(strtolower($Origine)=="cd"){
						$NbJ = $NbJ_L;		
					}

					echo "daterenouv : $daterenouv <br/>";
					echo "NbJ : $NbJ <br/>";
					
					$NewDateRenouv = date("Y-m-d H:i:s",dateadd("d",$NbJ,strtotime($daterenouv)));
					echo "NewDateRenouv : $NewDateRenouv <br/>";
					
					$req = $sql->exec("UPDATE GC_Membres SET DateRenouv='".$NewDateRenouv."',DateAccesMois='".$now."' WHERE MembreId = '".$MembreId."'");
					echo "UPDATE GC_Membres SET DateRenouv='".$NewDateRenouv."',DateAccesMois='".$now."' WHERE MembreId = '".$MembreId."'";
					echo "<br/>";

					$DateLongue = date_en_francais(date("j F",strtotime($NewDateRenouv)))." ".date("Y",strtotime($NewDateRenouv));
					$DateLongue = str_replace("Decembre","D&eacute;cembre",$DateLongue);
					$HeureLongue = date("H:i",strtotime($NewDateRenouv));

					if(strtolower($Origine)=="eg" || strtolower($Origine)=="cd" || strtolower($Origine)=="sl"){
						$message = "Votre paiement a bien été re&ccedil;u. Votre acc&egrave;s premium est valable jusqu'au ".strtolower($DateLongue)." à ". $HeureLongue;
					}else{
						$message = "Ton paiement a bien été re&ccedil;u. Ton acc&egrave;s premium est valable jusqu'au ".strtolower($DateLongue)." à ". $HeureLongue;
					}
					echo "Message: ".$message."<br/>";

					$result2 = $sql->query("SELECT IdVente FROM Logs_Gaymec ORDER BY IdVente DESC LIMIT 1");
					$ligne2 = $result2->fetch();
					if($ligne2){
						$id = $ligne2["IdVente"]+1;
					}else{
						$id = 1;
					}
					$req = $sql->exec("INSERT INTO Logs_Gaymec (IdVente, Alias, Jeton, Prix, Operateur, Jour, Mois, An, Heure, Mn, Statut, UrlRet) VALUES (" . $id . ", '" . $MembreId . "', '".$Jeton."', '".$Prix."', 'I', " . date("j") . ", " . date("n") . ", " . date("Y") . ", " . date("H") . ", " . date("i") . ", 'OUI', '')");
					$req = $sql->exec("UPDATE GC_Membres SET Confiance = '0' WHERE MembreId = '".(int)$MembreId."' AND Confiance = '1'");
					
					$emmeteurid = "3";
					$emmeteurpseudo = "Infos";
					$destinataireid = $MembreId;
					$destinatairepseudo = $Pseudo;
					$ar = 0;
					$message = utf8_decode($message);
					SendMsg($sql,$emmeteurid,$emmeteurpseudo,$destinataireid,$destinatairepseudo,$message,$ar);
					*/
					
				}
				else {
					$get = print_r($_GET,true);
					$post = print_r($_POST,true);
					$texte = "Variables en GET : <br/>".$get."<br/><br/>"."Variables en POST : <br/>".$post;
					$to    = "mickael.haddad@gmail.com";	//,jeremy.attia@gmail.com
					$subject = 'Erreur PAYPAL Mobile';
					
					// Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: Script <contact@deuscom.fr>' . "\r\n";
					
					//$statut =  mail($to, $subject, $texte, $headers);
				}

				### END SAVE BUYER INFORMATION IN DATABASE ###
				
				echo '<pre>';
				
					print_r($httpParsedResponseAr);
					
				echo '</pre>';
			} 
			else  {
				
				echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
				
				echo '<pre>';
				
					print_r($httpParsedResponseAr);
					
				echo '</pre>';

			}
		}
		
		function PPHttpPost($methodName_, $nvpStr_) {
				
				// Set up your API credentials, PayPal end point, and API version.
				$API_UserName = urlencode(PPL_API_USER);
				$API_Password = urlencode(PPL_API_PASSWORD);
				$API_Signature = urlencode(PPL_API_SIGNATURE);
				
				$paypalmode = (PPL_MODE=='sandbox') ? '.sandbox' : '';
		
				$API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
				$version = urlencode('109.0');
			
				// Set the curl parameters.
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
				//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
				
				// Turn off the server and peer verification (TrustManager Concept).
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
				curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2 );
			
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
			
				// Set the API operation, version, and API signature in the request.
				$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
			
				// Set the request as a POST FIELD for curl.
				curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
			
				// Get response from the server.
				$httpResponse = curl_exec($ch);
			
				if(!$httpResponse) {
					exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
				}
			
				// Extract the response details.
				$httpResponseAr = explode("&", $httpResponse);
			
				$httpParsedResponseAr = array();
				foreach ($httpResponseAr as $i => $value) {
					
					$tmpAr = explode("=", $value);
					
					if(sizeof($tmpAr) > 1) {
						
						$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
					}
				}
			
				if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
					
					exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
				}
			
			return $httpParsedResponseAr;
		}
	}
