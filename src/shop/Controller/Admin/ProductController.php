<?php
namespace Shop\Controller\Admin;
use Exception;
use Shop\App;
use Shop\Model\ProductModel;

class ProductController extends AbstractController {

    protected $curSection = 'product';


    public function listAction($params = []) {
        
        $curPage = $params[0] ?? 1;
        
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        
        
        $perPage = 15;
        $count = $prodModel->count();
        $pages = ceil($count / $perPage);
        if (!is_numeric($curPage) || $curPage<0 || $curPage>$pages) {
            throw new Exception("Invalid page number");
        }
        
        
        $products = $prodModel->findAll($curPage, $perPage);
        $this->renderView('product/list',[
            'title' => 'Products',
            'products' => $products,
            'pages'=>$pages,
            'curPage'=>$curPage
        ]);
    }

}