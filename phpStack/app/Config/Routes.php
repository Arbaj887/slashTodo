<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->match(['GET','POST'],'/signup', 'Signup::signup');
$routes->match(['GET','POST'],'/login', 'Login::login');
$routes->match(['GET','POST'], '/dashboard', 'Dashboard::dashboard');
$routes->match(['GET','POST'], '/logout', 'Logout::logout');
$routes->match(['GET','POST'], '/updateUser', 'UpdateUser::updateUser');
$routes->match(['GET','POST'], '/deleteuser(:any)', 'DeleteUser::deleteuser/$1');
$routes->match(['GET','POST'], '/uploadFile', 'Uploadfile::uploadFile');


