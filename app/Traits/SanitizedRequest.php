<?php

namespace App\Traits;

trait SanitizedRequest{

    private $clean = false;

    public function all(){
        return $this->sanitize(parent::all());
    }


    protected function sanitize(Array $inputs){
        if($this->clean){ return $inputs; }

        foreach($inputs as $i => $item){
            $inputs[$i] = trim($item);
        }

        $this->replace($inputs);
        $this->clean = true;
        return $inputs;
    }
}
