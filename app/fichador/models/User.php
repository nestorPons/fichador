<?php

namespace app\fichador\models;

use \app\fichador\core\{Conn, BaseClass};
use \PHPMailer\PHPMailer\{PHPMailer};

class User extends BaseClass
{
    private
        $id,
        $dni,
        $name,
        $email,
        $pass;

    function __construct(string $name_user)
    {
        $this->name = $name_user;
        $this->load_user();
    }
    private function load_user() : self
    {
        $conn = new Conn('singin', 'employee', true);
        $data = $conn->getBy("name LIKE '" . $this->name ."'")->get();
        foreach($data as $key => $value){
            $this->{$key} = $value;
        }
        return $this;
    }
    public function id(){
        return $this->id;
    }
    public function dni(string $value = null){
        $name_fun = explode('::',__METHOD__)[1];
        if($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    public function user(string $value = null){
        $name_fun = explode('::',__METHOD__)[1];
        if($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    public function email(string $value = null){
        $name_fun = explode('::',__METHOD__)[1];
        if($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    public function pass(string $value = null){
        $name_fun = explode('::',__METHOD__)[1];
        if($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
}
