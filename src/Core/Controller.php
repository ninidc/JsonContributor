<?php

namespace Core;

use Silex\Application;
use Core\Controller\Component;
use Core\Model\SiteConfiguration;
use Handlebars\Handlebars;
use Symfony\Component\Security\Core\SecurityContext;

/**
 *  Base Controller class
 */
abstract class Controller
{
    /**
     * @var \Silex\Application
     */
    protected $app;
    protected $Session;
    protected $Config;

    abstract public function initialize();

    public function __construct(Application $app)
    {

        $this->app      = $app;
        $this->Session  = new Controller\Component\Session();

        //--------------------------------------------------------------//
        //              HANDLEBARS
        //      https://github.com/XaminProject/handlebars.php
        //--------------------------------------------------------------// 
        $this->app["handlebars"] = new Handlebars(array(
            'loader' => new \Handlebars\Loader\FilesystemLoader($this->app['template_folder']),
            'partials_loader' => new \Handlebars\Loader\FilesystemLoader(
                $this->app['template_folder'],
                array(
                    'prefix' => ''
                )
            )
        ));

        $this->app["handlebars"]->addHelper('ifCond', function($template, $context, $args, $source) {

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

        $this->initialize();
    }




    /**
    *   Validate model object
    *   return false if no errors
    */
    public function validate($object)
    {
        // Check validate data
        $errors = $this->app['validator']->validate($object);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                return $error->getMessage()." : ".$error->getPropertyPath();
            }
        }

        return false;
    }


    /**
    *   Render template
    *   $template : template path
    *   $data : Array of data
    */
    public function render($template, $data = array())
    {
        
        $error      = true;
        $request    = $this->app['request'];
        $session    = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $session = array();

        if($error) {
            $session["bad_loggin"]      = true; 
        }

        if(isset($_SESSION["_sf2_attributes"]["_security_default"])) {
            $security                   = unserialize($_SESSION["_sf2_attributes"]["_security_default"]);
            $session["username"]        = $security->getUsername();
            $session["authenticaded"]   = $security->isAuthenticated();
        }
   
        $data = array_merge($data, array(
            "notification"  => $this->Session->getNotification(),
            "session"       => $session
        ));

        return $this->app["handlebars"]->render($template, $data);
    }
}

?>