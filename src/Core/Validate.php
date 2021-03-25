<?php


namespace App\Core;


use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;

class JwtService
{
    protected array $fields;



    /**
     * Set the value of fields
     *
     * @return  self
     */ 
    public function setFields($fields)
    {
        $this->fields[] = $fields;

        return $this;
    }
}