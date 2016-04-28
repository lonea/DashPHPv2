<?php

/**
 * Product: DashDouga Framework
 * Developers: Avexweb
 * Website: http://avexweb.com
 * Email: help.desk@avexweb.com
 * 
 * Unauthorized distribution of this script is prohibited
 * @copyright 2010 - Avexweb
 */


//This class will handle all the database transaction

class db{
    private static $settings_file = "configs/mysql.php";
    var $conn;
    var $result;
    var $raw_result;
    var $keys;
    var $values;
    var $pconnect;
    var $use_utf8 = FALSE;
    var $debug = FALSE;
    
    function __construct(){
        include($settings_file);
        
        $this->connect($db, $user, $pw, $server, $port);
    }
    
    /////////////////// Query Functions //////////////////////
    
    //This function return sanitized string
    function sanitize($str){
        $str = strip_tags($str);
        return mysql_real_escape_string($str);
    }
    
    //This function return sanitized string in UTF8
    function sanitize_utf8($str){
        return htmlentities($str);
    }
    
    //This function return query
    //Returns: real return
    public function query_table($cols, $condition, $table, $opts){
  
        if ($condition == NULL){
            $sql = "SELECT $cols FROM $table";
        }
        else{
            $this->keyvalSeparte($condition);
            $newconditions = $this->expressionGen_AND($this->keys, $this->values);
            $sql = "SELECT $cols FROM $table WHERE $newconditions";    
        }
        
        if ($opts != NULL){
            $sql .= " ".$opts;
        }        
        
        if ($this->query_me($sql)){
            $this->result = mysql_fetch_array($this->raw_result);
            return true;
        }
        
    } 
    
    //This function return query
    //Save variable $this->raw_result 
    //Returns: T/F
    public function query_table_raw($cols, $condition, $table, $opts){

        if ($condition == NULL){
            $sql = "SELECT $cols FROM $table";
        }
        else{
            $this->keyvalSeparte($condition);
            $newconditions = $this->expressionGen_AND($this->keys, $this->values);            
            $sql = "SELECT $cols FROM $table WHERE $newconditions";    
        }
        
        if ($opts != NULL){
            $sql .= " ".$opts;
        }
        
        if ($this->query_me($sql)){
            return TRUE;
        }
    }   
    
    //This function perfoum a count(*) query
    //Return: value of count(*)
    public function query_count($condition, $table, $opts){
        
        if ($condition == NULL){
            
            $sql = "SELECT count(*) FROM $table";
        }
        else{
            $this->keyvalSeparte($condition);
            $newconditions = $this->expressionGen_AND($this->keys, $this->values);   
            $sql = "SELECT count(*) FROM $table WHERE $newconditions";    
        }
        
        if ($opts != NULL){
            $sql .= " ".$opts;
        }        
        
        if ($this->query_me($sql)){
            $count = mysql_result($this->raw_result, 0);
            return $count;
        }
        
    }
    
    //This function performs the actual query
    //Return: T/F    
    public function query_me($sql){
        if ($this->use_utf8 != false){
            mysql_set_charset('utf8');
            //mysql_query("SET NAMES utf8");
            //mysql_query("SET CHARACTER SET utf8");            
        }
                
        
        if ($this->debug == TRUE){
            echo $sql;
        }
        
        $query = mysql_query($sql, $this->conn);
        
        if (!$query){
            $this->error_report("Failed to Run the Query: ".mysql_error());        
        }       
        else{
            $this->raw_result = $query;
            return TRUE;
        }
    }
    
    /////////////////// Insert Functions ///////////////////////
    
    //This function insert a row into a table
    //Return T/F    
    public function into_table($cols, $table){
        
        $this->keyvalSeparte($cols);
        $column = $this->arraySet_noq($this->keys);
        $values = $this->arraySet($this->values);
        
        $sql = "INSERT INTO $table ($column) VALUES ($values)";
        if ($this->query_me($sql)){
            return true;
        }        
        else return false;
        
    }  
    
    /////////////////// Update Functions ///////////////////////
    //This function update value in a table
    //Return T/F
    public function update_table($cols, $condition, $table){
        
        $this->keyvalSeparte($cols);
        $expression = $this->expressionGen($this->keys, $this->values);
        
        unset($this->keys);
        unset($this->values);
        if ($condition == NULL){
            $sql = "UPDATE $table SET $expression";    
        }
        else{
            $this->keyvalSeparte($condition);
            $newconditions = $this->expressionGen_AND($this->keys, $this->values);
            $sql = "UPDATE $table SET $expression WHERE $newconditions";                 
        }
        
        if ($this->query_me($sql)){
            return true;
        }      
        else return false;
        
    }
    
    /////////////////// Remove Functions ///////////////////////
    //This function remove a row in a table
    //Return T/F    
    public function remove_table($condition, $table){
                
        if ($condition != NULL){
            $this->keyvalSeparte($condition);
            $newcond = $this->expressionGen_AND($this->keys, $this->values);
            $sql = "DELETE FROM $table WHERE $newcond";    
        }
        else{
            $sql = "DELETE FROM $table";
        }
        
        if ($this->query_me($sql)){
            return true;
        }
        else return false;
        
    }
    
        
    /////////////////// Connection Functions ///////////////////////
    
    //This function connect to the db
    public function connect($db,$user,$pw,$server,$port){
        if ($port != "3306"){
            $server = $server.":".$port;
        }        
        
        $this->conn = mysql_connect($server, $user, $pw);
        if (!$this->conn){
            $this->error_report("Could Not Connect To MySQL Server: ".mysql_error());
            return false;
        }

        $db_conn = mysql_select_db("$db", $this->conn);
        if (!$db_conn){
            $this->error_report("Could Not Connect To Database: ".mysql_error());
            return false;
        }
        
        return true;
    }
    
    //This function connect to the db with a persistent connection
    public function p_connect($db,$user,$pw,$server,$port){
        if ($port != "3306"){
            $server = $server.":".$port;
        }        
        
        $this->conn = mysql_pconnect($server, $user, $pw);
        if (!$this->conn){
            $this->error_report("Could Not Connect To MySQL Server: ".mysql_error());
        }

        $db_conn = mysql_select_db("$db", $this->conn);
        if (!$db_conn){
            $this->error_report("Could Not Connect To Database: ".mysql_error());
        }
        else{
            $this->pconnect = TRUE;
        }
        
    }
    
    //This function close the mysql connection
    public function conn_close(){
        mysql_close($this->conn);
    }
    
////////////////////// MISC Functions ////////////////////////    
    
    //This function dispay database error
    public function display_error($msg){
        
    }
    
    //This function report database connection error
    //Die 
    private function error_report($msg){
        die($msg);
    }
    
    //This function organizes the columns
    //Returns: Columns in query form
    private function arraySet($array){
        
        for($i=0;$i<count($array);$i++){
            if ($i+1 == count($array)){
                $newArray .= "'".$array[$i]."'";
            }
            else{
                $newArray .= "'".$array[$i]."'".",";
            }
        }

        return $newArray;
    }
    
    //This function organizes the columns without quote
    //Returns: Columns in query form
    private function arraySet_noq($array){
        
        for($i=0;$i<count($array);$i++){
            if ($i+1 == count($array)){
                $newArray .= $array[$i];
            }
            else{
                $newArray .= $array[$i].",";
            }
        }

        return $newArray;
    }    
   
    //This function separate the keys and values
    //Return: None
    //Save: $this->keys, $this->values
    private function keyvalSeparte($input){
        unset($this->keys);
        unset($this->values);        
        
        foreach($input as $key => $value){
            $this->keys[] = $key;
            $this->values[] = $value;
        } 
    }
    
    //This function combine keys and values into a key='value' AND format
    //Return: String
    private function expressionGen_AND($key, $value){
        
        $pattern = "/\|\*\|LIKE\|\*\|/";
        $pattern2 = "/\|\*\|NOT LIKE\|\*\|/";
        $pattern3 = "/\|\*\|NOT EQUAL\|\*\|/";
        
        for($i=0;$i<count($key);$i++){
            if ($i+1 == count($key)){
                if (preg_match($pattern, $value[$i])){
                    $match = preg_split($pattern, $value[$i]);
                    $str .= $key[$i]." LIKE '".$match[1]."'";
                }
                else if (preg_match($pattern2, $value[$i])){
                    $match = preg_split($pattern2, $value[$i]);
                    $str .= $key[$i]." NOT LIKE '".$match[1]."'";
                }
                else if (preg_match($pattern3, $value[$i])){
                    $match = preg_split($pattern3, $value[$i]);
                    $str .= $key[$i]." != '".$match[1]."'";
                }                
                else{
                    $str .= $key[$i]."='".$value[$i]."'";    
                }
                
            }
            else{
                if (preg_match($pattern, $value[$i])){
                    $match = preg_split($pattern, $value[$i]);
                    $str .= $key[$i]." LIKE '".$match[1]."'"." AND ";
                }
                else if (preg_match($pattern2, $value[$i])){
                    $match = preg_split($pattern2, $value[$i]);
                    $str .= $key[$i]." NOT LIKE '".$match[1]."'"." AND ";                    
                }
                else if (preg_match($pattern3, $value[$i])){
                    $match = preg_split($pattern3, $value[$i]);
                    $str .= $key[$i]." !='".$match[1]."'"." AND ";                    
                }
                else{
                    $str .= $key[$i]."='".$value[$i]."'"." AND ";    
                }
                
            }
        }
        
        return $str;
    }

    //This function combine keys and values into a key='value', format
    //Return: String
    private function expressionGen($key, $value){
        
        $pattern = "/\|\*\|LIKE\|\*\|/";
        $pattern2 = "/\|\*\|NOT LIKE\|\*\|/";
        $pattern3 = "/\|\*\|NOT EQUAL\|\*\|/";
        
        for($i=0;$i<count($key);$i++){
            if ($i+1 == count($key)){
                if (preg_match($pattern, $value[$i])){
                    $match = preg_split($pattern, $value[$i]);
                    $str .= $key[$i]." LIKE '".$match[1]."'";
                }
                else if (preg_match($pattern2, $value[$i])){
                    $match = preg_split($pattern2, $value[$i]);
                    $str .= $key[$i]." NOT LIKE '".$match[1]."'";
                }
                else if (preg_match($pattern3, $value[$i])){
                    $match = preg_split($pattern3, $value[$i]);
                    $str .= $key[$i]." != '".$match[1]."'";
                }                
                else{
                    $str .= $key[$i]."='".$value[$i]."'";    
                }
                
            }
            else{
                if (preg_match($pattern, $value[$i])){
                    $match = preg_split($pattern, $value[$i]);
                    $str .= $key[$i]." LIKE '".$match[1]."'"." , ";
                }
                else if (preg_match($pattern2, $value[$i])){
                    $match = preg_split($pattern2, $value[$i]);
                    $str .= $key[$i]." NOT LIKE '".$match[1]."'"." , ";                    
                }
                else if (preg_match($pattern3, $value[$i])){
                    $match = preg_split($pattern3, $value[$i]);
                    $str .= $key[$i]." != '".$match[1]."'"." , ";
                }                       
                else{
                    $str .= $key[$i]."='".$value[$i]."'"." , ";    
                }
                
            }
        }
        
        return $str;
    }

}
    

?>