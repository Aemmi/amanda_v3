<?php

require "bootstrap.inc.php";

use Src\Amanda\Router;
use Src\Amanda\QueryBuilder;

$router = new Router();
$qb = new QueryBuilder();

if($router->get('/')){

	$title = "Home Page";
	$router::render('index', ['title'=>$title]);

}

if($router->get('/about', ['id','me'])){

	$title = "About Page";
	$router::render('about', ['title'=>$title]);

}

/**
 * make a get request with url params
 **/ 
if($router->get('/get', ['id','me'])){

	$id = $router->val('id');
	$me = $router->val('me');
	$title = "Simple Get Request Page";
	$router::render('get', ['title'=>$title,'id'=>$id,'me'=>$me]);

}

/**
 * make a post request and get form inputs
 **/ 
if($router->post('/contact', ['fname','message'])){

	echo $router->input('fname')."<br>";
	echo $router->input('message');

}

/**
 * make a simple get request and pass in data or variable to view
 **/ 
if($router->get('/contact')){

	$title = "Contact Page";
	$router::render('contact',['title'=>$title]);

}

/**
 * handle 404 redirects
 **/ 
if($router->notFound()){

    $router::render('not-found');

}