<?php 

namespace App\Service;

class Censurator{
    private $badwords= ["con","idiot"];

    public function purify(string $text){        
        $cleanText = array();
        foreach( explode(" ",$text) as $word)
            $cleanText[] = array_search($word,$this->badwords)!==false? str_repeat("*",strlen($word)): $word;
        return implode(" ",$cleanText);
    }   

}