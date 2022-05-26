<?php
namespace Shop\Model;

class  ManufacturerModel extends AbstractDbModel {
    protected $table = 'manufacturer';
    
    public function getWithImages($limit = null)
    {
        if ($limit) {
            $order = "ORDER BY RAND()";
            $limit = "LIMIT {$limit}";
        } else {
            $order = "";
            $limit = "";
        }
        $sql = "SELECT `id`,`name`, `image` FROM `{$this->table}` WHERE 1 {$order} {$limit}";
        return $this->select($sql);
    }
    
}