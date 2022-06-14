<?php

require "bootstrap.inc.php";

use Src\Amanda\Router;
use Src\Amanda\Test;
use Src\Amanda\Blog;

$router = new Router();

$blog = new Blog();

if($router->get('/')){
	echo '<h2>Home Page</h2>';
}

if($router->get('/about', ['id','me'])){
	$data = $blog->all();
	$router::render('index', ['data'=>$data]);

}

if($router->post('/about/submit', ['name'])){
	if($router->contain('name')){
		echo $router->val('name');
	}
}

if($router->post('/form', ['name'])){
	echo $router->val('name');
}