<?php

namespace Src\Amanda;

use Src\Amanda\Handler;

class Router{
    function __construct(){
        $this->full_path = $_SERVER['REQUEST_URI'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->indexedParams = array();
    }

    //convert incoming url to array
    public function url(){
        return explode('/', $this->full_path);
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
    private function queryString(){
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
        }else{
            return false;
        }
    }

    public function val($p){
        if($this->request_method === "GET"){ 
            if($this->isQueryString()){
                if(isset($_GET[$p])){
                    return $_GET[$p];
                }
            }else{
                return $this->url()[array_search($p, $this->url())+1];
            }
        }elseif($this->request_method === "POST"){
            if(isset($_POST[$p])){
                return $_POST[$p];
            }
        }else{

            return false;

        }
    }

    public function handleRequest($path,$params){
        //print ($this->url()[2]);
        if($_SERVER['REQUEST_METHOD'] == "GET"){

            //check if url has query string
            if($this->isQueryString()){ 
                //run query string method
                $url_comp = parse_url($this->full_path);
                parse_str($url_comp['query'], $params);
                
                if($this->url()[2] == $this->strippedPath($path)){
                    
                    $this->params = array_keys($this->queryString());
                    //print_r($this->params);
                    return true;
                }else{
                    return false;
                }

                // print($this->queryString());

            }else{

                if($this->url()[2] == $this->strippedPath($path)){
                    //print_r($this->url());
                    $this->params = $params;
                    //print_r($this->url());
                    return true;
                }else{
                    return false;
                }

            }

        }else{
            //run post method
            if($this->url()[2] == $this->strippedPath($path)){
                //print_r($this->url());
                $this->params = $_POST;
                //print_r($this->params);
                return true;
            }else{
                return false;
            }
        }

    }

    public function get($path = '/', $params = null){
        return $this->handleRequest($path,$params);
    }

    public function post($path = '/', $params = null){ 
        return $this->handleRequest($path,$params);
    }

    public static function render($file,$var = array()){
        //print_r($var);
        // Handler::execute($file, $callback);
        extract($var);
        require('temp/'.$file.'.temp.php');

    }
}   