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
require_once("./includes/recaptcha/autoload.php");

$siteKey = '';
$secret = '';

$errors=0;
$error="The following errors occured while processing your form input.<ul>";

$enquiry = $_POST["enquiry"];
$email = $_POST["email"];
$name = $_POST["name"];


if (isset($_POST['g-recaptcha-response'])){
    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
    
    if ($resp->isSuccess()){ //Recaptcha real
            $enquiry=preg_replace("/(\015\012)|(\015)|(\012)/","&nbsp;<br />", $enquiry);if($name=="" || $email=="" || $enquiry=="" ){
            $errors=1;
            $error.="<li>You did not enter one or more of the required fields. Please go back and try again.";
        }
        if(!eregi("^[a-z0-9]+([_\\.-][a-z0-9]+)*" ."@"."([a-z0-9]+([\.-][a-z0-9]+)*)+"."\\.[a-z]{2,}"."$",$email)){
            $error.="<li>Invalid email address entered";
            $errors=1;
        }
        if($errors==1) echo $error;
        else{
            $where_form_is="http".($HTTP_SERVER_VARS["HTTPS"]=="on"?"s":"")."://".$SERVER_NAME.strrev(strstr(strrev($PHP_SELF),"/"));
            $message="name: ".$name."<br/>email: ".$email."<br/>Inquiry: ".$enquiry."<br/>IP: ".$_SERVER["REMOTE_ADDR"];
            $message = stripslashes($message);
            $message = str_replace("\r\n\r\n", "<br/><br/>", $message);
        
            
            header("Refresh: 0;url=http://domain.com/contact.php?contact=success");      
        }  
    }
    else{
        header("Refresh: 0;url=http://domain.com/contact.php?recap=failed");       
    }
}
else{
    header("Refresh: 0;url=http://domain.com/contact.php?recap=failed");
}

/////////////////




?>
