<?php
namespace Shop\Controller\Admin;
use Exception;
use Shop\App;
use Shop\Model\OrderModel;
use Shop\Model\ProductModel;

class OrderController extends AbstractController {

    protected $curSection = 'order';


    public function listAction($params = []) {
        
        $curPage = $params[0] ?? 1;
        
        /** @var OrderModel $orderModel */
        $orderModel = App::getModel('order');
        
        
        $perPage = 15;
        $count = $orderModel->count();
        $pages = ceil($count / $perPage);
        if (!is_numeric($curPage) || $curPage<0 || $curPage>$pages) {
            throw new Exception("Invalid page number");
        }
        
        
        $orders = $orderModel->findAll($curPage, $perPage);
        $this->renderView('order/list',[
            'title' => 'Order',
            'orders' => $orders,
            'pages'=>$pages,
            'curPage'=>$curPage
        ]);
    }
    
    public function processAction($params)
    {
        $id = $params[0] ?? 0;
        $page = $params[1] ?? 1;
        try {
            if (!$id || !is_numeric($id)) {
                throw new Exception("Invalid ID");
            }
            /** @var OrderModel $orderModel */
            $orderModel = App::getModel('order');
            $orderModel->process($id);
        } catch (Exception $e) {
            $this->flash($e->getMessage());
        }
        $this->redirect('admin/order/list/'.($page>1?$page:''));
    }

    public function completeAction($params)
    {
        $id = $params[0] ?? 0;
        $page = $params[1] ?? 1;
        try {
            if (!$id || !is_numeric($id)) {
                throw new Exception("Invalid ID");
            }
            /** @var OrderModel $orderModel */
            $orderModel = App::getModel('order');
            $orderModel->complete($id);
        } catch (Exception $e) {
            $this->flash($e->getMessage());
        }
        $this->redirect('admin/order/list/'.($page>1?$page:''));
    }

    public function cancelAction($params)
    {
        $id = $params[0] ?? 0;
        $page = $params[1] ?? 1;
        try {
            if (!$id || !is_numeric($id)) {
                throw new Exception("Invalid ID");
            }
            /** @var OrderModel $orderModel */
            $orderModel = App::getModel('order');
            $orderModel->cancel($id);
        } catch (Exception $e) {
            $this->flash($e->getMessage());
        }
        $this->redirect('admin/order/list/'.($page>1?$page:''));
    }

}