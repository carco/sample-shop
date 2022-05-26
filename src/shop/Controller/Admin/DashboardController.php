<?php
namespace Shop\Controller\Admin;
use Shop\App;
use Shop\Model\OrderModel;
use Shop\Model\ProductModel;
use Shop\Model\UserModel;

class DashboardController extends AbstractController {

    protected $curSection = 'dashboard';
    
    public function indexAction($params = []) {
        
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        $prodCount = $prodModel->count();
        $products = $prodModel->last(5); //last 5 products

        /** @var OrderModel $orderModel */
        $orderModel = App::getModel('order');
        $orderCount = $orderModel->count();
        $orders = $orderModel->last(5, 'created'); //last 5 order

        /** @var UserModel $orderModel */
        $userModel = App::getModel('user');
        $userCount = $userModel->count();
        $users =  $userModel->last(5, 'registered');



        $this->renderView('dashboard',[
            'title' => 'Dashboard',
            'products' => $products,
            'orders'=> $orders,
            'users'=>$users,
            'prodCount'=>$prodCount,
            'orderCount' =>$orderCount,
            'userCount'=>$userCount
        ]);
    }

}