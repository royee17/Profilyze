<?php
set_time_limit(0);
include 'DbWrapper.php';
include("../APi/api.php");


$startAttId = 1;
$endAttId = 4;


function get_tiny_url($url)  {  
	$ch = curl_init();  
	$timeout = 5;  
	curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
	$data = curl_exec($ch);  
	curl_close($ch);  
	return $data;  
}


function update_attributes($id) {
	// connect to DB 
	$dbWrapper = new DbWrapper();
	$getUrlQuery = 'SELECT `PhotoLink` FROM `photos` WHERE `Id` = '. $id;
	echo $getUrlQuery."<br>";
	// run Query
	$result = $dbWrapper->execute($getUrlQuery);
	$row = ($result->fetch_assoc());
	$picUrl = $row['PhotoLink']; // extracted link

	$picUrl = get_tiny_url($picUrl);
	//$picUrl = 'http://www.math.tau.ac.il/~milo/design/images/tova11.jpg';
	echo "pic url: ".$picUrl. "<br>";
	chdir('../APi/');

	// run in betaface
	$api = new betaFaceApi();
	$face = $api->get_Image_attributes($picUrl);
	echo $api->image_Attributes->UpdateDate;
	echo "<br>";
	echo $api->image_Attributes->EyeColor;
	echo "<br>";
	echo $api->image_Attributes->HasGlasses;
	echo "<br>";
	echo $api->image_Attributes->Age;
	echo "<br>";
	echo $api->image_Attributes->Gender;

	if($face == 1){
		$dbWrapper->update($api);
		$updateQuery = 'UPDATE `photos` SET `IsValidPhoto` = 1 WHERE `Id` = '. $id;
		echo $updateQuery."<br>";
		$result = $dbWrapper->execute($updateQuery);
	}
	else{
		echo "no output";
	}

} 

function update_all_pictures() {

	GLOBAL $startAttId;
	GLOBAL $endAttId;

	for ($i = $startAttId; $i <= $endAttId; $i++) {
	    update_attributes($i);
	}

} 

update_all_pictures();
?> 
