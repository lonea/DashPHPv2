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
$page = "contact";
if($_GET["contact"] == "success") $smarty->assign("success",true);
if($_GET["recap"] == "failed") $smarty->assign("recapfailed",true);


$name = '';
$email = '';
$message = '';
if (isset($_SESSION['return_data']) ) {
    
    $formOK = $_SESSION['return_data']['formOK'];
    $entries = $_SESSION['return_data']['entries'];
    $errors = $_SESSION['return_data']['errors'];
    unset($_SESSION['return_data']);
    
    if (!$formOK) {
        foreach ($entries as $key => $value) {
            ${$key} = $value;
        }
        $submitmessage = 'There were some problems with your submission.';
        $responsetype = 'failure';
    }
    else {
        $submitmessage = 'Thank you! Your email has been submitted.';
        $responsetype = 'success';
    }

}

$smarty->assign("title", $lang[$page."_title"]);
$smarty->assign("desc", $lang[$page."_desc"]);
$smarty->assign("keywords", $lang[$page."_keywords"]);

loadTemplate("$page");
?>
