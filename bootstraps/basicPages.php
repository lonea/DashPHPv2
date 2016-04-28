<?php
//Jumpstart the template
define('SMARTY_DIR', MAIN_DIR.'includes/libs/template/smarty/');
require_once(MAIN_DIR."includes/libs/template/smarty/Smarty.class.php");

global $smarty;
$smarty = new Smarty;
$smarty->setTemplateDir('./templates/');
$smarty->setCompileDir('./templates_c/');
$smarty->setConfigDir('./configs/');
$smarty->setCacheDir('./cdir/');
$smarty->caching = true;
$smarty->cache_lifetime = $cache_timer;

require_once(INC_DIR."templateloader.php");

//Jumpstart the email
require_once(MAIN_DIR."./includes/libs/mail/swift/swift_required.php");



?>