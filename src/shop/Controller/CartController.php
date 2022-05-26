<?php
namespace Shop\Controller;

use Shop\Exception\NotFoundException;
use Shop\Model\CartModel;
use Shop\App;
use Shop\Model\OrderModel;

class CartController extends AbstractController {
    
    
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumbs['cart']="Cart";
    }
    
    public function indexAction()
    {
        /** @var CartModel $cartModel */
        $cartModel = App::getModel('cart');
        $items = $cartModel->getItems();
        $this->renderView('cart/list',[
           'noSidebar' => true,
        ]);
    }

    public function addAction($params)
    {
        if (!empty($_POST['product']['id'])) {
            $id = $_POST['product']['id'];
            $qty = max(1, intval($_POST['product']['qty'] ?? 1));
        } else {
            $id = $params[0] ?? null;
            $qty = 1;
        }
        if(!$id) {
            throw new NotFoundException("Invalid product ID");
        }
        /** @var CartModel $cartModel */
        $cartModel = App::getModel('cart');
        $item =  $cartModel->add($id, $qty);
        if (!$item) {
            throw new NotFoundException("Product not found");
        }
        
        $this->flash("{$item['product']['name']} was added into cart!");
        $this->redirect('cart');
    }
    
    public function updateAction() 
    {
        $qtyS = $_POST['qty'];
        if(!$qtyS) {
            $this->flash("Nothing to update");
        } else {
            /** @var CartModel $cartModel */
            $cartModel = App::getModel('cart');
            $cartModel->update($qtyS);
            $this->flash("Cart updated");
        }
        $this->redirect('cart');
    }
    
    public function orderAction()
    {
        $loggedUser = $_SESSION['loggedUser'];
        $userId = $loggedUser['id'] ?? 0;
        if (!$userId) {
            $this->flash("Please login");
            $this->redirect('user/login');
            return;
        }

        /** @var CartModel $cartModel */
        $cartModel = App::getModel('cart');
        $items = $cartModel->getItems();
        $billing = $_POST['billing'] ?? [];
        $shipping =  $_POST['shipping'] ?? [];
        $cartModel->setAddress($billing, $shipping);
        
        /** @var OrderModel $orderModel */
        $orderModel = App::getModel('order');
        $orderId = $orderModel->create($userId, $items, $billing, $shipping);
        
        if (!$orderId) {
            $errors = $orderModel->getErrrors();
            $this->flash(implode("\n", $errors));
            $this->redirect('cart');
        } else {
            $cartModel->clear();
            $this->flash("Order successfully created (#{$orderId})");
            $this->redirect('user/dashboard');
        }
    }
    
    
}