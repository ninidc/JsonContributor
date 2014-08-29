<?php
//--------------------------------------------------------------//
//              Project Model
//--------------------------------------------------------------//
namespace Core\Model;

use Core\Model;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class FieldType extends Model {

    public $id_type;
    public $name;
    public $id_object;

    public static $table = "fields_type";
    public static $index = "id_type";

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
    }
}
//--------------------------------------------------------------//

?>