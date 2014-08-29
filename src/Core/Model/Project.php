<?php
//--------------------------------------------------------------//
//              Project Model
//--------------------------------------------------------------//
namespace Core\Model;

use Core\Model;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Project extends Model {

    public $id_project;
    public $name;

    public static $table = "projects";
    public static $index = "id_project";

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