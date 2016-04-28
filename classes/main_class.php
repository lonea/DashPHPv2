<?php

class basic{

    public function st($var){
        return strip_tags($var);
    }
    
    public function strip($var){
        $var = mysql_real_escape_string($var);
        $var = strip_tags($var);

        return $var;
    }    
    
    public function sm($var){
        $var = mysql_real_escape_string($var);
        return $var;
    }
}

?>