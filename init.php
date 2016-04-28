<?php
//
// RW Framework 2.0
// Developed by: Reliant-web
// Revision 2.0
//
//Init Script
@session_start();
define('MAIN_DIR',dirname(__FILE__) . '/');
define('CONFIG_DIR',MAIN_DIR . 'configs/');
define('INC_DIR',MAIN_DIR . 'includes/');
define('CLASS_DIR',MAIN_DIR . 'classes/');
define('BOOTS_DIR',MAIN_DIR . 'bootstraps/');
require(CONFIG_DIR.'settings.php');




?>
