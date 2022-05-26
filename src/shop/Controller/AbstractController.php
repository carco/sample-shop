<?php
namespace Shop\Controller;

use Shop\App;
use Shop\Model\CartModel;

abstract class AbstractController {
    
    protected $breadcrumbs;
    
    /** @var string $redirectTo */
    private $redirectTo;
    /** @var bool $redirectTo */
    private $redirectPermanent = false;
    
    public function __construct() {
        $this->breadcrumbs = [
            '/' => 'Home'
        ];
    }
    
    //this is called before action call (@see index.php)
    public function beforeAction()
    {
        return true; //if false, 'action' will not be called 
    }
    
    //this is called after action call (@see index.php)
    public function afterAction() {
        return;
    }
    
    protected function renderView($view, $params = [], $template = 'main.phtml') {
        $path = SHOP_PATH.'/View/'.$view.'.phtml';
        
        if(!file_exists($path)) {
            exit("$path not exist");
        }
        ob_start();
        extract($params);
        if(!isset($breadcrumbs)) {
            $breadcrumbs = $this->breadcrumbs;
        }
        if(!empty($_SESSION['flashMessage'])) {
            $flashMessage = $_SESSION['flashMessage'];
        }
        
        if(!empty($_SESSION['loggedUser'])) {
            $loggedUser = $_SESSION['loggedUser'];
        }
        
        /** @var CartModel $cartModel */
        $cartModel =  App::getModel('cart');
        $cartItems = $cartModel->getItems();
        $billing = $cartModel->getBilling();
        $shipping = $cartModel->getShipping();

        
        include $path;
        $content = ob_get_contents();
        ob_end_clean();
        
        $title = $params['title'] ?? 'Shop';
        include SHOP_PATH.'/View/main.phtml';
    }
    
    protected function redirect($url, $permanent = false)
    {
         if (strpos($url,"://") === false) {
             $url = "/".ltrim($url,'/');
         }
        $this->redirectTo = $url;
        $this->redirectPermanent = $permanent;
        return $this;
    }
    public function flash(?string $message = null) {
        if ($message) {
            $_SESSION['flashMessage'] = $message;
        } else {
            unset($_SESSION['flashMessage']);
        }
    }
    
    public function isRedirect() {
        return !empty($this->redirectTo);
    }
    
    public function getRedirectTo() {
        return $this->redirectTo;
    }
    public function isRedirectPermanent() {
        return  $this->redirectPermanent;
    }
}