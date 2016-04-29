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
class upload_functions{
    
    var $files_array; 
    var $file_name;
    var $ext;
    var $path;
    var $id;
    var $source;
    
    public function save_file(){
        
        if (!file_exists($this->path)){
            mkdir($this->path);
        }        
        
        $info = pathinfo($this->file_name);
        
        $filename = $info["filename"];
        $ext = $info["extension"];
        $ext = strtolower($ext);
        
        $filename = $filename.".".$ext;
        
        
        if (move_uploaded_file($this->source, $this->path.$filename)){
            return true;
        }
        else{
            return false;
        }
        
    }
    
    public function file_verify($file){
        if ($file["error"] > 0){
            //echo "Error: " . $_FILES["file"]["error"] . "<br />";
            return false;
        }
        else if( $file["type"] != "image/jpeg" && $file["type"] != "image/gif" && $file["type"] != "image/png"){
        //echo "Error: Invalid File Type";
        //echo $_FILES["file"]["type"];
            return false;
        }
        else{
            return true;
        }
    }
    
    public function file_verify_swf($file){
        if ($_FILES["file"]["error"] > 0){
            //echo "Error: " . $_FILES["file"]["error"] . "<br />";
            return false;
        }
        else if( $_FILES["file"]["type"] != "application/x-shockwave-flash"){
        //echo "Error: Invalid File Type";
        //echo $_FILES["file"]["type"];
            return false;
        }
        else{
            return true;
        }
    }    
    
    public function remove_file(){
        if (@unlink($this->path.$this->file_name.".".$this->ext)){
            return true;
        }
        else{
            return false;
        }
    }
}
?>