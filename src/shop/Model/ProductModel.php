<?php
namespace Shop\Model;

use Exception;
use Shop\App;

class ProductModel extends AbstractDbModel
{

    protected $table = 'product';

    public function findByCategory($id, $limit = null, $order = null)
    {
        $limitClause = '';
        $orderClause = '';
        if((int)$limit > 0) {
            $limitClause = "LIMIT ".(int)$limit;
            if (!$order) {
                $orderClause = "ORDER BY RAND()";
            }
        }
        if ($order) {
            $orderClause = "ORDER BY `{$order}`";
        }
        $sql = "SELECT * FROM `{$this->table}` WHERE `category_id` = :id {$orderClause} {$limitClause}";
        return $this->select($sql, ['id' => $id]);
    }

    public function findAll($page = 1, $limit = 15)
    {
        $page = (int)$page;
        $limit = (int)$limit;
        if($page<=0 || $limit <=0) {
            throw new Exception("Invalid params");
        }
        $offset = ($page-1)*$limit;
        
        /** @var CategoryModel $categModel */
        $categModel = App::getModel('category');
        $categTable = $categModel->getTableName();
        
        $sql = "
                SELECT `P`.*, `C`.`name` as `category` FROM `{$this->table}` AS `P` 
                LEFT JOIN `{$categTable}` AS `C` ON `P`.`category_id` = `C`.`id`
                WHERE 1 
                ORDER BY `id` 
                LIMIT {$limit}
                OFFSET {$offset}
        ";
        return $this->select($sql);
    }

    public function countByCategory($id) 
    {
        $idValues = implode(', ', $idValues);
        $sql = "SELECT count(*) `cnt`FROM {$this->table} WHERE `category_id` = :categoryId";
        return $this->selectCount($sql, ['categoryId'=>$id]);
    }
    
    public function countByCategories($ids) {
        
        $idValues = [];
        foreach($ids as $id) {
            $id = (int)$id;
            if($id > 0 ) {
                $idValues[] = $id;
            }
        }

        if(!$idValues) {
            return [];
        }
        $idValues = implode(', ', $idValues);
        $sql = "SELECT count(*) `cnt`,`category_id` FROM {$this->table} WHERE `category_id` IN ({$idValues}) GROUP BY `category_id`";
        return $this->selectCount($sql, ['categoryId'=>$id],'cnt', 'category_id');
    }
}