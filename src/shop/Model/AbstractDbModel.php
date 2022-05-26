<?php
namespace Shop\Model;

use Exception;
use Shop\App;

abstract class AbstractDbModel extends AbstractModel
{

    protected $table;

    public function getTableName()
    {
        return $this->table;
    }

    public function count()
    {
        $sql = "SELECT count(*) as `cnt` FROM `{$this->table}`";
        $result = $this->query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['cnt'] ?? 0;
    }

    public function last($cnt = 5, $order = 'id')
    {
        $sql = "SELECT * FROM `{$this->table}` ORDER by `$order` DESC LIMIT {$cnt}";
        return $this->select($sql);
    }

    public function find($id, $key = 'id')
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$key}` = :id LIMIT 1";
        $result = $this->query($sql, ['id' => $id]);
        if (mysqli_num_rows($result) <= 0) {
            return false;
        }
        return mysqli_fetch_assoc($result);
    }

    public function query($sql, $params = [], $escape = true)
    {
        $conn = App::getConnection();
        if ($params) {
            $sql = $this->replaceValues($sql, (array)$params, $escape);
        }
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            throw new Exception("SQL error: " . mysqli_error($conn));
        }
        return $result;
    }


    public function select($sql, $params = [])
    {
        $result = $this->query($sql, $params);
        if (mysqli_num_rows($result) <= 0) {
            return [];
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function selectCount($sql, $params = [], $cntField = 'cnt', $idField = null)
    {
        $result = $this->query($sql,$params);
        if(!$idField) { //a single count
            $row = mysqli_fetch_assoc($result);
            return $row[$cntField] ?? 0;
        } else {
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $result = [];
            foreach($rows as $row) {
                $result[$row[$idField]] = $row[$cntField];
            }
        }
        return $result;
    }
    
    public function insert($sql, $params = []) 
    {
        $conn = App::getConnection();
        $this->query($sql, $params);
        return mysqli_insert_id($conn);
    }

    public function update($sql, $params = [])
    {
        $conn = App::getConnection();
        $this->query($sql, $params);
        return mysqli_affected_rows($conn);
    }

    public function delete($sql, $params = [])
    {
        $conn = App::getConnection();
        $this->query($sql, $params);
        return mysqli_affected_rows($conn);
    }
    
    public function begin() {
        $conn = App::getConnection();
        return mysqli_begin_transaction($conn);
    }
    
    public function commit() {
        $conn = App::getConnection();
        return mysqli_commit($conn);
    }
    
    public function rollback() {
        $conn = App::getConnection();
        return mysqli_rollback($conn);
    }
    
    private function replaceValues($sql, $params, $escape = true)
    {
        $conn = App::getConnection();
        $transform = [];
        foreach($params as $k=>$v) {
            if ($escape) {
                $v = "'".mysqli_real_escape_string($conn, $v)."'";
            }
            $transform[":$k"] = $v; //replace :field with $params['field'] 
        }
        return strtr($sql, $transform);
    }
    
    

}