<?php
require "bootstrap.inc.php";

use Src\Amanda\Router;
// use Src\Amanda\QueryBuilder;

$router = new Router();

$router->get('/', function(){
    $vars = [
        'title' => 'Home Page',
        'content' => 'Welcome to the School Management System!'
    ];
    Router::render('index', $vars);
}, 'home.index');

$router->get('/about', function(){
    $vars = [
        'title' => 'About Page',
        'content' => 'Welcome to the School Management System!'
    ];
    Router::render('about', $vars);
}, 'home.about');

$router->get('/contact', function(){
    $vars = [
        'title' => 'Contact Page',
        'content' => 'Welcome to the School Management System!'
    ];
    Router::render('contact', $vars);
}, 'home.about');

$router->post('/form/submit', function() {
    echo input('name')."<br>";
	echo input('message');
});

// $router->use('web', $router);

// PUT request: Update Student Details
$router->put('/student/{id}', function ($params) {
    $id = $params['id'] ?? null;
    $name = $_POST['name'] ?? '';

    if ($id) {
        echo "Updating student ID $id with name $name";
    } else {
        echo "Student ID not provided!";
    }
}, 'student.update');

// PATCH request: Partially Update Student Details
$router->patch('/student/{id}', function ($params) {
    $id = $params['id'] ?? null;
    $field = $_POST['field'] ?? '';
    $value = $_POST['value'] ?? '';

    if ($id) {
        echo "Updating student ID $id: setting $field to $value";
    } else {
        echo "Student ID not provided!";
    }
}, 'student.patch');

// DELETE request: Delete Student
$router->delete('/student/{id}', function ($params) {
    $id = $params['id'] ?? null;

    if ($id) {
        echo "Deleting student with ID $id";
    } else {
        echo "Student ID not provided!";
    }
}, 'student.delete');

// GET request with dynamic parameters: View Student Profile
$router->get('/student/{id}', function ($params) {
    $id = $params['id'] ?? null;

    if ($id) {
        echo "Displaying profile for student ID $id".input('page');
    } else {
        echo "Student ID not provided!";
    }
}, 'student.profile');

$router->get('/student/{id}/{subject}', function ($params) {
    $id = $params['id'] ?? null;
    $subject = $params['subject'] ?? null;
    if ($id) {
        echo "Displaying profile for student ID $id and subject $subject ";
    } else {
        echo "Student ID not provided!";
    }
}, 'student.profile');

// Grouped routes: Admin section
$router->group('/admin', function ($router) {
    $router->get('/settings', function () {
        echo "Admin Settings Page";
    });
});

// GET request: Home Page
$router->get('/home', function () {
    $vars = [
        'title' => 'Home Page',
        'content' => 'Welcome to the School Management System!'
    ];
    Router::render('index', $vars);
}, 'home.page');

// POST request: Login Form Submission
$router->post('/login', function () {
    $username = input('username') ?? '';
    $password = input('password') ?? '';

    if ($username === 'admin' && $password === 'secret') {
        echo "Login successful!";
    } else {
        echo "Invalid credentials!";
    }
}, 'login.submit');

// Rate Limiting: Login Attempts
$router->rateLimit('/login', 5, 60);

$router->post('/login', function () {
    echo "Processing login...";
}, 'login.rateLimited');

// Custom 404 Error Handler
$router->setErrorHandler(404, function () {
    echo "Page not found!";
});

// $router->debugRoutes(); // Debug routes

// Process the current request
$router->run();