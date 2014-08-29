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

class ObjectFieldController extends Controller
{

    public function initialize() 
    {
    }



    public function newField($id_project, $id_object)
    {

        $data = array(
            "PROJECT"       => Project::find($id_project),
            "OBJECT"        => Object::find($id_object),
            "FIELDS_TYPE"   => FieldType::fetchAll(),
            "FIELDS"        => ObjectField::fetchAll(array(
                "WHERE" => array(
                    "id_object" => $id_object
                )
            ))
        );

        return $this->render("object.field.new", $data);
    }
   


   public function edit($id_project, $id_object, $id_field) 
   {
        
        $data =  array(
            "PROJECT"       => Project::find($id_project),
            "OBJECT"        => Object::find($id_object),
            "FIELD"         => ObjectField::find($id_field),
            "FIELDS_TYPE"   => FieldType::fetchAll(),
            "FIELDS"        => ObjectField::fetchAll(array(
                "WHERE" => array(
                    "id_object" => $id_object
                )
            ))
        );

        return $this->render("object.field.new",$data);
    }



    public function createForm($Fields, $data = array())
    {
        $HTML = '<form action="?" method="POST" role="form" />';

        foreach($Fields as $F) {

            $FieldType = FieldType::find($F->id_type);

            switch($FieldType->name) {

                
                
                case "int":
                    $value = null;

                    if(isset($data[$F->name])) {
                        $value = $data[$F->name];
                    }

                    $HTML .= '
                        <div class="form-group">
                            <label>'.$F->name.'</label>
                            <input type="text" name="'.$F->name.'" class="form-control" placeholder="Enter '.$F->name.'" value="'.$value.'" />
                        </div>
                    ';
                break;

                case "string":

                    $value = null;

                    if(isset($data[$F->name])) {
                        $value = $data[$F->name];
                    }

                    $HTML .= '
                        <div class="form-group">
                            <label>'.$F->name.'</label>
                            <textarea name="'.$F->name.'" class="form-control" >'.$value.'</textarea>
                        </div>
                    ';
                break;

                default:

                    $value = null;

                    if(isset($data[$F->name])) {
                        $value = $data[$F->name];

                        if(is_array($value)) {
                            $value = json_encode($value);
                        }
                    }

                    $HTML .= '
                    <div class="form-group">
                        <label>'.$F->name.'</label>
                        <textarea name="'.$F->name.'" class="form-control">'.$value.'</textarea>
                    </div>';
                break;

            }
        }
        $HTML .= '<input type="submit" value="Save" class="btn btn-primary" />';
        $HTML .= '</form>';

        return $HTML;
    }


    public function removeData($id_project, $id_field, $index)
    {
        $Field          = Field::find($id_field);
        $data           = json_decode($Field->value, true);
        unset($data[$index]);
        $Field->value = json_encode($data, JSON_PRETTY_PRINT);

        if($Field->save()) {
            $url = $this->app['url_generator']->generate('project.data.field.edit',  array(
                'id_project'   => $Field->id_project,
                'id_field'     => $Field->id_field
            ));

            return $this->app->redirect($url);
        }
    }


    public function editData($id_project, $id_field, $index)
    {
        $Field          = Field::find($id_field);
        $FieldType      = FieldType::find($Field->id_type);
        $ObjectFields   = ObjectField::fetchAll(array(
            "WHERE" => array(
                "id_object" => $FieldType->id_object
            )
        ));

        $FORM = $this->createForm($ObjectFields, json_decode($Field->value, true)[$index]);

        return $this->render("object.field.data.edit", array(
            "FORM" => $FORM
        ));
    }


    public function addData($id_project, $id_field)
    {
        $Field          = Field::find($id_field);
        $FieldType      = FieldType::find($Field->id_type);
        $ObjectFields   = ObjectField::fetchAll(array(
            "WHERE" => array(
                "id_object" => $FieldType->id_object
            )
        ));

        $FORM = $this->createForm($ObjectFields);

        return $this->render("object.field.data.edit", array(
            "FORM" => $FORM
        ));
    }


     public function createData($id_project, $id_field, Request $request)
    {
        $Field          = Field::find($id_field);
        $FieldType      = FieldType::find($Field->id_type);
        $ObjectFields   = ObjectField::fetchAll(array(
            "WHERE" => array(
                "id_object" => $FieldType->id_object
            )
        ));

        $data = array();
        foreach($ObjectFields as $F) {
            $data[$F->name] = $request->get($F->name);
        }

        $FieldData  = json_decode($Field->value, true);
        $FieldData[] = $data;

        $Field->value = json_encode($FieldData, JSON_PRETTY_PRINT);

        if($Field->save()) {
            $url = $this->app['url_generator']->generate('project.data.field.edit',  array(
                'id_project'   => $Field->id_project,
                'id_field'     => $Field->id_field
            ));

            return $this->app->redirect($url);
        }

    }


    public function saveData($id_project, $id_field, $index, Request $request)
    {
        $Field          = Field::find($id_field);
        $FieldType      = FieldType::find($Field->id_type);
        $ObjectFields   = ObjectField::fetchAll(array(
            "WHERE" => array(
                "id_object" => $FieldType->id_object
            )
        ));

        $data = array();
        foreach($ObjectFields as $F) {
            $data[$F->name] = $request->get($F->name);
        }

        $FieldData  = json_decode($Field->value, true);
        $FieldData[$index] = $data;

        $Field->value = json_encode($FieldData, JSON_PRETTY_PRINT);

        if($Field->save()) {
            $url = $this->app['url_generator']->generate('project.data.field.edit',  array(
                'id_project'   => $Field->id_project,
                'id_field'     => $Field->id_field
            ));

            return $this->app->redirect($url);
        }
    }

    public function save($id_project, $id_object, Request $request) 
    {

        $Field = new ObjectField(array(
            "id_field"      => $request->get('id_field'),
            "name"          => $request->get('name'),
            "id_type"       => $request->get('type'),
            "parent"        => $request->get('parent'),
            "id_object"     => $request->get('id_object')
        ));

        $error = $this->validate($Field);       

        if(!$error) {
            try {
                if($Field->save()) {
                    
                    $url = $this->app['url_generator']->generate('project.object.edit',  array(
                        'id_project'    => $id_project,
                        'id_object'     => $id_object
                    ));

                    return $this->app->redirect($url);
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } 

        echo $error;

        return $this->render("object.field.new", array(
            "ERROR"         => $error,
            "PROJECT"       => Project::find($id_project),
            "OBJECT"        => Object::find($id_object),
            "FIELD"         => $Field,
            "FIELDS_TYPE"   => FieldType::fetchAll(),
            "FIELDS"        => ObjectField::fetchAll(array(
                "WHERE" => array(
                    "id_object" => $id_object
                )
            ))
        ));

    }

    
}
//--------------------------------------------------------------//
?>