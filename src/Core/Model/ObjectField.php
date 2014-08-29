<?php
//--------------------------------------------------------------//
//              Field Model
//--------------------------------------------------------------//
namespace Core\Model;

use Core\Model;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class ObjectField extends Model {

    public $id_object_field;
    public $name;
    public $id_object;
    public $id_type;
    public $parent;
    public $ord;
    public $childrens;

    public static $table = "objects_fields";
    public static $index = "id_object_field";

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
        //$metadata->addPropertyConstraint('id_type', new Assert\NotBlank());
    }


    public function beforeSave()
    {
        unset($this->childrens);
    }


    static public function getFields($id_object) {

        $Fields = ObjectField::fetchAll(array(
            "WHERE" => array(
                "id_object"    => $id_object
            ),
            "ORDER" => "parent ASC"
        ));

        foreach($Fields as $F) {    // Father
            foreach($Fields as $i=>$C) {    // Children
                if($F->id_object_field == $C->parent) {
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