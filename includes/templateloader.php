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
//Template Loader

if ($debug == true){
    $smarty->debugging = true;
    $smarty->force_compile = true;
}
if ($caching_off == true){
    $smarty->caching = false;
}

$template_dir = $smarty->getTemplateDir();
$template_dir = $template_dir[0];

//Page loading function

function loadTemplate($page){
        global $smarty;
        $header_file = $smarty->fetch ($template_dir . 'header.tpl');
        $body_file = $smarty->fetch ($template_dir . $page.'.tpl');
        $footer_file = $smarty->fetch ($template_dir . 'footer.tpl');
        $output = $header_file.$body_file.$footer_file;
        echo $output;

}

function loadTemplate_content($page){
        global $smarty;
        $body_file = $smarty->fetch ($template_dir . $page.'.tpl');
        $output = $body_file;
        echo $output;    
}

function loadTemplate_header($header,$page){
        global $smarty;
        $header_file = $smarty->fetch ($template_dir . $header.'.tpl');
        $body_file = $smarty->fetch ($template_dir . $page.'.tpl');
        $footer_file = $smarty->fetch ($template_dir . 'footer.tpl');
        $output = $header_file.$body_file.$footer_file;
        echo $output;

}

function loadTemplate_footer($page){
        global $smarty;
        $header_file = $smarty->fetch ($template_dir . 'header.tpl');
        $body_file = $smarty->fetch ($template_dir . $page.'.tpl');
        $footer_file = $smarty->fetch ($template_dir . $footer.'.tpl');
        $output = $header_file.$body_file.$footer_file;
        echo $output;

}

function loadTemplate_all($header,$page,$footer){
        global $smarty;
        $header_file = $smarty->fetch ($template_dir . $header.'.tpl');
        $body_file = $smarty->fetch ($template_dir . $page.'.tpl');
        $footer_file = $smarty->fetch ($template_dir . $footer.'.tpl');
        $output = $header_file.$body_file.$footer_file;
        echo $output;

}


?>