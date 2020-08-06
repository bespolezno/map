<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;

    public $timestamps = false;

    public function generateToken()
    {
        $this->token = md5($this->username);
        $this->save();
        return $this->token;
    }

    public function clearToken()
    {
        $this->token = null;
        $this->save();
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }
}
