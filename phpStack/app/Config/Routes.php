<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->match(['get','post'],'/signup', 'Home::signup');
$routes->match(['get','post'],'/login', 'Home::login');
$routes->match(['get','post'], '/dashboard', 'Home::dashboard');
$routes->match(['get','post'], '/logout', 'Home::logout');
$routes->match(['get','post'], '/updateUser', 'Home::updateUser');
$routes->match(['get','post'], '/deleteuser(:any)', 'Home::deleteuser/$1');


