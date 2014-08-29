<?php
//--------------------------------------------------------------//
//              Account Model
//--------------------------------------------------------------//
namespace Core;

class Model
{
    
    protected static $table;
    protected static $index;

	/*
    *   Magic getters
    */
	public function __get($name) 
    {
        if(isset($this->$name)) 
        {
            return $this->$name;
        }
        
        return false;
    }

    /*
    *   Magic setters
    */
    public function __set($name, $value)
    {
    	if(isset($this->$name)) 
        {
        	$this->$name = $value;
        }
    }


    /*
    *   Setters
    */
    public function setId($value) {
        $this->id = $value;
    }

    public function getId() {
        return $this->id;
    }


    /*
    *   Get array representation of the object
    */
    public function toArray($attrList = array())
    {
        $attr       = get_object_vars($this);
        $attrArray  = array();

        foreach($attr as $key=>$value) 
        {
            if(!empty($attrList))
            {
                if(in_array($key, $attrList)) 
                {
                    $attrArray[$key] = $value;
                }
            } 
            else 
            {
                $attrArray[$key] = $value;
            }
        }

        return $attrArray;
    }


    /*
    *   Set attributs from array
    */
    public function fromArray(array $data)
    {

        if(empty($data))
            return false;

        foreach($data as $name=>$value) 
        {
            if(property_exists($this, $name))
                $this->$name = $value;

        }
        
        return $this;
    }


    /*
    *   Serialize object
    */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /*
    *   CallBack
    */
    public function beforeSave(){}
    public function afterSave(){}

    public function beforeCreate(){}
    public function afterCreate(){}
    
    public function beforeUpdate(){}
    public function afterUpdate(){}
 
 
    /*
    *   Saving model into the database
    */  
    public function save() 
    {

        $this->beforeSave();

        $className  = get_called_class();
        $indexName  = $className::$index;

        if(!$this->$indexName) // INSERT
        {  
            $this->beforeCreate();

            $data = $this->toArray();
            unset($data[$indexName]);

            $this->$indexName = $className::create($data);

            if($this->$indexName) {
                $this->afterCreate();
                $this->afterSave();
            }
            
            return $this;
        } 
        else // UPDATE
        {
            $this->beforeUpdate();

            $data = $this->toArray();
            unset($data[$indexName]);

            $className::update($this->$indexName, $data);
            
            $obj = $className::find($this->$indexName);

            if($obj->$indexName) {
                $this->afterUpdate();
                $this->afterSave();
            }

            return $obj;
        }
    }


    /*
    *   Retrive model by ID
    */
    public static function find($id) 
    {

        global $app;

        $className = get_called_class();

        $stmt = $app['db']->prepare('
            SELECT 
                *
            FROM 
                ' . $className::$table . '
            WHERE 
                ' . $className::$index . ' = ?
        ');
		
        $stmt->bindValue(1, $id);
        $stmt->execute();
		
        $data = $stmt->fetch();

        $obj = new $className($data);
        $obj->id = $data[ $className::$index ];

        return $obj;
    }

    /*
    *   Count
    */
    public static function count($options = array())
    {
        global $app;
        
        $className  = get_called_class();

        $sql = '
            SELECT 
                COUNT(*) AS NB
            FROM 
                ' . $className::$table . '
            WHERE 1
        ';

        if(isset($options["WHERE"])) {
            foreach($options["WHERE"] as $key=>$value) {
                $sql .=  ' AND ' . $key.' = "' . $value . '"';
            }
        }

        return $app['db']->fetchColumn($sql);
        
    }



    /*
    *   Retrieves all model
    */
    public static function fetchAll($options = array())
    {

        // FIXME : WHERE : conditions telles que >, <, = et LIKE ... ?
        /*
            $options = array(
                "WHERE" => array(
                    "id" => 1,
                    "type" => 2
                )
            )
            
        */
        global $app;
        
        $className  = get_called_class();

        // FIXME : utiliser le QueryBuilder de Doctrine
        $sql = '
            SELECT 
                * 
            FROM 
                ' . $className::$table . '
            WHERE 1
        ';
		

        if(isset($options["WHERE"])) {
            foreach($options["WHERE"] as $key=>$value) {
                $sql .=  ' AND ' . $key.' = "' . $value . '"';
            }
        }

        if(isset($options["ORDER"])) {
            $sql .=  ' ORDER BY ' . $options["ORDER"];
        }

        if(isset($options["LIMIT"])) {
            $sql .=  ' LIMIT ' . $options["LIMIT"];
        }
        
        $result = $app['db']->fetchAll($sql);
        $data = array();

        foreach($result as $r) {
            $data[] = new $className($r);
        }

		
        return $data;
        
    }

    


    //--------------------------------------------------------------//
    //                      BASIC CRUD
    //  http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html
    //--------------------------------------------------------------//
    
    public static function create($data) 
    {
        global $app;

        $className = get_called_class();

        $app['db']->insert($className::$table, $data);

        return $app['db']->lastInsertId();
    }

    
    public static function read($id) 
    {
        $className = get_called_class();

        return $className::find($id);
    }


    public static function update($id, $data) 
    {
        global $app;

        $className = get_called_class();

        $app['db']->update($className::$table, $data, array($className::$index => $id));
    }


    public static function delete($id) 
    {
        global $app;

        $className = get_called_class();

        $app['db']->delete($className::$table, array( $className::$index => $id));
    }

    //--------------------------------------------------------------//


    static public function slugify($text)
    { 
      // replace non letter or digits by -
      $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

      // trim
      $text = trim($text, '-');

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // lowercase
      $text = strtolower($text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      if (empty($text))
      {
        return 'n-a';
      }

      return $text;
    }

}
//--------------------------------------------------------------//
?>