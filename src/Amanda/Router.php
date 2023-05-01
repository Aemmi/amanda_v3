<?php

namespace Src\Amanda;
use Src\Amanda\DB;

class Router extends DB{
    function __construct(){
        parent::__construct();
        $this->full_path = $_SERVER['REQUEST_URI'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->indexedParams = array();
        $this->_getList = [];
        $this->_postList = [];
    }

    //convert incoming url to array
    public function url(){
        if($this->isQueryString()){
            $url = $this->full_path;
            $query_string_pos = strpos($url, '?');

            if ($query_string_pos !== false) {
              // The query string was found in the URL
              // You can add a slash before it like this:
              $url = substr_replace($url, '/', $query_string_pos,0);
              return explode('/', $url);
            }
        }else{
            return explode('/', $this->full_path);
        }
        
    }


    //check if request is query string
    public function isQueryString(){
        if($_SERVER['QUERY_STRING']){
            return true;
        }else{
            return false;
        }
    }

    //check if url contains query strings
    public function queryString(){
        $str = $this->url()[count($this->url())-(1)];
        parse_str(str_replace("?", "", $str),$get);
        $this->request = $get;
        return $this->request;
    }

    //get paramaters from query string
    public function params(){
        return $this->indexedParams;
    }

    private function strippedPath($path){
        return str_replace("/","",$path);
    }

    public function contain($p){ 
        if(isset($_GET[$p])){
            return true;
        }elseif(in_array($p,$this->params)){
            return true;
        }elseif(isset($_POST[$p])){
            return true;
        }else{
            return false;
        }
    }

    public function val($p){

        if($_SERVER['REQUEST_METHOD'] === "GET"){ 

            if($this->isQueryString()){
                if(isset($_GET[$p])){
                    return sanitize($_GET[$p]);
                }
            }else{
                return $this->url()[array_search($p, $this->url())+1];
            }

        }else{

            return false;

        }

    }

    public function input($p){

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            if(isset($_POST[$p])){
                return sanitize($_POST[$p]);
            }

        }else{

            return false;

        }

    }

    public function handleRequest($path,$params){
        
        if($_ENV['APP_ENV'] == 'local'){
            $index = 2;
        }else{
            $index = 1;
        }

        if($_SERVER['REQUEST_METHOD'] === "GET"){

            //check if url has query string
            if($this->isQueryString()){ 
                //run query string method
                $url_comp = parse_url($this->full_path);
                parse_str($url_comp['query'], $params);
                
                if($this->url()[$index] == $this->strippedPath($path)){
                    
                    $this->params = array_keys($this->queryString());
                    return true;

                }else{

                    return false;

                }

            }else{

                if($this->url()[$index] == $this->strippedPath($path)){

                    $this->params = $params;
                    return true;

                }else{

                    return false;

                }

            }

        }

        if($_SERVER['REQUEST_METHOD'] === "POST"){
            //run post method
            if($this->url()[$index] == $this->strippedPath($path)){
         
                $this->params = $_POST;
                return true;

            }else{

                return false;

            }
        }

    }

    public function get($path = '/', $params = null){
        if($_SERVER['REQUEST_METHOD'] === "GET"){
            $this->_getList[] = $this->strippedPath($path);
            return $this->handleRequest($path,$params);
        }
    }

    public function post($path = '/', $params = null){ 
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $this->_postList[] = $this->strippedPath($path);
            return $this->handleRequest($path,$params);
        }
    }

    public function notFound(){
        
        if($_ENV['APP_ENV'] == 'local'){
            $index = 2;
        }else{
            $index = 1;
        }

        if($_SERVER['REQUEST_METHOD'] == "GET"){

            
            if(in_array($this->url()[$index], $this->_getList)){
               
                return false;

            }else{

                return true;

            }

        }else{

            if(in_array($this->url()[$index], $this->_postList)){
               
                return false;

            }else{

                return true;

            }

        }
    }

    public static function render($file,$var = array()){
      
        extract($var);
        require('temp/'.$file.'.temp.php');

    }

    public static function use($file,$var = array()){
       
        extract($var);
        require($file.'.route.php');

    }

    public function _url($index){
        if($_ENV['APP_ENV'] == 'local'){
            $index = $index;
        }else{
            if($index != 0){
                $index = ($index-1);
            }else{
                $index = $index;
            }
        }
        if(array_key_exists($index, $this->url())){
            if($_SERVER['REQUEST_METHOD'] == "GET"){
                $this->_getList[] = $this->url()[$index];
                return $this->url()[$index];
            }else{
                $this->_postList[] = $this->url()[$index];
                return $this->url()[$index];
            }
        }else{
            return null;
        }
        
    }
}   