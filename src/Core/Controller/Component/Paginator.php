<?php
//--------------------------------------------------------------//
//              Paginator components
//--------------------------------------------------------------//
namespace Core\Controller\Component;

class Paginator {
    
    public $base_url        = '';
    public $url_params      = '';
    public $param_name      = 'page';
    public $url_type        = 'default';
    public $page_current    = 1;
    public $page_step       = 10;
    public $total_rows      = 0;
    public $per_page        = 10;
    public $page_array      = array();
    public $num_pages       = 0;
    public $limit           = 10;

    /**
	 * Constructeur
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
    public function __construct($params = array()) 
    {
    
        if (count($params) > 0) {
			$this->initialize($params);
		}

        // Paramètre dans l'url
        if($this->url_params != '') {
            $this->url_params = '?' . $this->url_params;
        }

		if ($this->total_rows == 0 || $this->per_page == 0) {
			return;
		}

		$this->num_pages = ceil($this->total_rows / $this->per_page);

        if($this->num_pages == 1) {
            return;
        }

		// NEXT        
        $this->page_array["next"] = array(
            "value" => $this->getNext(),
            "url"   => $this->getPageUrl($this->getNext())
        );
        
        
        // PREVIOUS
        $this->page_array["prev"] = array(
            "value" => $this->getPrevious(),
            "url"   => $this->getPageUrl($this->getPrevious())
        );

        // FIRST
        $this->page_array["first"] = array(
            "value" => 1,
            "url"   => $this->getPageUrl(1)
        );
        
        // LAST
        $this->page_array["last"] = array(
            "value" => $this->num_pages,
            "url"   => $this->getPageUrl($this->num_pages)
        );

		// PAGES
        $this->page_array["pages"] = $this->getPages();

    }
    
    
    
    /*
    *   Retourne l'url de la page correspondante
    *
    *   @access	public
    *   @return	string || false
    *
    */
    public function getPageUrl($page_number) 
    {        
        
        switch($this->url_type) {
        
            // URL de base + parametre (ex: ?page=1)
            case "param":
                if($page_number != null && $page_number != '') {

                    $url = $this->base_url . "?" . $this->param_name . "=" . $page_number;
                    
                    // Ajout des parametres de l'URL
                    if($this->url_params) {
                    
                        $params = preg_replace("/" . $this->param_name . "=([0-9]+)/i","",substr($this->url_params, 1));
                        
                        if($params != "") {
                            if(substr($params,0,1) != "&") {
                                $params = "&" . $params;
                            }
                            $url .= $params;
                        }
                    }
                
                    return $url;
                }
            break;
        
            // Par défaut, l'URL à la forme : URL + /page/1/
            default:
            case "default":
                if($page_number != null && $page_number != '') {
                    
                    return $this->base_url . $this->param_name . "/" . $page_number . "/" . $this->url_params;
                }
            break;
        }

        return false;
    }
    
    
    /**
	 * Calcul et renvoi un tableau des pages
	 *
	 * @access	public
	 * @return	array
	 */
    public function getPages() 
    {
    
        $page_array = array();
    
        $start  = $this->page_current - ceil(($this->limit / 2));
        $end    = $this->page_current + floor(($this->limit / 2));

        if($end > $this->num_pages) {
            $start = $this->num_pages - $this->limit;
            $end = $this->num_pages;
        }
        
        if($start < 0) {
            $start = 0;
            $end = $start + $this->limit;
        }
        
        if($end > $this->num_pages) {
            $end = $this->num_pages;
        }
        
        for($i=$start; $i < $end; $i++) {
            $page_number = $i + 1;
        
            $page_array[] = array(
                "label"     => $page_number,
                "value"     => $page_number,
                "url"       => $this->getPageUrl($page_number)
            );
            
            if($page_number == $this->page_current) {
                $page_array[sizeof($page_array) - 1]["current"] = true;
            } 
        }        
        return $page_array;
    }
    
    
    
    /**
	 * Calcul et renvoi la valeur de la page suivante.
	 *
	 * @access	public
	 * @return	array
	 */
    public function getNext() 
    {
        $page_next = $this->page_current + 1;
        
        if($this->page_current >= $this->num_pages) {
		    $page_next = $this->num_pages;
        }  
        
        return $page_next;
    }
    

    
    
    /**
	 * Calcul et renvoi la valeur la page précédente
	 *
	 * @access	public
	 * @return	array
	 */
    public function getPrevious() {
        $page_prev = $this->page_current - 1;
        
        if($this->page_current <= 1) {
		    $page_prev = 1;
        }   
        
        return $page_prev;
    }
    
    
    /**
	 * Initialize les préférences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
    public function initialize($params = array()){
		if (count($params) > 0) {
			foreach ($params as $key => $val){
				if (isset($this->$key)) {
					$this->$key = $val;
				}
			}
		}
	}
	
	
	/**
	 * Retourne la pagination sous forme d'un tableau
	 *
	 * @access	public
	 * @return	array
	 */
	public function toArray() {
	    return $this->page_array;
	}

}

?>
