<?php

class bookmark {
    /* Attributes */
    private $title;
    private $link;
    private $description;
    private $tags;
    private $dateLastModified;
    

    /* create a Bookmark */
    function __construct($title, $link, $description, $tags, $dateLastModified)
    {
        $this->title        = $title;
        $this->link         = $link;
        $this->description  = $description;
        $this->tags  = $tags;
        $this->dateLastModified  = $dateLastModified;
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
