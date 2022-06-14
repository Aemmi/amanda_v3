<?php 

namespace Src\Amanda;

class Handler{
	public static function execute($file,$callback){
		// if($callback != null){

  //           if(gettype($callback) == 'int' || gettype($callback) == 'string' || gettype($callback) == 'boolean' || gettype($callback) == 'array'){
  //               $GLOBALS['callback'] = $callback;
  //           }else{
  //               $callback();
  //           }
  //           // call_user_func($callback, [array_merge($_GET, $_POST)]);

  //       }
        require('temp/'.$file.'.temp.php');
	}
}