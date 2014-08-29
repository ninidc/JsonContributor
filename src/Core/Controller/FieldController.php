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

class FieldController extends Controller
{

    public function initialize() 
    {
    }

    public function newField($id_project)
    {
    	

    	$data = array(
    		"PROJECT" 		=> Project::find($id_project),
    		"FIELDS_TYPE" 	=> FieldType::fetchAll(),
    		"FIELDS" 		=> Field::fetchAll(array(
    			"WHERE"	=> array(
    				"id_project" => $id_project
    			)
    		))
    	);

    	return $this->render("field.new", $data);
    }


    public function save($id_project, Request $request) 
    {

    	$Field = new Field(array(
            "id_field"             => $request->get('id_field'),
            "name" 			=> $request->get('name'),
            "id_type" 		=> $request->get('type'),
            "parent" 		=> $request->get('parent'),
            "id_project" 	=> $request->get('id_project')
        ));

        $error = $this->validate($Field);       

        if(!$error) {
            try {
                if($Field->save()) {
                    
                    $url = $this->app['url_generator']->generate('project.definition.index',  array(
                    	'id' => $id_project
                    ));

                    return $this->app->redirect($url);
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } 

    	return $this->render("field.new", array(
    		"ERROR" => $error
    	));
    }

    // Retourne la liste HTML des catégories
    
    public function displayFieldsTree($Fields, $stage = 0) {

        $HTML = '<ul>';

        foreach($Fields as $Field) {

            $url = $this->app['url_generator']->generate('project.field.edit',  array(
                'id_project' => $Field->id_project,
                'id_field'  => $Field->id_field
            ));

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


    public function edit($id_project, $id_field) {
        
        $data =  array(
            "PROJECT"       => Project::find($id_project),
            "FIELD"         => Field::find($id_field),
            "FIELDS_TYPE"   => FieldType::fetchAll(),
            "FIELDS"        => Field::fetchAll(array(
                "WHERE" => array(
                    "id_project" => $id_project
                )
            ))
        );

        return $this->render("field.new",$data);
    }


    public function createTable($fields, $data) 
    {
        $HTML = '<table class="table">';

        $HTML .= '<tr>';
        $n = 2;
        foreach($fields as $field) {
            $HTML .= '<th>'.$field->name.'</th>';
            $n++;
        }
        $HTML .= '<th></th>';
        $HTML .= '<th></th>';
        $HTML .= '</tr>';

        foreach($data as $index => $row) {

            $HTML .= '<tr>';
            foreach($row as $cell) {
                if(!is_array($cell)) {
                    if(strlen($cell) > 50) {
                        $HTML .= '<td width="'.(100 / $n).'">'.substr($cell, 0, 50).'...</td>';
                    } else {
                        $HTML .= '<td width="'.(100 / $n).'">'.$cell.'</td>';
                    }
                    
                } else {
                    $HTML .= '<td>Voir</td>';
                }
            }

            $HTML .= '<td><a href="/project/1/data/12/index/'.$index.'/edit" class="btn btn-default">Edit</a></td>';
            $HTML .= '<td><a href="/project/1/data/12/index/'.$index.'/delete" class="btn btn-danger" onclick="return confirm(\'Etes-vous sur de vouloir supprimer cet élément ?\');">Delete</a></td>';
            $HTML .= '</tr>';

        }

        $HTML .= '</table>';

        return $HTML;
    }


    public function editData($id_project, $id_field)
    {
        $Field      = Field::find($id_field);
        $FieldType  = FieldType::find($Field->id_type);
        $table      = false;
        $fields     = null;

        if($FieldType->id_object) {

            $fields = ObjectField::fetchAll(array(
                "WHERE" => array(
                    "id_object" =>  $FieldType->id_object
                )
            ));

            $table = $this->createTable($fields, json_decode($Field->value, true));
        }

        $data =  array(
            "PROJECT"       => Project::find($id_project),
            "FIELD"         => $Field,
            "TABLE"         => $table
        );

        return $this->render("field.data.edit",$data);
    }




    public function saveData($id_project, $id_field, Request $request) 
    {

        $Field = Field::find($id_field);
        $Field->value = $request->get('value');

        if($Field->save()) {
            
            $url = $this->app['url_generator']->generate('project.data.index',  array(
                'id_project' => $id_project
            ));

            return $this->app->redirect($url);
        }
            
        return $this->render("field.new", array(
            "ERROR" => $error
        ));
    }
    
}
//--------------------------------------------------------------//
?>