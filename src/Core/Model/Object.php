<?php
//--------------------------------------------------------------//
//              Object Model
//--------------------------------------------------------------//
namespace Core\Model;

use Core\Model;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Object extends Model {

    public $id_object;
    public $name;
    public $id_project;

    public static $table = "objects";
    public static $index = "id_object";

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