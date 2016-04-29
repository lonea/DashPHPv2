
<?php
/**
 * Product: DashPHP Framework
 * Developers: CodeBox
 * Website: http://codebox.ca
 * Email: cs@codebox.ca
 * 
 * Unauthorized distribution of this script is prohibited
 * @copyright 2016 - Reliant-web
 */
require('./init.php');
require(BOOTS_DIR.'basicPages.php');

$obj = new phpmotion;

//Enable SQL debug
//$obj->debug=true; 

//Manual Query
$obj->QueryTable("video_id", "", "videos", "");
//var_dump($obj->result);
//Get all videos
$obj->GetAllVideos();
//var_dump($obj->result);

//Get all videos by userid; 
$obj->GetAllVideosByMemeberID("Userid");
//var_dump($obj->result);

if ($obj->LoginByEmail("Email","Password")){
	$smarty->assign("loggedin", true);
	$smarty->assign("username", $_SESSION["username"]);
}else echo "wrong login";

$obj->LogOut();

loadTemplate("index"); 

?> 

