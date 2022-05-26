<?php
namespace Shop\Controller;
use Exception;
use Shop\App;
use Shop\Model\CategoryModel;

class PageController extends AbstractController {
    
    public function indexAction()
    {
        /** @var CategoryModel $catModel */
        $catModel = App::getModel('category');

        $categories = $catModel->findAllWithSampleProducts();
        $this->renderView('page/home',[
            'title' => "Welcome",
            'categories'=>$categories,
            'noSidebar'=>true,
        ]);
    }
    
    public function contactAction()
    {
        $data = $_POST['contact'] ?? [];
        $message = '';
        if ($data) {
            if(!empty($_SESSION['lastEmailSent'])) {
                $diff = time() - $_SESSION['lastEmailSent'];
                if ($diff < 60) { //last email sent under 60sec
                    $this->flash("Please wait few minutes before sending new message!");
                    $this->redirect("/page/contact");
                    return;
                }
            }
            
            if(!defined('SHOP_EMAIL') || !filter_var(SHOP_EMAIL, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("SHOP_EMAIL not (right) configured, cannot send email");
            }
            if (empty($data['message'])) {
                $this->flash("empty message, nothing sent!");
                $this->redirect("/page/contact");
                return;
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->flash("Invalid email, message was not sent!");
                $this->redirect("/page/contact");
                return;
            }
            $name = $data['name'] ??'Anonymous';
            
            $headers = [
                'From' => SHOP_EMAIL,
                'Reply-to' => "\"{$name}\" <{$data['email']}>",
                'X-Mailer' => 'PHP/' . phpversion()
            ];
            
            $subject = $data['subject'] ?? "Site contact";
            $message = [];
            foreach($data as $k=>$v) {
                $message[] = "$k: $v";
            }
            $message = implode("\n",$message);
            if (mail (SHOP_EMAIL,$subject, $message, $headers)) {
                $message = "Message<br><pre>{$message}</pre><br>sent successfully!";
            } else {
                $message = 'Message could not be sent, please try again later';
            }
            $_SESSION['lastEmailSent'] = time();

        }
        
        $this->renderView('page/contact',[
            'title' => "Contact",
            'noSidebar'=>true,
            'message'=>$message
        ]);
    }
}