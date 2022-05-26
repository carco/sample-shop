<?php
namespace Shop\Model;

abstract class AbstractModel {

    protected $errors = [];

    public function getErrrors() {
        return $this->errors;
    }

}