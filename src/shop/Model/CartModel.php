<?php
namespace Shop\Model;

use Shop\App;

class CartModel  extends AbstractModel {
 
    public function add($id, $qty = 1) 
    {
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        $product = $prodModel->find($id);
        if (!$product) {
            return false;
        }
        
        $items = $this->getItems();
        if(empty($items[$id])) {
            $items[$id]['product'] = $product;
            $items[$id]['qty'] = 0;
        }
        $items[$id]['qty'] += $qty;
        $_SESSION['cart']['items'] = $items;
        return $items[$id];
    }
    
    public function getItems($check = true)
    {
        $items = $_SESSION['cart']['items'] ?? [];
        if ($check) {
            /** @var ProductModel $prodModel */
            $prodModel = App::getModel('product');
            foreach ($items as $id => $item) {
                $product = $prodModel->find($id);
                if ($product) {
                    $items[$id] = [
                        'product' => $product,
                        'qty' => $items[$id]['qty'] ?? 1
                    ];
                } else {
                    unset($items[$id]);
                }
            }
        }
        return $items;
    }
    public function update($qtyS) 
    {
        $items = $this->getItems();
        foreach($qtyS as $id=>$qty) {
            if ($qty<=0) {
                unset($items[$id]);
            } elseif(isset($items[$id])) {
                $items[$id]['qty'] = $qty;
            } else {
                //nothing to do
            }
        }
        $_SESSION['cart']['items'] = $items ?? [];
        return $_SESSION['cart']['items'];
    }
    
    
    public function clear() {
        $_SESSION['cart'] = [];
    }
    
    
    public function setAddress($billing, $shipping) {
        $_SESSION['cart']['billing'] = $billing;
        $_SESSION['cart']['shipping'] = $shipping;
    }
    public function getBilling() {
        return $_SESSION['cart']['billing'] ?? [];
    }
    public function getShipping() {
        return $_SESSION['cart']['shipping'] ?? [];
    }
}