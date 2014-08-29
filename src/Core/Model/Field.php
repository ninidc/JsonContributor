<?php
//--------------------------------------------------------------//
//              Field Model
//--------------------------------------------------------------//
namespace Core\Model;

use Core\Model;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Field extends Model {

    public $id_field;
    public $name;
    public $id_type;
    public $parent;
    public $ord;
    public $value;
    public $id_project;
    public $childrens;

    public static $table = "fields";
    public static $index = "id_field";

  	public function __construct($data = array()) 
    {
        if(!empty($data))
        {
            $this->fromArray($data);
        }
    }

    /*
    *   Model validation
    */
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('id_type', new Assert\NotBlank());
    }


    public function beforeSave()
    {
        unset($this->childrens);
    }


    static public function getProjectFields($id_project) {

        $Fields = Field::fetchAll(array(
            "WHERE" => array(
                "id_project"    => $id_project
            ),
            "ORDER" => "parent ASC"
        ));

        foreach($Fields as $F) {    // Father
            foreach($Fields as $i=>$C) {    // Children
                if($F->id_field == $C->parent) {
                    $F->childrens[] = $C;
                    unset($Fields[$i]);
                }
            }
        }

        return $Fields;
    }
}
//--------------------------------------------------------------//

?>