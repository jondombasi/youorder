<?php 
require_once("inc_connexion.php");
$continuUpload = true;

if(isset($_POST)) {
	//echo $_SERVER['DOCUMENT_ROOT'];
	############ Edit settings ##############
	$BigImageMaxSize 		= 800; //Image Maximum height or width
	$DestinationDirectory	= '/home/www/you-order/www/admin/upload/'.$directory.'/'; //specify upload directory ends with / (slash)
	$Quality 				= 90; //jpeg quality
	##########################################
	
	// check $_FILES['ImageFile'] not empty
	if(!isset($_FILES['ImageFile']) || !is_uploaded_file($_FILES['ImageFile']['tmp_name'])) {
		//die('Something wrong with uploaded file, something missing!'); // output error when above checks fail.
		$continuUpload = false;
	}

	if ($continuUpload) {
		// Random number will be added after image name
		$RandomNumber 	= rand(0, 9999999999); 

		$ImageName 		= str_replace(' ','-',strtolower($_FILES['ImageFile']['name'])); //get image name
		$ImageSize 		= $_FILES['ImageFile']['size']; // get original image size
		$TempSrc	 	= $_FILES['ImageFile']['tmp_name']; // Temp name of image file stored in PHP tmp folder
		$ImageType	 	= $_FILES['ImageFile']['type']; //get file type, returns "image/png", image/jpeg, text/plain etc.	

		//Let's check allowed $ImageType, we use PHP SWITCH statement here
		switch(strtolower($ImageType)) {
			case 'image/png':
				//Create a new image from file 
				$CreatedImage =  imagecreatefrompng($_FILES['ImageFile']['tmp_name']);
				break;
			case 'image/gif':
				$CreatedImage =  imagecreatefromgif($_FILES['ImageFile']['tmp_name']);
				break;			
			case 'image/jpeg':
			case 'image/pjpeg':
				$CreatedImage = imagecreatefromjpeg($_FILES['ImageFile']['tmp_name']);
				break;
			default:
				//output error and exit
				//die('Unsupported File!'); 
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
		$NewImageName = $RandomNumber.'.'.$ImageExt;
		$NewImageNameOriginal = $RandomNumber.'_original.'.$ImageExt;
		
		//set the Destination Image
		$DestRandImageName 			= $DestinationDirectory.$NewImageName; // Image with destination directory
		$DestRandImageName_original = $DestinationDirectory.$NewImageNameOriginal; // Original with destination directory

		//save the original image
		move_uploaded_file($TempSrc, $DestRandImageName_original);
		
		//Resize image to Specified Size by calling resizeImage function.
		if(resizeImage($CurWidth,$CurHeight,$BigImageMaxSize,$DestRandImageName,$CreatedImage,$Quality,$ImageType)) {
			//We have succesfully resized	
			//Rotate image
			$exif = @exif_read_data($_FILES['ImageFile']['tmp_name']);
			if(!empty($exif['Orientation'])) {
			    switch($exif['Orientation']) {
			        case 8:
			            rotation($DestRandImageName,90);
			            break;
			        case 3:
			            rotation($DestRandImageName,180);
			            break;
			        case 6:
			            rotation($DestRandImageName,-90);
			            break;
			    }
			}
			// Insert info into database table!
			$req = $sql->exec("UPDATE ".$directory." SET photo = '".$NewImageName."' WHERE id = '".$id."'");
		}
		else{
			//die('Resize Error'); //output error
		}

		/*move_uploaded_file($TempSrc, $DestRandImageName);
		$exif = exif_read_data($TempSrc);
		if(!empty($exif['Orientation'])) {
		    switch($exif['Orientation']) {
		        case 8:
		            rotation($DestRandImageName,90);
		            break;
		        case 3:
		            rotation($DestRandImageName,180);
		            break;
		        case 6:
		            rotation($DestRandImageName,-90);
		            break;
		    }
		}
		$req = $sql->exec("UPDATE livreurs SET photo = '".$NewImageName."' WHERE id = '".$id."'");
		echo "UPDATE livreurs SET photo = '".$NewImageName."' WHERE id = '".$id."'";*/
	}
	else {
		//die('Error continue'); 
	}
}
else {
	//die('Error POST'); 
}


// This function will proportionally resize image 
function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType) {
	//Check Image size is not 0
	if($CurWidth <= 0 || $CurHeight <= 0) {
		return false;
	}
	
	//Construct a proportional size of new image
	$ImageScale      	= min($MaxSize/$CurWidth, $MaxSize/$CurHeight); 
	$NewWidth  			= ceil($ImageScale*$CurWidth);
	$NewHeight 			= ceil($ImageScale*$CurHeight);
	$NewCanves 			= imagecreatetruecolor($NewWidth, $NewHeight);
	
	// Resize Image
	if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight)) {
		switch(strtolower($ImageType)) {
			case 'image/png':
				imagepng($NewCanves,$DestFolder);
				break;
			case 'image/gif':
				imagegif($NewCanves,$DestFolder);
				break;			
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;
			default:
				return false;
		}
		//Destroy image, frees memory	
		if(is_resource($NewCanves)) {imagedestroy($NewCanves);} 
		return true;
	}
}

function rotation($img,$degres){
   	if(file_exists($img)) {        
		$image = getimagesize($img);
		$image_type = $image['2'];
		                           
		// création de l'image selon son extension (type) :
		if($image_type == "1") $source = imagecreatefromgif($img);
		if($image_type == "2") $source = imagecreatefromjpeg($img);
		if($image_type == "3") $source = imagecreatefrompng($img);
		if($image_type == "6") $source = imagecreatefromwbmp($img);

		//echo $source."<br/>";
		//rotation de l'image
		$rotation = imagerotate($source,$degres,-1) or die("Erreur lors de la rotation de ".$img);
		//Le -1 permet de remplir les zones vides avec du transparent

		// sauvegarde de l'image (selon son type :
		if($image_type == "1") imagegif($rotation,$img);
		if($image_type == "2") imagejpeg($rotation,$img);
		if($image_type == "3") imagepng($rotation,$img);
		if($image_type == "6") imagewbmp($rotation,$img);          
   	}
   	else {
        echo("Le fichier ".$img." n'existe pas");
   	}
}
