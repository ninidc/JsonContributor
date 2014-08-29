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

class Contributor extends Controller
{

    public function initialize() 
    {
    }
    

    public function data($id_project)
    {
        $Fields = Field::getProjectFields($id_project);

        $data = array(
            "PROJECT" => Project::find($id_project),
            "FIELDS" => $this->displayFieldsTree($Fields, true),
            "OBJECTS" => Object::fetchAll(array(
                "WHERE" => array(
                    "id_project" => $id_project
                )
            )),
        ); 

        return $this->render("project.data", $data);
    }


    public function displayJSONTree($Fields, $data = false, $stage = 0) 
    {
        $JSON = '';
        foreach($Fields as $i => $Field) {

            if(sizeof($Field->childrens) > 0) {

                $JSON .= '"' . $Field->name . '" : {';
                $JSON .= $this->displayJSONTree($Field->childrens, $data, $stage + 1);
                $JSON .= '},';

            } else {

                if($Field->id_type <= 4) {
                    $JSON .= '"' . $Field->name . '" : "' .$Field->value. '",' ; 
                } else {
                    $JSON .= '"' . $Field->name . '" : ' .$Field->value. ',' ; 
                }
            }
            //

            if($i == sizeof($Fields) - 1) {
                $JSON = substr($JSON, 0, -1);
            }
                
        }        
        return $JSON;
    }

    public function getJSON($id_project) 
    {
        $Fields = Field::getProjectFields($id_project);

        $JSON = '{';
        $JSON .= $this->displayJSONTree($Fields);
        $JSON .= '}';

        // TEST JSON 
        $array = json_decode($JSON);

        $data = array(
            "PROJECT"   => Project::find($id_project),
            "JSON"      => json_encode($array, JSON_PRETTY_PRINT)
        );

        return $this->render("project.json", $data);
    }    


    public function definition($id)
    {

        $Fields = Field::getProjectFields($id);

        $data = array(
            "PROJECT" => Project::find($id),
            "FIELDS" => $this->displayFieldsTree($Fields, false),
            "OBJECTS" => Object::fetchAll(array(
                "WHERE" => array(
                    "id_project" => $id
                )
            )),
        ); 

        return $this->render("project.definition", $data);
    }


    public function displayFieldsTree($Fields, $data = false, $stage = 0) 
    {

        $HTML = '<ul>';

        foreach($Fields as $Field) {

            $HTML .= '<li>';

            if($data) {

                $url = $this->app['url_generator']->generate('project.data.field.edit',  array(
                    'id_project' => $Field->id_project,
                    'id_field'  => $Field->id_field
                ));
                $HTML .= '<a href="'.$url.'" class="stage'.$stage.'">' . $Field->name . '</a>';

                $FieldType = FieldType::find($Field->id_type);

                if(!$FieldType->id_object) {
                    $HTML .= ' : ' . $Field->value;
                }

            } else {
                $url = $this->app['url_generator']->generate('project.field.edit',  array(
                    'id_project' => $Field->id_project,
                    'id_field'  => $Field->id_field
                ));
                $HTML .= '<a href="'.$url.'" class="stage'.$stage.'">' . $Field->name . '</a>';
            }

            if(sizeof($Field->childrens) > 0) {
                $HTML .= $this->displayFieldsTree($Field->childrens, $data, $stage + 1);
            }
            $HTML .= '</li>';
            
        }
        $HTML .= '</ul>';

        return $HTML;
    }

}
//--------------------------------------------------------------//
?>