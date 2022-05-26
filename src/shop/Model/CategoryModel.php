<?php
namespace Shop\Model;

use Exception;
use Shop\App;

class CategoryModel extends AbstractDbModel
{
    protected $table = 'category';

    public function getNames()
    {
        $sql = "SELECT `id`,`name` FROM `{$this->table}` ORDER BY `position`, `id`";
        return $this->select($sql);
    }

    public function findAllWithSampleProducts()
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE 1 ORDER BY `position`,`id`";
        $result = [];
        $rows = $this->select($sql);

        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        foreach ($rows as $row) {
            $row['_products'] = $prodModel->findByCategory($row['id'], 3);
            if ($row['_products']) {
                $result[$row['id']] = $row;
            }
        }
        return $result;
    }

    
    public function findAllWithProductCount()
    {

        $sql = "SELECT * FROM `{$this->table}` WHERE 1 ORDER BY `position`,`id`";
        $result = [];
        $rows =  $this->select($sql);
        foreach($rows as $row) {
            $row['_prodCount'] = 0;
            $result[$row['id']] = $row;
        }
        $ids = array_keys($result);
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        
        $prodCount = $prodModel->countByCategories($ids);
        foreach($prodCount as $id=>$cnt) {
            $result[$id]['_prodCount'] = $cnt;
        }
        return $result;
    }
    public function save($data, $id = null)
    {
        if (!$this->validate($data)) {
            return false;
        }
        $params = [
            'name' => $data['name'],
            'position' => (int)$data['position']
        ];
        if ($id) { //update
            $sql = "UPDATE `{$this->table}` SET `name` = :name, `position` = :position WHERE `id` = :id";
            $params['id'] = $id;
            $result = $this->update($sql, $params);
        } else {
            $sql = "INSERT INTO `{$this->table}` (`name`, `position`) VALUES (:name, :position)";
            $result = $this->insert($sql, $params);
        }
        return $result;
    }

    private function validate($data)
    {
        if (empty($data['name'])) {
            $this->errors['name'] = 'Name is required';
        }
        if (empty($data['position'])) {
            $this->errors['position'] = 'Position is required';
        } else {
            $data['position'] = (int)$data['position'];
            if ($data['position'] <= 0) {
                $this->errors['position'] = 'Position is invalid';
            }
        }
        return empty($this->errors);
    }

    public function remove($id)
    {
        $id = (int)$id;
        $category = null;
        if($id >0)  {
            $category = $this->find($id);
        }
        if (!$category) {
            $this->errors[] = "Invalid category id";
            return false;
        }
        //check products
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        $prodCount = $prodModel->countByCategory($category['id']); 
        if($prodCount>0) {
            $this->errors[] = "{$prodCount} product(s) belongs to this category, NOT removed";
            return false;
        }
        
        $sql = "DELETE FROM `{$this->table}` WHERE `id` = :id";
        return $this->delete($sql, ['id'=>$id]);
    }
}