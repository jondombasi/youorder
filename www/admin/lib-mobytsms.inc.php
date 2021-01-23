<?php
/**
 * Logithéque pour léenvoi de SMS é la demande de POST/GET HTTP
 *
 *
 * @version 1.4.11
 * @package Mobyt-ModuleHTTP
 * @auteurs  
 *		Simone Coreggioli 	- 	simone.coreggioli@mobyt.it
 *		Matteo Beccati 		- 	matteo.beccati@mobyt.it
 *
 * @copyright (C) 2003-2010 Mobyt srl
 * @licence https://www.mobyt.it/bsd-license.html BSD License
 *
 */



/**#@+
 * @access	private
 */
/**
 * Version de la classe
 */
define('MOBYT_PHPSMS_VERSION',	'1.4.11');

/**
 * Type déauthentification basée sur hash md5
 */
define('MOBYT_AUTH_MD5',	1);

/**
 * Type déauthentification basée sur IP avec mot de passe lisible
 */
define('MOBYT_AUTH_PLAIN',	2);

/**
 * Qualité messages Direct 
 */
define('MOBYT_QUALITY_DRT',	3);

/**
 * Qualité messages  TOP 
 */
define('MOBYT_QUALITY_TOP',	4);
/**
 * Qualité messages Default
 */
define('MOBYT_QUALITY_DEFAULT',	5);
/**
 * Qualité messages  Low Cost
 */
define('MOBYT_QUALITY_LOWCOST',	6);

/**
 * @global array Array de conversion pour les qualités
 */
$GLOBALS['mobyt_qty'] = array(
		MOBYT_QUALITY_DRT		=> 'l',
		MOBYT_QUALITY_TOP		=> 'n',
		MOBYT_QUALITY_LOWCOST   => 'll',
		MOBYT_QUALITY_DEFAULT		=> 'd'
		
	);
	
/**#@-*/

/**
 * Classe pour léenvoi de SMS é la demande de POST/GET HTTP
*
 * Les paramétres utilisés par défaut sont les suivants :
 * - Expéditeur: <b>"MobytSms"</b>
 * - Authentification: <b>basée sur IP, avec mot de passe lisible</b>
 * - Qualité: <b>qualité defaut du client</b>
 * - Domaine: <b>"http://multilevel.mobyt.fr"</b>
 *
 * @package Mobyt-ModuleHTTP
 * @example EnvoiUniqueSMS.php Envoi déun sms simple
 */

class mobytSms
{
	/**#@+
	 * @access	private
	 * @var		string
	 */
	var $quality = MOBYT_QUALITY_DEFAULT;
	var $from;
	var $domaine = 'http://multilevel.mobyt.fr';
	var $login;
	var $pwd;
	var $udh;
	var $auth = MOBYT_AUTH_PLAIN;
	var $ignoreErr= false;

	/**#@-*/
	
	/**
	 * @param string	Nom de léutilisateur (Identifiant)
	 * @param string	Mot de passe
	 * @param string	en-téte expéditeur
	 *
	 * @see setFrom
	 */
	function mobytSms($login, $pwd, $from = 'MobytSms')
	{
		$this->login = $login;
		$this->pwd = $pwd;
		$this->setFrom($from);
	}
	
	/**
	 * Configurer  en-téte expéditeur
	 *
	 * Léexpéditeur peut contenir max 11 caractéres alphanumériques ou un numéro de téléphone
	 * avec préfixe international. 
	 *
	 * @param string	En-téte expéditeur
	 */
	function setFrom($from)
	{
		$this->from = substr($from, 0, 14);
	}
	
	/**
	 * Configurer  l'adresse URL du domaine de léadministrateur/revendeur au quel les éventuels clients devront accéder
	 * L'URL doit figurer au format 'http://www.mondomaine.fr'
	 *
	 * @param string    URL
	 */
	function setDomaine($domaine)
	{
		$this->domaine = $domaine;
	}
	
	/**
	 * Utiliser l'authentification avec mot de passe
	 */
	function setAuthPlain()
	{
		$this->auth = MOBYT_AUTH_PLAIN;
	}
	
	/**
	 * Utiliser l'authentification basée sur hash md5
	 */
	function setAuthMd5()
	{
		$this->auth = MOBYT_AUTH_MD5;
	}
	
	/**
	 * Configurer la qualité des messages Direct
	 */
	function setQualityDirect()
	{
		$this->quality = MOBYT_QUALITY_DRT;
	}
	
	
	/**
	 * Configurer la qualité des messages TOP
	 */
	function setQualityTop()
	{
		$this->quality = MOBYT_QUALITY_TOP;
	}
	
	/**
	 * Configurer la qualité des messages Low Cost
	 */
	function setQualityLowCost()
	{
		$this->quality = MOBYT_QUALITY_LOWCOST;
	}
	
	
	/**
	 * Configurer la qualité des messages Default
	 */
	function setQualityDefault()
	{
		$this->quality = MOBYT_QUALITY_DEFAULT;
	}


	/**
	 * Ignore error on rcpt send batch
	 */
	function setIgnoreError($ignoreErr)
	{
		$this->ignoreErr = $ignoreErr;
	}
	
		
	
	/**
	 * Contréler le crédit disponible exprimé en Euros
	 *
	 * @returns mixed OK suivie par un entier correspondant au crédit ou KO en cas déerreur
	 *
	 * @example ControleSMS.php Contréle le crédit résiduel et les messages disponibles
	 */
	function getCredit($type='credit')
	{
		
		$fields = array(
				'user'		=> $this->login,
				//'pass'	=> $this->pwd,
				'pass'	=> $this->auth == MOBYT_AUTH_MD5 ? '' : $this->pwd,
				'ticket'	=> $this->auth == MOBYT_AUTH_MD5 ? md5($this->login.$type.md5($this->pwd)) : ''
			);
		
		$fields['type'] = $type ;
		$fields['domaine'] = $this->domaine;
		$fields['path'] = '/sms/credit.php';
		
		return trim($this->httpPost($fields));
	}


	/**
	 * Envoyer un SMS
	 *
	 *
	 * @param string Numéro de  téléphone avec préfixe international (ex. +336101234567)
	 * @param string Texte du message (max 160 caractéres)
	 * @param string Type de SMS (TEXT | WAPPUSH)
	 * @param string L'adresse URL auquel le téléphone mobile qui reéoit un SMS Wap Push ira se connecter 
	 * @param integer Si le paramétre est égal é 1, le message de retour sera léidentificateur déenvoi, é utiliser en cas de requéte 
	 * déétat déanvoi effectué par POST/GET HTTP  (es. HTTP00000000111)
	 *
	 * @returns string Réponse reéue de la passerelle ("OK ..." o "KO ...")
	 *
	 * @example EnvoiUniqueSMS.php Envoi déun sms simple 
	 */


	function sendSms($rcpt, $text, $operation='TEXT', $url='', $return_id='')
	{
		global $mobyt_qty, $mobyt_ops;
		
		
		$fields = array(
				'user'		=> $this->login,
				//'pass'      => $this->pwd,
				'sender'		=> $this->from,
				'rcpt'		=> $rcpt,
				'data'		=> $text,
				'operation' => $operation,
				'url' => $url,
				'return_id' => $return_id
			);
		
		if ($this->auth == MOBYT_AUTH_MD5)
		{
			$fields['pass'] = '';
			$fields['ticket'] = md5($this->login.$rcpt.$this->from.$text.$mobyt_qty[$this->quality].md5($this->pwd));
		}
		else
		{
			$fields['pass'] = $this->pwd;
			$fields['ticket'] = '';
		}
		
		if (isset($mobyt_qty[$this->quality]))
			$fields['qty'] = $mobyt_qty[$this->quality];
		
		
		$fields['domaine'] = $this->domaine;
		
		$fields['path'] = '/sms/send.php';
		
		return trim($this->httpPost($fields));
	}
	
	/** 
	 * Envoyer un SMS é plusieurs destinataires
	 *
	 *
	 *
	 * @param array Array de numéros de téléphone avec préfixe international (ex. +336101234567)
	 * @param string Texte du message (max 160 caractéres)
	 * @param string Type de SMS (TEXT | WAPPUSH)
	 * @param string L'adresse URL auquel le téléphone mobile qui reéoit un SMS Wap Push ira se connecter 
	 * @param integer Si le paramétre est égal é 1, le message de retour sera léidentificateur déenvoi, é utiliser en cas de requéte 
	 * déétat déanvoi effectué par POST/GET HTTP  (es. HTTP00000000111)
	 *
	 * @returns string Réponse reéue de la passerelle ("OK ..." o "KO ...")
	 *
	 * @example EnvoiMultipleSMS.php Envoi déun sms vers plusieurs numéros avec authentification é travers mot de passe lisible
	 */


	function sendMultiSms($rcpts, $data, $operation='TEXT', $url='',$return_id='' )
	{
        global $mobyt_qty, $mobyt_ops;
		
		if (!is_array($rcpts))
			return $this->sendSms($rcpts, $data);
		

		$fields = array(
				'user'		=> $this->login,
				//'pass'		=> $this->pwd,
				'sender'	=> $this->from,
				'data'		=> $data,
				'rcpt'   	=> join(',',$rcpts),
				'operation' => $operation,
				'url'       => $url,
				'return_id' => $return_id,
				'ignoreErr' => $this->ignoreErr
			);
		
		
		if ($this->auth == MOBYT_AUTH_MD5)
		{
			$fields['pass'] = '';
			$fields['ticket'] = md5($this->login.join(',',$rcpts).$this->from.$data.$mobyt_qty[$this->quality].md5($this->pwd));
		}
		else
		{
			$fields['pass'] = $this->pwd;
			$fields['ticket'] = '';
		}
		
		if (isset($mobyt_qty[$this->quality]))
			$fields['qty'] = $mobyt_qty[$this->quality];
		
		$fields['domaine'] = $this->domaine;
		$fields['path']='/sms/batch.php';
		
		return trim($this->httpPost($fields));
		
	}
	
	/**
	 * Envoyer un request MNC
	 *
	 * @param array Array de numéros de téléphone avec préfixe international (ex. +336101234567)
	 * @param integer Si le paramétre est égal é 1, le message de retour sera léidentificateur déenvoi, é utiliser en cas de requéte 
	 * déétat déanvoi effectué par POST/GET HTTP  (es. HTTP00000000111)
	 *
	 * @returns string Réponse reéue de la passerelle ("OK ..." o "KO ...")
	 *
	 * @example EnvoiMNC.php Envoi d'un request MNC 
	 */
	function sendMNC($numbers,$return_id='')
	{
		global $mobyt_qty, $mobyt_ops;
		
		$fields = array(
				'user'		=> $this->login,
				//'pass'		=> $this->pwd,
				'pass'	=> $this->auth == MOBYT_AUTH_MD5 ? '' : $this->pwd,
				'ticket'	=> $this->auth == MOBYT_AUTH_MD5 ? md5($this->login.$numbers.md5($this->pwd)) : '',
				'numbers'   => $numbers,
				'return_id' => $return_id,
				'ignoreErr' => $this->ignoreErr
			);
		
		$fields['domaine'] = $this->domaine;
		
		$fields['path'] = '/sms/mnc.php';
		
		return trim($this->httpPost($fields));
	}
	
	/**
	 * Report on demand des envois
	 *
	 * @param string Léidentificateur de l'envoi
	 * @param string Le type de report souhaité (queue, notify, mnc)
	 * @param string Le schéma du report (Le paramétre doit actuellement avoir la valeur é1é (un))
	 *
	 * @returns string En cas d'erreur, le script renverra une seule ligne contenant KO ainsi qu'un message d'erreur;
	 * dans le cas contraire il renverra les données du report demandé au format CSV avec les champs séparés par une virgule 
	 * et oé la premiére ligne contiendra le nom des colonnes. 
	 *
	 * @example ReportOnDemande.php Report On Demand FTP/HTTP 
	 */
	function sendStatus($id, $type, $schema='1')
	{
		global $mobyt_qty, $mobyt_ops;
		
		$fields = array(
				'user'		=> $this->login,
				//'pass'	    => $this->pwd,
				'pass'	=> $this->auth == MOBYT_AUTH_MD5 ? '' : $this->pwd,
				'ticket'	=> $this->auth == MOBYT_AUTH_MD5 ? md5($this->login.$id.$type.$schema.md5($this->pwd)) : '',
				'id'        => $id,
				'type'      => $type,
				'schema'    => $schema
			);
	
		
		$fields['domaine'] = $this->domaine;
		
		$fields['path'] = '/sms/batch-status.php';
		
		return trim($this->httpPost($fields));
	}

	/**
	 * Send an HTTP POST request, choosing either cURL or fsockopen
	 *
	 * @access private
	 */
	
	function httpPost($fields)
	{
		$qs = array();
		foreach ($fields as $k => $v)
			$qs[] = $k.'='.urlencode($v);
		$qs = join('&', $qs);
		
		
		if (function_exists('curl_init'))
			return mobytSms::httpPostCurl($qs, $fields['domaine'].$fields['path']);
	
		
		$errno = $errstr = '';
		if ($fp = @fsockopen(substr($fields['domaine'],7), 80, $errno, $errstr, 30)) 
		{   
			fputs($fp, "POST ".$fields['path']." HTTP/1.0\r\n");
			fputs($fp, "Host: ".substr($fields['domaine'],7)."\r\n");
			fputs($fp, "User-Agent: phpMobytSms/".MOBYT_PHPSMS_VERSION."\r\n");
			fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-Length: ".strlen($qs)."\r\n");
			fputs($fp, "Connection: close\r\n");
			fputs($fp, "\r\n".$qs);
			
			$content = '';
			while (!feof($fp))
				$content .= fgets($fp, 1024);
			
			fclose($fp);
			
			return preg_replace("/^.*?\r\n\r\n/s", '', $content);
		}
		
		return false;
	}

	/**
	 * Send an HTTP POST request, through cURL
	 *
	 * @access private
	 */
	function httpPostCurl($qs, $domaine)
	{   
		if ($ch = @curl_init($domaine))
		{   
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'phpMobytSms/'.MOBYT_PHPSMS_VERSION.' (curl)');
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $qs);
		
			return curl_exec($ch);
		}
		
		return false;
	}
	
	
}


/**
 * Classe pour les messages reéus é travers Web Service SOAP
 *
 * Le contréle des messages reéus nécessite l'utilisation de la classe NuSOAP, distribuée sous licence GNU Lesser
 * Public License (LGPL). Le fichier lib-nusoap.inc.php doit étre copié dans la méme 
 * directory de lib-mobytsms.inc.php pour garantir le bon fonctionnement du service.
 *
 * Les paramétres utilisés par défaut sont les suivants:
 * - Authentification: <b>Basée sur adresse IP et mot de passe lisible</b>
 *
 * @package Mobyt-ModuleHTTP
 * @example ControleSMS-SOAP.php Contréle des messages reéus
 * 
 */
class mobytSOAP
{
	/**
	 * @param string	Eventuel message d'erreur
	 */
	var $errorMessage = '';

	/**#@+
	 * @access	private
	 * @var		string
	 */
	var $login;
	var $pwd;
	var	$auth = MOBYT_AUTH_PLAIN;
	var $domaine = 'http://multilevel.mobyt.fr';
	var $dotnet = 0;
	/**#@-*/


	/**
	 * @param string	Nom de léutilisateur (Identifiant)
	 * @param string	Mot de passe
	 */
	function mobytSOAP($login, $pwd)
	{
		$this->login = $login;
		$this->pwd = $pwd;
	}	
	
	
	/**
	 * Configurer  l'adresse URL du domaine de léadministrateur/revendeur au quel les éventuels clients devront accéder
	 * L'URL doit figurer au format 'http://www.mondomaine.fr'
	 *
	 * @param string    URL
	 */
	function setDomaine($domaine)
	{
		$this->domaine = $domaine;
	}
	
	/**
	 * Utiliser l'authentification avec mot de passe lisible
	 */
	function setAuthPlain()
	{
		$this->auth = MOBYT_AUTH_PLAIN;
	}
	
	/**
	 * Utiliser l'authentification basée sur hash md5
	 */
	function setAuthMd5()
	{
		$this->auth = MOBYT_AUTH_MD5;
	}
	
	/**
	 * Convertir la date au format .Net compatible
	 */
	function setDate()
	{
		$this->dotnet = 1;
	}
	

	/**
	 * 
	 *
	 * @param string    Numéro di réception
     * @param string    Code de partage
     * @param string    Nombre de messages é afficher
     *
     *
     * @returns mixed array de structure recvSms (int Id message, string Numéro de l'expéditeur, string Texte du message,
     * dateTime Date et heure de réception)
     *
     * @example ControleSMS-SOAP.php Contréle des messages reéus
	 */
	function receiveSms($rcpt, $sharecode, $messages)
	{
		require_once('lib-nusoap.inc.php');
		
		if (is_array($rcpt))
				$rcpt = join(',', $rcpt);

		$params = array(
				'user'		=> $this->login,
				//'pass'	    => $this->pwd,
				'rcpt'		=> $rcpt,
				'sharecode'	=> $sharecode,
				'pass'	=> $this->auth == MOBYT_AUTH_MD5 ? '' : $this->pwd,
				'ticket'	=> $this->auth == MOBYT_AUTH_MD5 ? md5($this->login.$rcpt.$sharecode.md5($this->pwd)) : '',
				'messages'	=> $messages
			);


		$client = new soapclient($this->domaine.'/wsdl/?wsdl', true);

		if ($err = $client->getError())
			trigger_error('Erreur dans la création du client SOAP: '.$err, E_USER_ERROR);
		
		$res = $client->call('receiveSms', array_values($params));

		if ($client->fault)
			return join(' ', $res);
		
		return $res;
	}
}

/**
 * Classe pour la gestion des activités de BackOffice
 *
 * Les paramétres utilisés par défaut sont les suivants :
 * -	Authentification : <b> Adresse IP + mot de passe en clair</b>
 *
 * @package Mobyt-ModuleHTTP
 * @example ContréleOpérations.php
 *
 *
 **/
class BackOffice
{
	/**#@+
	 * @access	private
	 * @var		string
	 */
	var $auth = MOBYT_AUTH_PLAIN;
	var $smsusername;
	var $smspassword;
	var $domaine = 'http://multilevel.mobyt.fr';
	/**#@-*/
	
	/**
	 * @param string	Username di accesso (Login)
	 * @param string	Password di accesso
	 *
	 */
	function BackOffice($login, $pwd)
	{
		$this->smsusername = $login;
		$this->smspassword = $pwd;
	}
	
	/**
	 * Configurer  l'adresse URL du domaine de léadministrateur/revendeur au quel les éventuels clients devront accéder
	 * L'URL doit figurer au format 'http://www.mondomaine.fr'
	 *
	 * @param string    URL
	 */
	function setDomaine($domaine)
	{
		$this->domaine = $domaine;
	}
	
	/**
	 * Utiliser l'authentification avec mot de passe
	 */
	function setAuthPlain()
	{
		$this->auth = MOBYT_AUTH_PLAIN;
	}
	
	/**
	 * Utiliser l'authentification basée sur hash md5
	 */
	function setAuthMd5()
	{
		$this->auth = MOBYT_AUTH_MD5;
	}
	
	
	/**Création déun nouveau client
	 * @param string Nom du client é créer
	 * @param string Nom déutilisateur que le client utilisera pour séauthentifier
	 * @param string Mot de passe que le client utilisera pour séauthentifier
	 * @param array Autres informations optionnelles (email, domaine, identifiant tarifé)
	 *
	 * @returns string En cas de succés éOK <id du nouveau client créé>. En cas dééchec 
	 * éKO <message déerreur>
	 *
	 * @example CreerClient.php
	 *
	 */
	function clientAdd($name, $username, $password, $options)
	{
		
		$fields = array(
		        'smsusername' => $this->smsusername,
				'smspassword' => $this->smspassword,
				'name'		=> $name,
				'username'		=> $username,
				'password'		=> $password
			);
			
		$fields['email'] = isset($options['email']) ? $options['email'] : '';
		$fields['tpl_id'] = isset($options['tpl_id']) ? $options['tpl_id'] : '';
		$fields['contact'] = isset($options['contact']) ? $options['contact'] : '';
		$fields['ref_id'] = isset($options['ref_id']) ? $options['ref_id'] : '';
		$fields['reseller'] = isset($options['reseller']) ? $options['reseller'] : '';
		$fields['vhost'] = isset($options['vhost']) ? $options['vhost'] : '';
		
		$fields['domaine'] = $this->domaine;
		$fields['path'] = '/backoffice/client-add.php';
		
		return trim($this->httpPost($fields));
	}
	
	/** Attribution crédits au client spécifié
	 *  @param string Identifiant du client auquel le crédit doit étre attribué
	 * 	@param string Identifiant univoque du tarif
	 *  @param array Autres informations optionnelles (crédit)
	 *
	 *  @returns string En cas de succés éOK<id du nouveau crédit>é . <br>
	 *  En cas dééchec éKO <message déerreur>
	 *
	 * @example AttribuerCredits.php
	 *
	 */
	function creditAdd($u_id,$bill_id,$options){
		
		$fields = array(
		        'smsusername' => $this->smsusername,
				'smspassword' => $this->smspassword,
				'u_id'        => $u_id,
				'bill_id'     => $bill_id
				);
		
		$fields['credit'] = isset($options['credit']) ? $options['credit'] : '';
		
		$fields['domaine'] = $this->domaine;
		$fields['path'] = '/backoffice/credit-add.php';
		
		return trim($this->httpPost($fields));
	}
	
	/** Contréle du crédit résiduel déun client
	  * @param string Identifiant univoque du client dont le crédit résiduel doit étre 
	  * contrélé
	  *
	  * @returns string En cas de succés éOK <crédit en euro>é. En cas dééchec éKO 
	  * <message déerreur>
	  *
	  * @example ControleCredits.php
      *
	  */
	function creditCheck($u_id){
		
			$fields = array(
		        'smsusername' => $this->smsusername,
				'smspassword' => $this->smspassword,
				'u_id'        => $u_id
				);
				
			$fields['domaine'] = $this->domaine;
			$fields['path'] = '/backoffice/credit-get.php';
			
			return trim($this->httpPost($fields));
	}
	
	/** Contréle des opérations de BackOffice effectuées
	  * @param string Typologie déopération effectuée (écreditsé est la seule supportée 
	  * dans léétat actuel des choses)
	  * @param array Autres informations optionnelles (identifiant client, date début 
	  * report, date fin report)
	  *
	  * @returns array En cas de succés il y aura en retour un report dont la premiére 
	  * ligne contient léen-téte des champs et les suivantes contiennent les données. Les 	  * champs sont séparés par des tabulations et les lignes terminent par les 
	  * caractéres é<CR><LF>é . En cas dééchec éKO <message déerreur>
	  *
	  * @example ControleOperations.php
	  *
	  */
	function operationCheck($type, $options){
	
			$fields = array(
		        'smsusername' => $this->smsusername,
				'smspassword' => $this->smspassword,
				'type'        => $type
				);
				
			$fields['u_id'] = isset($options['u_id']) ? $options['u_id'] : '';
			$fields['from'] = isset($options['from']) ? $options['from'] : '';
			$fields['to'] = isset($options['to']) ? $options['to'] : '';
			
			$fields['domaine'] = $this->domaine;
			$fields['path'] = '/backoffice/userlog-get.php';
			
			return trim($this->httpPost($fields));
	}
	
	/** Création/Attribution du service de réception
	  * @param string Identifiant du client auquel le service doit étre attribué
	  * @param string Numéro de téléphone réception
	  * @param string Numéro de codes é créer
	  *
	  * @returns string En cas de succés éOK <codes créés>é. <br>  En cas dééchec éKO 
	  * <message déerreur>
      *
	  * @example Reception.php
      *
	  */
	function Reception($u_id,$dest,$options){
			
			$fields = array(
		        'smsusername' => $this->smsusername,
				'smspassword' => $this->smspassword,
				'u_id'        => $u_id,
				'dest'        => $dest
				);
		
			$fields['num'] = isset($options['num']) ? $options['num'] : '';
			
			$fields['domaine'] = $this->domaine;
			$fields['path'] = '/backoffice/recv-add.php';
			
			return trim($this->httpPost($fields));
	}
	
	
	/** Suspension du service de réception
	  *	@param string Identifiant du client dont le service doit étre suspendu
	  *	@param string Numéro de téléphone réception
	  *	@param string Code de partage é suspendre
      *
	  *	@returns string En cas de succés éOKé . <br>  En cas dééchec éKO <message 
	  * déerreur>
      *
	  *	@example ReceptionDel.php
	  *
	  */
	  
	function receptionDelete($u_id,$dest,$sharecode){
			
			$fields = array(
		        'smsusername' => $this->smsusername,
				'smspassword' => $this->smspassword,
				'u_id'        => $u_id,
				'dest'        => $dest,
				'sharecode'   => $sharecode
				);
		
			
			
			$fields['domaine'] = $this->domaine;
			$fields['path'] = '/backoffice/recv-del.php';
			
			return trim($this->httpPost($fields));
	}
	
	/** Activation/Désactivation déun client
	  *	@param string Identifiant du client é activer/désactiver
	  *	@param integer 0 = désactiver / 1 = activer
      *
	  *	@returns string En cas de succés ééOK <statut client>é . <br> En cas dééchec éKO 	
	  *	<message déerreur>
	  *
	  *	@example ClientManagement.php
	  *
	  */
	function clientManage($u_id, $active){
	
			$fields = array(
		        'smsusername' => $this->smsusername,
				'smspassword' => $this->smspassword,
				'u_id'        => $u_id,
				'active'        => $active
				);
	
			$fields['domaine'] = $this->domaine;
			$fields['path'] = '/backoffice/client-status.php';
			
			return trim($this->httpPost($fields));
	}
	
	/**
	 * Send an HTTP POST request, choosing either cURL or fsockopen
	 * 
	 * @access private
	 */
	function httpPost($fields)
	{
		$qs = array();
		foreach ($fields as $k => $v)
			$qs[] = $k.'='.urlencode($v);
		$qs = join('&', $qs);
		
		
		if (function_exists('curl_init'))
			return BackOffice::httpPostCurl($qs, $fields['domaine'].$fields['path']);
		
		$errno = $errstr = '';
		if ($fp = @fsockopen("'".substr($fields['domaine'], 6)."'", 80, $errno, $errstr, 30)) 
		{   
			fputs($fp, "POST ".$fields['path']." HTTP/1.0\r\n");
			fputs($fp, "Host: ".substr($fields['domaine'], 6)."\r\n");
			fputs($fp, "User-Agent: phpMobytSms/".MOBYT_PHPSMS_VERSION."\r\n");
			fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-Length: ".strlen($qs)."\r\n");
			fputs($fp, "Connection: close\r\n");
			fputs($fp, "\r\n".$qs);
			
			$content = '';
			while (!feof($fp))
				$content .= fgets($fp, 1024);
			
			fclose($fp);
			
			return preg_replace("/^.*?\r\n\r\n/s", '', $content);
		}
		
		return false;
	}

	/**
	 * Send an HTTP POST request, through cURL
	 *
	 * @access private
	 */
	function httpPostCurl($qs, $domaine)
	{    
		if ($ch = @curl_init($domaine))
		{   
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'phpMobytSms/'.MOBYT_PHPSMS_VERSION.' (curl)');
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $qs);
		
			return curl_exec($ch);
		}
		
		return false;
	}

}

?>