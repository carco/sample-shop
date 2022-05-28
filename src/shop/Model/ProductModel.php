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

    public function save($data, $id = null)
    {
        if (!$this->validate($data)) {
            return false;
        }
        $params = [
            'name' => $data['name'],
            'price' => (double)$data['price'],
            'description' => $data['description'],
            'category_id' => (int)$data['category_id'],
            'image' => $data['image'],
        ];
        if ($id) { //update
            $sql = "UPDATE `{$this->table}` SET `name` = :name, `price` = :price, `description` = :description, `category_id` = :category_id, `image` = :image WHERE `id` = :id";
            $params['id'] = $id;
            $result = $this->update($sql, $params);
        } else {
            $sql = "INSERT INTO `{$this->table}` (`name`, `price`, `description`, `category_id`, `image`) VALUES (:name, :price, :description, :category_id, :image)";
            $result = $this->insert($sql, $params);
        }
        return $result;
    }

    private function validate($data)
    {
        if (empty($data['name'])) {
            $this->errors['name'] = 'Name is required';
        }
        if (empty($data['price'])) {
            $this->errors['price'] = 'Price is required';
        } else {
            $data['price'] = (double)$data['price'];
            if ($data['price'] <= 0) {
                $this->errors['price'] = 'Price is invalid';
            }
        }
        if (empty($data['category_id'])) {
            $this->errors['category_id'] = 'Category is required';
        } else {
            $catModel = App::getModel('category');
            $category = $catModel->find((int)$data['category_id']);
            if(!$category) {
                $this->errors['category_id'] = 'Invalid category';
            }
        }
        return empty($this->errors);
    }
}