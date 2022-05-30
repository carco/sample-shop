<?php
namespace Shop\Controller\Admin;
use Exception;
use Shop\App;
use Shop\Model\CategoryModel;
use Shop\Model\ProductModel;

class ProductController extends AbstractController {

    protected $curSection = 'product';


    public function listAction($params) {
        
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

    public function exportCsvAction() 
    {
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');

        $products = $prodModel->findAll(1, 9999999);


        $delimiter = ",";
       
        $fields = array('id', 'name', 'category_id', 'category', 'price', 'image', 'description');

        // Set headers to download file rather than displayed 
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="product.csv";');
        $f = fopen('php://output', 'w');
        fputcsv($f, $fields, $delimiter);
        foreach($products as $product) {
            $row = [];
            foreach($fields as $field) {
                $row[$field] = $product[$field];
            }
            fputcsv($f, $row, $delimiter);
        }
        return;
        
    }
    
    public function editAction($params){
        $id = $params[0] ?? 0;
        if (!is_numeric($id) || (int)$id <= 0) {
            throw new Exception("Invalid product id");
        }
        $id = (int)$id;
        
        /** @var ProductModel $prodModel */
        $prodModel = App::getModel('product');
        $product = $prodModel->find($id);
        if (!$product) {
            throw new Exception("Product not found");
        }
        
        $data = $_POST['product'] ?? [];
        $errors = [];
        if ($data) {
            if(!empty($_FILES['image']['name'])){
                $tmpName = $_FILES['image']['tmp_name'] ?? '';
                if($tmpName && is_uploaded_file($tmpName) && getimagesize($tmpName)) {
                    $destFile = SHOP_ROOT.'/assets/product/'.basename($_FILES['image']['name']);
                    if(move_uploaded_file($tmpName, $destFile)) {
                        $data['image'] = basename($destFile);
                    } else {
                        $errors[] = "Cannot upload file";
                    }
                } else {
                    $errors[] = "Invalid uploaded file";
                }
            }
            if(!$errors) {        
                $result = $prodModel->save($data, $id);
                if (false !== $result) { //can be 0 (save return affected rows) if no changes
                    $this->flash($result ? "Product was updated" : "No changes on product");
                    $this->redirect('/admin/product/list');
                    return;
                }
                $errors = $prodModel->getErrrors();
            }
            $this->flash(implode("\n", $errors));
            $product = array_merge($product, $data);
        } 
        
        /** @var CategoryModel $catModel */
        $catModel = App::getModel('category');
        $categories = $catModel->getNames();
        
        $this->renderView('product/edit',[
            'title' => 'Edit Product #'.$product['id'],
            'product' => $product,
            'categories' => $categories,
        ]);
    }

}