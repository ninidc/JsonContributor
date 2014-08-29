<?php
//--------------------------------------------------------------//
//              Frontend controller
//--------------------------------------------------------------//
namespace Core\Controller;

use Core\Controller;
use Symfony\Component\HttpFoundation\Request;

use Core\Model\Project;

class Frontend extends Controller
{

    public function initialize() 
    {
    }


    public function index()
    {
    	return $this->render("index", array(
            "PROJECTS" => Project::fetchAll()
        ));
    }


    public function newProject()
    {
    	return $this->render("new");
    }

    public function saveProject(Request $request)
    {

    	$Project = new Project(array(
            "name" => $request->get('name'),
        ));

        $error = $this->validate($Project);       

        if(!$error) {
            try {
                if($Project->save()) {
                    
                    $url = $this->app['url_generator']->generate('project.definition.index',  array(
                    	'id' => $Project->id_project
                    ));

                    return $this->app->redirect($url);
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } 

    	return $this->render("new", array(
    		"ERROR" => $error
    	));
    }

}
//--------------------------------------------------------------//
?>