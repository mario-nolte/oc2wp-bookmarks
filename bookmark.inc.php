<?php

class bookmark {
    /* Attributes */
    private $title;
    private $link;
    private $description;
    

    /* create a Bookmark */
    function __construct($title, $link, $description)
    {
        $this->title        = $title;
        $this->link         = $link;
        $this->description  = $description;
    }
    
 public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }


    /* Kontroll-Ausgabe der Daten eines Bookmarks */
  /*  function __toString()
    {
        $output = "Title: $this->title, ";
        $output .= "Link: $this->link, ";
        $output .= "Description: $this->description";
        
        return($output);
    }*/

}
