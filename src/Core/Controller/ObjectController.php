<?php
//--------------------------------------------------------------//
//              Contributor controller
//--------------------------------------------------------------//
namespace Core\Controller;

use Core\Controller;
use Symfony\Component\HttpFoundation\Request;

use Core\Model\Project;
use Core\Model\FieldType;
use Core\Model\Field;
use Core\Model\Object;
use Core\Model\ObjectField;

class ObjectController extends Controller
{

    public function initialize() 
    {
    }
    
    public function newObject($id_project) 
    {

        $data = array(
            "PROJECT" =>  Project::find($id_project)
        );

        return $this->render("object.new", $data);
    }


    public function edit($id_project, $id_object) {

        $Fields = ObjectField::getFields($id_object);

        $data = array(
            "PROJECT"   =>  Project::find($id_project),
            "OBJECT"    =>  Object::find($id_object),
            "FIELDS"    =>  $this->displayFieldsTree($Fields)
        );

        return $this->render("object.fields.edit", $data);
    }

    public function displayFieldsTree($Fields, $stage = 0) {

        $HTML = '<ul>';

        foreach($Fields as $Field) {

            /*
            $url = $this->app['url_generator']->generate('project.field.edit',  array(
                'id_project' => $Field->id_project,
                'id_field'  => $Field->id_field
            ));
            */
            $url = null;

            $HTML .= '<li>';
            $HTML .= '<a href="'.$url.'" class="stage'.$stage.'">' . $Field->name . '</a>';
            if(sizeof($Field->childrens) > 0) {
                $HTML .= $this->displayFieldsTree($Field->childrens, $stage + 1);
            }
            $HTML .= '</li>';
            
        }
        $HTML .= '</ul>';

        return $HTML;
    }

    public function save($id_project, Request $request) 
    {

        $Object = new Object(array(
            "name"          => $request->get('name'),
            "id_project"    => $request->get('id_project')
        ));

        $error = $this->validate($Object);       

        if(!$error) {
            try {
                if($Object->save()) {

                    $url = $this->app['url_generator']->generate('project.object.edit',  array(
                        'id_project'    => $id_project,
                        'id_object'     => $Object->id_object
                    ));

                    return $this->app->redirect($url);
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } 

        return $this->render("object.new", array(
            "ERROR" => $error
        ));
    }

}
//--------------------------------------------------------------//
?>