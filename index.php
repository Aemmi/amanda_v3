<?php

require __DIR__.'/vendor/autoload.php';

use Src\Amanda\Router;
use Src\Amanda\Test;

$router = new Router();

if($router->get('/')){
	echo '<h2>Home Page</h2>';
}

if($router->get('/about', ['id','me'])){


	$router->render('index', welcomeUser());

}

if($router->post('/about/submit', ['name'])){
	if($router->contain('name')){
		echo $router->val('name');
	}
}

if($router->post('/form', ['name'])){
	echo $router->val('name');
}


function welcomeUser(){
	echo '<script>alert("Welcome, sir!");</script>';
}