<?php
require_once("./includes/kayako/kyIncludes.php");
require_once("./includes/recaptcha/autoload.php");

$siteKey = '';
$secret = '';

$errors=0;
$error="The following errors occured while processing your form input.<ul>";

$enquiry = $_POST["enquiry"];
$email = $_POST["email"];
$name = $_POST["name"];


//Kayako codes
define('DEFAULT_TICKET_STATUS_NAME', 'Open');
define('BASE_URL', '');
define('API_KEY', '');
define('SECRET_KEY', '');
define('DEBUG', false);
define('USER_GROUP_TITLE', 'Registered');

function initKayako() {
	$config = new kyConfig(BASE_URL, API_KEY, SECRET_KEY);
	$config->setDebugEnabled(DEBUG);
	kyConfig::set($config);
}
//End Kayako


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
        
            //Kayako Submit
            initKayako();
            kyConfig::get()->setDebugEnabled(false);
            /*$default_status_id = kyTicketStatus::getAll()->filterByTitle("Open")->first()->getId();
            $default_priority_id = kyTicketPriority::getAll()->filterByTitle("Normal")->first()->getId();
            $default_type_id = kyTicketType::getAll()->filterByTitle("Issue")->first()->getId();
            */
            kyTicket::setDefaults("1", "1", "1");
            $department = kyDepartment::get("3");
            $ticket = kyTicket::createNewAuto($department, $name, $email, $message, "VimHost Website Inquiry")
            		->create();
            //End Kayako
            
            /*
            //Vision Codes
            $deptid=1;
            $url = "http://";  // URL to API file
            $username = "api";  // Admin or Staff username goes here
            $password = "apiPASS12345!";  // Admin or Staff password goes here
            
            $postfields["vis_txtusername"] = $username;
            $postfields["vis_txtuserpass"] = md5($password);
            $postfields["vis_module"] ="ticket";
            $postfields["vis_operation"] ="open_new_ticket";
            
            if ($deptid == "5"){
                $postfields["vis_domain"] = 4;    // This is the domain id under which you wish to open ticket
                $postfields["vis_department"] = 4; // This is the department id under which you wish to open ticket
            }
            elseif ($deptid == "1"){
                $postfields["vis_domain"] = 2;    // This is the domain id under which you wish to open ticket
                $postfields["vis_department"] = 2; // This is the department id under which you wish to open ticket
            }
       
            $postfields["vis_status"] = 1; // This is the status id
            $postfields["vis_priority"] = 1; // This is the ticket priority id
            $postfields["vis_subject"] = "Website Inquiry Form";
            $postfields["vis_type"] = 1;  // This is the ticket type id under which you wish to open ticket
            //$postfields["vis_client"] = 1;  // This is the client id to whom you wish to send ticket
            $postfields["vis_ticket_post"] = $message;
            $postfields["vis_from"] = $name." <".$email.">"; // This is the from email address that will be displayed in from column
            $postfields["vis_email"] = $email;
            $postfields["vis_send_message"] = 1; //
            $postfields["vis_private"] = 1; //
            $postfields["vis_send_email"] = 1; //
            $postfields["vis_as_client"] = 1; //
            
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 100);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            $data = curl_exec($ch);
            curl_close($ch);
            
            if ($data != "success") return false;
            else{header("Refresh: 0;url=http://domain.com/contact.php?contact=success");}
            //End Vision
            */
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
