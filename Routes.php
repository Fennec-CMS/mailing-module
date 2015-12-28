<?php
use Fennec\Library\Router;

$routes = array(
    array(
        'name' => 'mailing-register',
        'route' => '/mailing/register/',
        'module' => 'Mailing',
        'controller' => 'Index',
        'action' => 'register',
        'layout' => null
    ),
    array(
        'name' => 'admin-mailing',
        'route' => '/admin/mailing/',
        'module' => 'Mailing',
        'controller' => 'Admin\\Index',
        'action' => 'index',
        'layout' => 'Admin/Default'
    ),
    array(
        'name' => 'admin-mailing-register',
        'route' => '/admin/mailing/register/',
        'module' => 'Mailing',
        'controller' => 'Admin\\Index',
        'action' => 'form',
        'layout' => 'Admin/Default'
    ),
    array(
        'name' => 'admin-contact-delete',
        'route' => '/admin/mailing/delete/([0-9]+)/',
        'params' => array(
            'id'
        ),
        'module' => 'Mailing',
        'controller' => 'Admin\\Index',
        'action' => 'delete',
        'layout' => null
    )
);

foreach ($routes as $route) {
    Router::addRoute($route);
}
