<?php
use Src\Amanda\Router;
// use Src\Amanda\QueryBuilder;

$router = new Router();

// Middleware example
$router->useMiddleware(function ($request, $response, $next) {
    if (!isset($_SESSION['user'])) {
        echo "Access denied! Please log in.";
        return;
    }
    $next($request, $response);
});

// GET request: Home Page
$router->get('/home', function () {
    $vars = [
        'title' => 'Home Page',
        'content' => 'Welcome to the School Management System!'
    ];
    Router::render('home', $vars);
}, 'home.page');

// POST request: Login Form Submission
$router->post('/login', function () {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'secret') {
        echo "Login successful!";
    } else {
        echo "Invalid credentials!";
    }
}, 'login.submit');

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
        echo "Displaying profile for student ID $id";
    } else {
        echo "Student ID not provided!";
    }
}, 'student.profile');

// Grouped routes: Admin section
$router->group('/admin', function ($router) {
    $router->get('/dashboard', function () {
        echo "Admin Dashboard";
    }, 'admin.dashboard');

    $router->post('/settings', function () {
        echo "Updating admin settings";
    }, 'admin.settings');
});

// Rate Limiting: Login Attempts
$router->rateLimit('/login', [
    'limit' => 5,
    'interval' => 60, // 60 seconds
]);

$router->post('/login', function () {
    echo "Processing login...";
}, 'login.rateLimited');

// Custom 404 Error Handler
$router->setErrorHandler(404, function () {
    echo "Page not found!";
});

// Named Routes: Generate URLs
$dashboardUrl = $router->generate('admin.dashboard');
echo "Visit your dashboard at $dashboardUrl";

// Process the current request
$router->run();