<?php 

namespace Database\Entities;

class Movie {

    public $id;
    public $title;
    public $description;
    public $user_name;
    public $publication_date;
    public $likes;
    public $hates;

    public function __construct() {}

    public function __toString(){
        
        return $this->title.'-'.$this->description.'-'.$this->user_name.'-'.$this->publication_date.'-'.$this->likes.'-'.$this->hates; 
 }

}

 