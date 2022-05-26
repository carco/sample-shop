<?php
namespace Shop\Controller\Admin;
use Exception;
use Shop\App;
use Shop\Model\CategoryModel;

class CategoryController extends AbstractController {

    protected $curSection = 'category';


    public function listAction() {
        /** @var CategoryModel $catModel */
        $catModel = App::getModel('category');
        $categories = $catModel->findAllWithProductCount();
        $this->renderView('category/list',[
            'title' => 'Categories',
            'categories' => $categories,
        ]);
    }

    public function newAction()
    {
        /** @var CategoryModel $catModel */
        $catModel = App::getModel('category');
        $data = $_POST['category'] ?? [];
        if ($data) { //save
            if($catModel->save($data)) {
                $this->flash("Category was created");
                $this->redirect('/admin/category/list');
                return;
            }
            $errors = $catModel->getErrrors();
            $this->flash(implode("\n", $errors));
        }
        $category = [
            'name' => '',
            'position' => 99999
        ];
        if($data) {
            $category = array_merge($category, $data);
        }
        $this->renderView('category/edit',[
            'title' => 'New Category',
            'category' => $category,
        ]);
    }
    
    public function editAction($params) {
    
        $id = $params[0] ?? 0;
        if (!is_numeric($id) || (int)$id <= 0) {
            throw new Exception("Invalid category id");
        }
        $id = (int)$id;
        
        /** @var CategoryModel $catModel */
        $catModel = App::getModel('category');
        $category = $catModel->find($id);
        if (!$category) {
            throw new Exception("Category not found");
        }
        
        $data = $_POST['category'] ?? [];
        if ($data) {
            $result = $catModel->save($data, $id);
            if (false !== $result) { //can be 0 (save return affected rows) if no changes
                $this->flash($result ? "Category was updated" : "No changes on category");
                $this->redirect('/admin/category/list');
                return;
            }
            $errors = $catModel->getErrrors();
            $this->flash(implode("\n", $errors));
            $category = array_merge($category, $data);
        } 
        
        $this->renderView('category/edit',[
            'title' => 'Edit Category #'.$category['id'],
            'category' => $category,
        ]);
    }
    
    
    public function deleteAction($params)
    {
        /** @var CategoryModel $catModel */
        $catModel = App::getModel('category');
        if ($catModel->remove($params[0] ?? 0)) {
            $this->flash("Category #{$params[0]}  was removed");
        } else {
            $errors = $catModel->getErrrors();
            $this->flash(implode("\n", $errors));
        }
        $this->redirect('/admin/category/list');
    }

}