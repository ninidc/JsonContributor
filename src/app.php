<?php
//--------------------------------------------------------------//
//              JSON CONTRIBUTOR :)
//
//      Author : Nicolas DEL CASTILLO
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              DEPENDENCIES
//--------------------------------------------------------------//
use Silex\Application;
use Core\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File;
use Handlebars\Handlebars;
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              SILEX APP
//--------------------------------------------------------------//
$app = new Application();
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              CONFIG
//--------------------------------------------------------------//
date_default_timezone_set("Europe/Paris"); 

$app['debug'] = true; // Debug mode

// DB
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array (
        'site' => array(
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'dbname'   => 'jsoncontributor',
            'user'     => 'root',
            'password' => 'spud83',
            'charset'   => 'utf8',
            'driverOptions' => array(
                    1002 =>'SET NAMES utf8'
            )
        )
    ),
));

$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\RememberMeServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'default' => array(
            'pattern' => '^/play/',
            'form' => array(
                'login_path' => '/inscription', 
                'check_path' => '/play/signin'
            ),
            'logout' => array(
                'logout_path' => '/play/logout'
            ),
            'users' => $app->share(function() use ($app) {
                return new Core\Model\UserProvider($app['db']);
            }),
        ),
    ),
    'security.access_rules' => array(
        array('^/play$', 'USER'),
    )
));

$app['upload_folder']       = __DIR__ . '/../web/uploads';
$app['template_folder']     = __DIR__ . '/Templates/';
$app['domain']              = 'coudy';

$app->boot();
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              HANDLEBARS
//      https://github.com/XaminProject/handlebars.php
//--------------------------------------------------------------// 
// FIXME : rendre ça plus propre
$app["handlebars"] = new Handlebars(array(
    'loader' => new \Handlebars\Loader\FilesystemLoader($app['template_folder']),
    'partials_loader' => new \Handlebars\Loader\FilesystemLoader(
        $app['template_folder'],
        array(
            'prefix' => ''
        )
    )
));

// IfCond Helper
$app["handlebars"]->addHelper('ifCond', function($template, $context, $args, $source) {

    $args = explode(' ', $args);

    $a = $context->get($args[0]);
    $b = $context->get($args[2]);

    if(!$b) {
        $b = $args[2];
    }

    $operator = $args[1];

    $template->setStopToken('else');

    switch($operator) {
        case '==':
            if($a == $b) {
                $buffer = $template->render($context);
                $template->setStopToken(false);
                $template->discard($context);
            } else {
                $template->discard($context);
                $template->setStopToken(false);
                $buffer = $template->render($context);
            }
        break;

        case '!=':
            if($a != $b) {
                $buffer = $template->render($context);
                $template->setStopToken(false);
                $template->discard($context);
            } else {
                $template->discard($context);
                $template->setStopToken(false);
                $buffer = $template->render($context);
            }
        break;
    }

    return $buffer;
});
//--------------------------------------------------------------//




//--------------------------------------------------------------//
//              LOADING ROUTES AND INIT THEM
//--------------------------------------------------------------//
require("Config/routes.php");

$Dispatcher = new Core\Dispatcher($app);

$Dispatcher->setRoutes($routes);
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              Accepting a JSON Request Body
// (http://silex.sensiolabs.org/doc/cookbook/json_request_body.html)
//--------------------------------------------------------------//
$app->before(function(Request $request) {
    if(0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              ERRORS
//--------------------------------------------------------------//
/*
$app->error(function (\Exception $e, $code) {

    global $app;

    switch ($code) {
        case 404:
            //return $app["handlebars"]->render("/Front/404");
        break;
    }
});
*/
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              RETURN APP...
//--------------------------------------------------------------//
return $app;
//--------------------------------------------------------------//

?>