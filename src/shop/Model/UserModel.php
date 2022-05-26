<?php
namespace Shop\Model;

use Exception;

class UserModel extends AbstractDbModel {
    
    protected $table = 'user';
    
    public function login($email, $pass) {
        $userData = $this->find($email, 'email'); //findByEmail
        if ($userData && ($userData['password'] === hash('sha256', $pass))) {
            return $userData;
        } else {
            return false;
        }
    }

    public function register($data, $validate = true)
    {
        if ($validate) {
            if (!$this->validate($data)) {
             return false;
            }
        } else {
            //required keys 
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                throw new Exception("Invalid data");
            }
        }
        $data['password'] = hash('sha256', $data['password']);
        return $this->insert(
            "INSERT INTO `{$this->table}` (`email`, `name`, `password`) VALUES (:email, :name, :password)",
                $data
        );
    }
    
    public function validate($user)
    {
        $this->errors = [];
        
        //check email
        if(empty($user['email'])) {
            $this->errors['email'] = "email is missing";
        } else {
            if(!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = "email is not valid";
            }
        }

        //check name
        if(empty($user['name'])) {
            $this->errors['name'] = "name is missing";
        } else {
            if (strlen($user['name']) < 3) {
                $this->errors['name'] = "name too short";
            } else {
                if (!strpos($user['name'],' ')) { //doesn't contain space (or is on first position)
                    $this->errors['name'] ="name is invalid, use first and last name";
                }
            }
        }


        //check password(s)
        if(empty($user['password']) || empty($user['password2'])) {
            $this->errors['password'] = "password is missing";
        } else {
            if ($user['password'] != $user['password2']) {
                $this->errors['password'] = "password && confirm password don't match";
            }
        }
        
        if(!$this->errors) {
            //check if user already exist
            $existingUser = $this->find($user['email'], 'email'); //findByEmail
            if($existingUser) {
                $this->errors['email'] = "email already used";
            }
        }
        return empty($this->errors);
    }
    
    
    
    

}