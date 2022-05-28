<?php
namespace Shop\Controller;

use Shop\Exception\NotFoundException;
use Shop\Model\CategoryModel;
use Shop\Model\ManufacturerModel;
use Shop\Model\ProductModel;
use Shop\App;

class ProductController extends AbstractController {
    
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumbs['product/list']="Products"; 
    }

    public function viewAction($params)
    {
        $id = $params[0] ?? 0;
        if (!$id || !is_numeric($id)) {
            throw new NotFoundException("Invalid ID");
        }
        $this->breadcrumbs['product/view']="View"; 
        
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        
        $product = $prodModel->find($id);
        if (!$product) {
            throw new NotFoundException("Product not found");
        }
        
        $this->renderView('product/view',[
            'title' => $product['name'],
            'product' => $product
        ]);
    }
    
    public function categoryAction($params)
    {
        $id = $params[0] ?? 0;
        if (!$id || !is_numeric($id)) {
            $this->flash("invalid category ID");
            $this->redirect("/");
            return;
        }
        
        /** @var CategoryModel $catModel */
        $catModel = App::getModel('category');
        $category = $catModel->find($id);
        if (!$category) {
            throw new NotFoundException("No category");
        }
        
        $this->breadcrumbs["#"]="Category";
        $this->breadcrumbs["product/category/{$id}"]=$category['name']; 
        
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        $products = $prodModel->findByCategory($id);
        
        
        $this->renderView('product/list',[
            'title' => $category['name'],
            'currentCategory' => $category,
            'products' => $products
        ]);
    }

    public function manufacturerAction(?array $params)
    {
        $id = $params[0] ?? 0;
        if (!$id || !is_numeric($id)) {
            throw new NotFoundException("Invalid manufacturer ID");
        }

        /** @var ManufacturerModel $catModel */
        $manModel = App::getModel('manufacturer');
        $manufacturer = $manModel->find($id);
        if (!$manufacturer) {
            throw new NotFoundException("No manufacturer");
        }

        $this->breadcrumbs["#"]="Manufacturer";
        $this->breadcrumbs["product/manufacturer/{$id}"]=$manufacturer['name'];

        $this->renderView('uc',[
            'title' => $manufacturer['name'],
            'description' => "'{$manufacturer['name']}' [#{$id}] products"
        ]);
    }
}