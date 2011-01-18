<?php
//xdebug_start_trace($_SERVER['DOCUMENT_ROOT'].'/trace.txt');
//xdebug_stop_trace();

error_reporting (E_ALL | E_STRICT);

if (empty($_REQUEST['_path']))
{
    $_REQUEST['_path'] = '/';
}

require('./kernel/configuration.php');

$map = array
(
    // Frontend
    array( 'pattern' => '~^/$~',
           'module' => 'index',
           'controller' => 'index'
         ),
    array( 'pattern' => '~^/categories/?$~',
           'module' => 'category',
           'controller' => 'frontend-categories-list',
         ),
    array( 'pattern' => '~^/categories(/[a-z0-9_/\-]+/)$~',
           'module' => 'advert',
           'controller' => 'frontend-category-list',
           'aliases' => array('category_url'),
         ),
    array( 'pattern' => '~^/categories(/[a-z0-9_/\-]+/)([0-9]+)\.xhtml$~',
           'module' => 'advert',
           'controller' => 'view',
           'aliases' => array('category_url', 'id'),
         ),
    array( 'pattern' => '~^/advert/([0-9]+)\.xhtml$~',
           'module' => 'advert',
           'controller' => 'view',
           'aliases' => array('id'),
         ),
    array( 'pattern' => '~^/add\.xhtml$~',
           'module' => 'advert',
           'controller' => 'frontend-add',
         ),

    array( 'pattern' => '~^/my/?$~',
           'module' => 'index',
           'controller' => 'login',
           'is_frontend' => TRUE,
         ),
    array( 'pattern' => '~^/my/adverts/?$~',
           'module' => 'advert',
           'controller' => 'frontend-user-adverts-list',
         ),
    array( 'pattern' => '~^/my/adverts/edit/?(?:([0-9]+)\.xhtml)?$~',
           'module' => 'advert',
           'controller' => 'frontend-edit-advert',
           'aliases' => array('id'),
         ),
    array( 'pattern' => '~^/my/adverts/up/([0-9]+)\.xhtml$~',
           'module' => 'advert',
           'controller' => 'frontend-up-advert',
           'aliases' => array('id'),
         ),
    array( 'pattern' => '~^/my/adverts/active/([0-9]+)\.xhtml$~',
           'module' => 'advert',
           'controller' => 'frontend-active-advert',
           'aliases' => array('id'),
         ),
    array( 'pattern' => '~^/my/adverts/delete/([0-9]+)\.xhtml$~',
           'module' => 'advert',
           'controller' => 'frontend-delete-advert',
           'aliases' => array('id'),
         ),
    array( 'pattern' => '~^/my/info/?$~',
           'module' => 'user',
           'controller' => 'frontend-edit',
         ),
    array( 'pattern' => '~^/registration\.xhtml$~',
           'module' => 'user',
           'controller' => 'frontend-registration',
         ),
    array( 'pattern' => '~^/getpassword/?$~',
           'module' => 'user',
           'controller' => 'frontend-getpassword',
         ),
    array( 'pattern' => '~^/getpassword/([a-z0-9]{32,32})/?$~',
           'module' => 'user',
           'controller' => 'frontend-getpassword-end',
           'aliases' => array('hash'),
         ),


    // Captcha
    array( 'pattern' => '~^/captcha/?~',
           'module' => 'captcha',
           'controller' => 'main',
         ),

    // Backend

    // Index
    array( 'pattern' => '~/admin/?$~',
           'module' => 'index',
           'controller' => 'login'
         ),
    array( 'pattern' => '~/logout/?~',
           'module' => 'index',
           'controller' => 'logout'
         ),

    // Category
    array( 'pattern' => '~^/admin/category/?$~',
           'module' => 'category',
           'controller' => 'backend-main'
         ),
    array( 'pattern' => '~^/admin/category/motion/(up|down)/([0-9]+)/([0-9]+)/?$~',
           'module' => 'category',
           'controller' => 'backend-motion',
           'aliases' => array('tomotion', 'id', 'pid'),
         ),
    array( 'pattern' => '~^/admin/category/edit/([0-9]+)?/?([0-9]+)?/?$~',
           'module' => 'category',
           'controller' => 'backend-edit',
           'aliases' => array('id', 'pid'),
         ),
    array( 'pattern' => '~^/admin/category/delete/?$~',
           'module' => 'category',
           'controller' => 'backend-delete'
         ),

         // User
    array( 'pattern' => '~^/admin/user/?$~',
           'module' => 'user',
           'controller' => 'backend-main',
         ),
    array( 'pattern' => '~^/admin/user/edit/?$~',
           'module' => 'user',
           'controller' => 'backend-edit'
         ),
    array( 'pattern' => '~^/admin/user/delete/?$~',
           'module' => 'user',
           'controller' => 'backend-delete'
         ),

    // Group
    array( 'pattern' => '~^/admin/group/?$~',
           'module' => 'group',
           'controller' => 'backend-main'
         ),
    array( 'pattern' => '~^/admin/group/edit/?$~',
           'module' => 'group',
           'controller' => 'backend-edit'
         ),
    array( 'pattern' => '~^/admin/group/delete/?$~',
           'module' => 'group',
           'controller' => 'backend-delete'
         ),

    // Module
    array( 'pattern' => '~^/admin/module/?$~',
           'module' => 'module',
           'controller' => 'main-module'
         ),
    array( 'pattern' => '~^/admin/module/edit/?$~',
           'module' => 'module',
           'controller' => 'edit-module'
         ),
    array( 'pattern' => '~^/admin/module/delete/?$~',
           'module' => 'module',
           'controller' => 'delete-module'
         ),
    array( 'pattern' => '~^/admin/controller/edit/([0-9]+)/([0-9]+)/?$~',
           'module' => 'module',
           'controller' => 'edit-controller',
           'aliases' => array('id', 'id_module'),
         ),
    array( 'pattern' => '~^/admin/controller/delete/([0-9]+)/([0-9]+)/?$~',
           'module' => 'module',
           'controller' => 'delete-controller',
           'aliases' => array('id', 'id_module'),
         ),
    // Advert
    array( 'pattern' => '~^/admin/advert/?$~',
           'module' => 'advert',
           'controller' => 'backend-main'
         ),
    array( 'pattern' => '~^/admin/advert/edit/?$~',
           'module' => 'advert',
           'controller' => 'backend-edit'
         ),
    array( 'pattern' => '~^/admin/advert/delete/?$~',
           'module' => 'advert',
           'controller' => 'backend-delete'
         ),
);

$application = new Base_Application(Http_Request::getInstance(), Http_Response::getInstance());
$application->setMaps($map)->run();
exit;