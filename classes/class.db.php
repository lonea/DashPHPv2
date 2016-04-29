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

class phpmotion extends db{
	public function GetAllVideos(){
		$this->QueryTable("*", "", "videos", "");
	}
	
	public function GetAllVideosByMemeberID($userid){
		$this->QueryTable("*", array("user_id"=>$userid), "videos", "");
	}
	
	public function LoginByEmail($email, $password){
		try{
			$sql = "SELECT count(*) FROM member_profile WHERE email_address = :email AND password = :password";
			if ($this->debug == TRUE) echo $sql;
			$query = $this->conn->prepare($sql);
			$query->execute(array(":email"=>$email,":password"=>md5($password)));
			$query = $query->fetch( \PDO::FETCH_ASSOC);
			$count = $query["count(*)"];
			if ($count == 1){
				//logged in
				$sql = "SELECT user_id, user_name, email_address  FROM member_profile WHERE email_address = :email AND password = :password";
				if ($this->debug == TRUE) echo $sql;
				$query = $this->conn->prepare($sql);
				$query->execute(array(":email"=>$email,":password"=>md5($password)));
				$query = $query->fetch( \PDO::FETCH_ASSOC);
				
				$_SESSION["loggedin"] = true;
				$_SESSION["username"] = $query["user_name"];
				$_SESSION["userid"] = $query["user_id"];
				$_SESSION["email"] = $query["email_address"];
				
				return true;				
			}
			else return false;
		
		}catch(PDOException $e){
			$this->error_report("Failed to run query: ".$e->getMessage());
			return false;
		}
	}
	
	public function LoginByUsername($username, $password){
		try{
			$sql = "SELECT count(*) FROM member_profile WHERE user_name = :username AND password = :password";
			if ($this->debug == TRUE) echo $sql;
			$query = $this->conn->prepare($sql);
			$query->execute(array(":username"=>$username,":password"=>md5($password)));
			$query = $query->fetch( \PDO::FETCH_ASSOC);
			$count = $query["count(*)"];
			if ($count == 1){
				//logged in
				$sql = "SELECT user_id, user_name, email_address  FROM member_profile WHERE username = :username AND password = :password";
				if ($this->debug == TRUE) echo $sql;
				$query = $this->conn->prepare($sql);
				$query->execute(array(":username"=>$username,":password"=>md5($password)));
				$query = $query->fetch( \PDO::FETCH_ASSOC);
				
				$_SESSION["loggedin"] = true;
				$_SESSION["username"] = $query["user_name"];
				$_SESSION["userid"] = $query["user_id"];
				$_SESSION["email"] = $query["email_address"];
				
				return true;				
			}
			else return false;
		}catch(PDOException $e){
			$this->error_report("Failed to run query: ".$e->getMessage());
			return false;
		}
	}
	
	public function LogOut(){
		unset($_SESSION["userid"], $_SESSION["loggedin"], $_SESSION["email"]);
	}
}
 
 
//This class will handle all the database transaction

class db{
    private $SettingsFile = "configs/settings.db.php";
    var $conn;
    var $result;
    var $RawResult;
    var $keys;
    var $values;
    var $pconnect;
    var $use_utf8 = FALSE;
    var $debug = FALSE;
    
    function __construct(){
        include($this->SettingsFile);
        
        $this->connect($db, $user, $pw, $server, $port);
    }
    
    /////////////////// Query Functions //////////////////////
    
    //This function return sanitized string
    function sanitize($str){
        $str = strip_tags($str);
        return $str;
    }
    
    //This function return sanitized string in UTF8
    function sanitize_utf8($str){
        return htmlentities($str);
    }
    
    //This function return query
    //Returns: real return
    public function QueryTable($cols, $condition, $table, $opts){
  
        if ($condition == NULL){
            $sql = "SELECT $cols FROM $table";
        }
        else{
            $this->keyvalSeparate($condition);
            $newconditions = $this->expressionGen_AND($this->keys, $this->values);
            $sql = "SELECT $cols FROM $table WHERE $newconditions";    
        }
        
		//Add additional query string options
        if ($opts != NULL) $sql .= " ".$opts;
        
		$query = $this->QueryMe($sql);
		$query = $query->fetchAll( \PDO::FETCH_ASSOC);
		$this->result = $query;
		return true;
        
    } 
    
    //This function return query
    //Save variable $this->raw_result 
    //Returns: T/F
    public function QueryTableRaw($cols, $condition, $table, $opts){

        if ($condition == NULL){
            $sql = "SELECT $cols FROM $table";
        }
        else{
            $this->keyvalSeparate($condition);
            $newconditions = $this->expressionGen_AND($this->keys, $this->values);            
            $sql = "SELECT $cols FROM $table WHERE $newconditions";    
        }
        
        if ($opts != NULL) $sql .= " ".$opts;
        
		$query = $this->QueryMe($sql);
		$this->RawResult = $query;
    }   
    
    //This function perfoum a count(*) query
    //Return: value of count(*)
    public function QueryCount($condition, $table, $opts){
        
        if ($condition == NULL){            
            $sql = "SELECT count(*) FROM $table";
        }
        else{
            $this->keyvalSeparate($condition);
            $newconditions = $this->expressionGen_AND($this->keys, $this->values);   
            $sql = "SELECT count(*) FROM $table WHERE $newconditions";    
        }
        
        if ($opts != NULL) $sql .= " ".$opts;
        
		$query = $this->QueryMe($sql);
		$query = $query->fetch( \PDO::FETCH_ASSOC);
		$count = $query["count(*)"];
		return $count;
        
    }
    
    //This function performs the actual query
    //Return: T/F    
    public function QueryMe($sql){
        if ($this->UseUTF8 != false){
            //mysql_set_charset('utf8');
            //mysql_query("SET NAMES utf8");
            //mysql_query("SET CHARACTER SET utf8");            
        }
                        
        if ($this->debug == TRUE) echo $sql;
        
		try{
			$query = $this->conn->prepare($sql);
			$query->execute();
			return $query;
		}catch(PDOException $e){
			$this->error_report("Failed to run query: ".$e->getMessage());
			return false;
		}
    }
    
    /////////////////// Insert Functions ///////////////////////
    
    //This function insert a row into a table
    //Return T/F    
    public function IntoTable($cols, $table){
        
        $this->keyvalSeparate($cols);
        $column = $this->arraySet_noq($this->keys);
        $values = $this->arraySet($this->values);
        
        $sql = "INSERT INTO $table ($column) VALUES ($values)";
		if ($this->debug == TRUE) echo $sql;
		
		try{
			$query = $this->conn->prepare($sql);
			$query->execute();
			
			return true;
		}catch(PDOException $e){
			$this->error_report("Failed to run query: ".$e->getMessage());
			return false;
		}
        
    }  
    
    /////////////////// Update Functions ///////////////////////
    //This function update value in a table
    //Return T/F
    public function UpdateTable($cols, $condition, $table){
        
        $this->keyvalSeparate($cols);
        $expression = $this->expressionGen($this->keys, $this->values);
        
        unset($this->keys);
        unset($this->values);
        if ($condition == NULL){
            $sql = "UPDATE $table SET $expression";    
        }
        else{
            $this->keyvalSeparate($condition);
            $newconditions = $this->expressionGen_AND($this->keys, $this->values);
            $sql = "UPDATE $table SET $expression WHERE $newconditions";                 
        }
        if ($this->debug == TRUE) echo $sql;
		try{
			$query = $this->conn->prepare($sql);
			$query->execute();
			
			return true;
		}catch(PDOException $e){
			$this->error_report("Failed to run query: ".$e->getMessage());
			return false;
		}
        
    }
    
    /////////////////// Remove Functions ///////////////////////
    //This function remove a row in a table
    //Return T/F    
    public function RemoveTable($condition, $table){
                
        if ($condition != NULL){
            $this->keyvalSeparate($condition);
            $newcond = $this->expressionGen_AND($this->keys, $this->values);
            $sql = "DELETE FROM $table WHERE $newcond";    
        }
        else{
			$this->error_report("Bad remove table string");
            return false;
        }
        
		try{
			$query = $this->conn->prepare($sql);
			$query->execute();
			
			return true;
		}catch(PDOException $e){
			$this->error_report("Failed to run query: ".$e->getMessage());
			return false;
		}
        
    }
    
        
    /////////////////// Connection Functions ///////////////////////
    
    //This function connect to the db
    public function connect($db,$user,$pw,$server,$port){
		try {
			$this->conn = new PDO("mysql:host=$server;port=$port;dbname=$db", "$user", "$pw", array( PDO::ATTR_PERSISTENT => false));    	
			return true;
		}catch(PDOException $e){
			$this->error_report("Could not connect to database: ".$e->getMessage());
			return false;
		}
    }
    
    //This function connect to the db with a persistent connection
    public function p_connect($db,$user,$pw,$server,$port){
		try {
			$this->conn = new PDO("mysql:host=$server;port=$port;dbname=$db", "$user", "$pw", array( PDO::ATTR_PERSISTENT => false));    	
			return true;
		}catch(PDOException $e){
			$this->error_report("Could not connect to database: ".$e->getMessage());
			return false;
		}
    }
    
    //This function close the mysql connection
    public function conn_close(){
        $this->conn = null;
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
    private function keyvalSeparate($input){
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