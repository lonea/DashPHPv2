<?php
//
// RW Framework 2.0
// Developed by: Reliant-web
// Revision 2.0
//
require('./init.php');
require(BOOTS_DIR.'basicPages.php');

//test database
$obj = new db;
//$obj->debug=true;
$obj->QueryTable("video_id", "", "videos", "");
//var_dump($obj->result);
echo $obj->QueryCount("","videos","");


loadTemplate("index");
?>
