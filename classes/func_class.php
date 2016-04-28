<?php

class Init{

    //Accept length of the string
    //Result random string
    public function randomcode_gen($z){
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        srand ( ( double ) microtime () * 1000000 );
        $i = 0;
        $pass = '';

        while ( $i < $z ){
            $num = rand () % 62;
            $tmp = substr ( $chars, $num, 1 );
            $pass = $pass . $tmp;
            $i ++;
        }
        return $pass;        
    }
    
    //Get the path of the document root
    public function getBasepath($SERVER){
        return $SERVER["DOCUMENT_ROOT"];
    }
    
    //Get the filename
    public function getFilename($SERVER){
        
        $arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
        $arrStr = array_reverse($arrStr);
        
        return $arrStr[0];        
    }
    
    //Get domain name
    public function getDomain($SERVER){
        
        $array = array($SERVER[SERVER_NAME], "www.".$SERVER[SERVER_NAME]);
        return $array; 
    }
    
    //Get the IP of the server
    public function getServerIP($SERVER){
        return $SERVER["SERVER_ADDR"];
    }

    //Get the path of the script
    public function get_execpath($SERVER){
        
        $arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
        if (count($arrStr) == "2"){
            return $SERVER["DOCUMENT_ROOT"];    
        }
        else{
            $tmp = $SERVER["DOCUMENT_ROOT"];
            for($i=1;$i<count($arrStr)-1;$i++){
            $tmp .= "/".$arrStr[$i];
            }
            return $tmp;
        }
        
        
    }    
}

?>