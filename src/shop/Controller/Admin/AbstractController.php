<?php
namespace Shop\Controller\Admin;

use Shop\Controller\AbstractController as BaseAbstractController;

abstract class AbstractController extends BaseAbstractController
{

    
    
    /** @var string $curSection */
    protected $curSection = '';

    public function __construct() {
        return parent::__construct();
    }

    //this is called before action call (@see index.php)
    public function beforeAction() {
        if (empty($_SESSION['loggedUser'])) {
            $this->flash("Please, login!");
            $this->redirect('user/login');
            return false; //action will not be called (@see index.php)
        }
        if(empty($_SESSION['loggedUser']['admin'])) {
            $this->flash("Access denied!");
            $this->redirect('user/dashboard');
            return false;
        }
        return true;
    }
    

    protected function renderView($view, $params = [], $template = 'main.phtml') {
        $path = SHOP_PATH.'/View/Admin/'.$view.'.phtml';

        if(!file_exists($path)) {
            exit("$path not exist");
        }
        ob_start();
        extract($params);
      
        if(!empty($_SESSION['flashMessage'])) {
            $flashMessage = $_SESSION['flashMessage'];
        }

        if(!empty($_SESSION['loggedUser'])) {
            $loggedUser = $_SESSION['loggedUser'];
        }
        $curSection = $this->curSection;

   
        include $path;
        $content = ob_get_contents();
        ob_end_clean();

        $title = $params['title'] ?? 'Shop';
        include SHOP_PATH.'/View/Admin/'.$template;
    }
}