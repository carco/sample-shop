<?php
namespace Shop\Model;

use Exception;

class OrderModel extends AbstractDbModel {
    protected $table = 'order';
    protected $tableItems = 'order_items';

    public function getUserOrders($userId)
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `user_id` = :userId ORDER BY `created` DESC";
        $orders = $this->select($sql, ['userId' => $userId]);
        $this->attachOrderItems($orders);
        return $orders;
    }

    
    public function create($userId, $items, $billing, $shipping = null, $validate = true)
    {
        if ($validate) {
            $ok = $this->validateItems($items) && $this->validateAddress($billing) && $this->validateAddress($shipping, true);
            if (!$ok) {
                return false;
            }
        } 
        $total = 0;
        foreach($items as $id=>$item) {
            $total += $item['product']['price']*$item['qty'];
        }
        
        //fill missing shipping values from billing address
        foreach($shipping as $k=>$v) {
            if (empty($v)) {
                $shipping[$k] = $billing[$k] ?? '';
            }
        }
        
        $this->begin();
        try {
            //insert order
            $orderId = $this->insert(
                "INSERT INTO `{$this->table}` (
                         `user_id`, 
                         `bill_name`, `bill_email`, `bill_phone`, `bill_address`, `bill_city`, `bill_state`,
                         `ship_name`, `ship_email`, `ship_phone`, `ship_address`, `ship_city`, `ship_state`,
                         `no_items`, `total`
                ) VALUES (
                          :userId, 
                          :billName, :billEmail, :billPhone, :billAddress, :billCity, :billState,
                          :shipName, :shipEmail, :shipPhone, :shipAddress, :shipCity, :shipState,
                          :noItems, :total
                );",
                [
                    'userId' => $userId,
                    'billName' => $billing['name'] ?? '',
                    'billEmail' => $billing['email'] ?? '',
                    'billPhone' => $billing['phone'] ?? '',
                    'billAddress' => $billing['address'] ?? '',
                    'billCity' => $billing['city'] ?? '',
                    'billState' => $billing['state'] ?? '',
                    'shipName' => $shipping['name'] ?? '',
                    'shipEmail' => $shipping['email'] ?? '',
                    'shipPhone' => $shipping['phone'] ?? '',
                    'shipAddress' => $shipping['address'] ?? '',
                    'shipCity' => $shipping['city'] ?? '',
                    'shipState' => $shipping['state'] ?? '',
                    'noItems' => count($items),
                    'total' => round($total, 2)
                ]
            );
            
            //insert order items
            foreach($items as $id=>$item) {
                $this->insert("
                    INSERT INTO `{$this->tableItems}` (`order_id`, `product_id`, `name`, `price`, `qty`, `total`) 
                    VALUES (:orderId, :productId, :name, :price, :qty, :total)
                ", [
                        'orderId' => $orderId,
                        'productId' => $item['product']['id'],
                        'name' => $item['product']['name'],
                        'price' => $item['product']['price'],
                        'qty' => $item['qty'],
                        'total'=>round($item['qty'] * $item['product']['price'],2)
                    ]
                );
            }
            $this->commit();
        } catch (Exception $e) {
            $this->errors['_mysql'] = $e->getMessage();
            return false;
        }
        return $orderId;
    }
    
    public function validateItems($items) {
        if(!count($items)) {
            $this->errors['_items'] = 'No itemns';
            return false;
        }
        $err = false;
        foreach($items as $id=>$item) {
            $product = $item['product'] ?? [];
            $qty = $item['qty'] ?? 0;
            if(!$id || !$product || !$qty || $id != $product['id']) {
                $this->errors['_items_'.$id] = "Product {$id} is invalid";
                $err = true;
            }
        }
        return !$err;
    }
    
    public function validateAddress($address, $isShipping = false) {
       
        if(!$isShipping) {
            $key = '_bill_address';
            $type = 'billing';
            //minimal check 
            if (empty($address['name']) || empty($address['email']) || empty($address['phone'])) {
                $this->errors[$key] = "Invalid {$type} address (missing name, email or phone)";
                return false;
            }
        } else {
            $key = '_ship_address';
            $type = 'shipping';
        }
        $err = false;
        
        //check email
        if(!empty($address['email']) && !filter_var($address['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$key.'_email'] = "Invalid {$type} address (email is wrong)";
            $err = true;
        }
        
        //check name
        if(!empty($address['name'])) {
            if (strlen($address['name']) < 3) {
                $this->errors[$key.'_name'] = "Invalid {$type} address (name too short)";
                $err = true;
            } else {
                if (!strpos($address['name'],' ')) { //doesn't contain space (or is on first position)
                    $this->errors[$key.'_name'] = "Invalid {$type} address (use first & last name)";
                    $err = true;
                }
            }
        }
        
        return !$err;
    }

    public function findAll($page = 1, $limit = 15)
    {
        $page = (int)$page;
        $limit = (int)$limit;
        if($page<=0 || $limit <=0) {
            throw new Exception("Invalid params");
        }
        $offset = ($page-1)*$limit;
        $sql = "SELECT * FROM `{$this->table}` WHERE 1 ORDER BY `created` DESC LIMIT {$limit} OFFSET {$offset}";
        $orders =  $this->select($sql);
        $this->attachOrderItems($orders);
        return $orders;
    }
    

    private function attachOrderItems(&$orders) {
        foreach($orders as $k=>$order) {
            $sql = "SELECT * FROM `{$this->tableItems}` WHERE `order_id` = :orderId";
            $orders[$k]['_items'] = $this->select($sql, ['orderId' => $order['id']]);
        }
    }
    
    public function process($id) 
    {
        return $this->updateStatus($id, 'processing');
    }
    
    public function complete($id)
    {
        return $this->updateStatus($id, 'complete');
    }
    
    public function cancel($id)
    {
        return $this->updateStatus($id, 'canceled');
    }
    
    private function updateStatus($id, $newStatus) {
        
        $order = $this->find($id);
        if(!$order) {
            throw new Exception("Order not found");
        }
        if ('processing' == $newStatus) {
            if ('new' != $order['status']) {
                throw new Exception("Order cannot be processed, expected 'new' status, '{$order['status']}' found");
            }
        } elseif ('complete' == $newStatus) {
            if ('processing' != $order['status']) {
                throw new Exception("Order cannot be processed, expected 'processing' status, '{$order['status']}' found");
            }
        } elseif ('canceled' == $newStatus) {
            if ('new' != $order['status'] && 'processing' != $order['status']) {
                throw new Exception("Order cannot be processed, expected 'new' or 'processing' status, '{$order['status']}' found");
            }   
        } else {
            throw new Exception('Invalid status '.$newStatus);
        }
        $sql = "UPDATE `{$this->table}` SET `status` = :status WHERE `id` = :id";
        return $this->update($sql, ['id'=>$order['id'],'status'=> $newStatus]);
    }
    
}