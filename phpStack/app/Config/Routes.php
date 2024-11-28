<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->match(['GET','POST'],'/signup', 'Home::signup');
$routes->match(['GET','POST'],'/login', 'Home::login');
$routes->match(['GET','POST'], '/dashboard', 'Home::dashboard');
$routes->match(['GET','POST'], '/logout', 'Home::logout');
$routes->match(['GET','POST'], '/updateUser', 'Home::updateUser');
$routes->match(['GET','POST'], '/deleteuser(:any)', 'Home::deleteuser/$1');
$routes->match(['GET','POST'], '/uploadFile', 'Home::uploadFile');


