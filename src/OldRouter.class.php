<?php

// namespace Router\HttpRequest;

class OldRouter{
    function __construct(){
        $this->full_path = $_SERVER['REQUEST_URI'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->indexedParams = array();
    }

    //convert incoming url to array
    private function url(){
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

    public function handleRequest($path){

        if($this->request_method == "GET"){

            //check if path is homepage
            if($path == "/" || $path == ""){
                return true;
                exit();
            }
            //check if url has query string
            if($this->isQueryString()){ 
                //run query string method
                $url_comp = parse_url($this->full_path);
                parse_str($url_comp['query'], $params);
                //get query parameters
                //echo $params['uid']."<br/>";

                // $uri = explode('/', $this->full_path);

                //convert path variable to url format
                $var_path = parse_url("/".$this->url()[1].str_replace(["{","}"],["?",""],$path));
                $var_path = $var_path['path'];

                $stripped_url = (str_replace("/","",$url_comp['path']));

                echo "URL ".$stripped_url;

                $stripped_path = str_replace("/","",$var_path);

                echo "PATH ".$stripped_path;

                if(($stripped_url == $stripped_path) && isset($params)){
                    $this->indexedParams = $params;
                    return true;
                }else{
                    echo $stripped_path." kl ";
                    return false;
                }

            }else{
                //run get method
                // $uri = explode('/', $this->full_path);
                
                $stripped_path = str_replace("/","",$this->url()[1].$path); //remove trailing '/'
                $stripped_url = str_replace("/","",$this->full_path);

                if($stripped_url == $stripped_path){
                    return true;
                }else{
                    return false;
                }
            }

        }else{
            //run post method
            $stripped_path = str_replace("/","",$this->url()[1].$path); //remove trailing '/'
            $stripped_url = str_replace("/","",$this->full_path);

            if($stripped_url == $stripped_path){
                return true;
            }else{
                return false;
            }
        }

    }

    public function get($path = '/'){
        return $this->handleRequest($path);
    }

    public function post($path = '/'){ 
        return $this->handleRequest($path);
    }

    public function render($file){
        include_once('temp/'.$file.'.temp.php');
    }
}