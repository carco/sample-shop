<?php
namespace Shop\Controller;

use Shop\App;
use Shop\Model\OrderModel;
use Shop\Model\UserModel;

class UserController extends AbstractController
{
    public function loginAction($params = [])
    {
        $p0 = $params[0] ?? ''; // "toOrder" when login/register is called from checkout (cart) page
        $errors = [];
        
        if (!empty($_POST['user'])) {
            $email = $_POST['user']['email'] ?? '';
            $pass  = $_POST['user']['password'] ?? '';
            if(empty($email)) {
                $errors['email'] = 'Email is missing';
            }
            if(empty($pass)) {
                $errors['password'] = 'Password is missing';
            }
            if(!$errors) {
                /** @var UserModel $userModel */
                $userModel = App::getModel('user');
                $userData = $userModel->login($email,$pass);
                if ($userData) {
                    session_regenerate_id(true);
                    $this->flash("Welcome ".h($userData['name']));
                    $_SESSION['loggedUser'] = $userData;
                    if ($p0 == 'toOrder') {
                        $this->redirect('cart');
                    } else {
                        $this->redirect('user/dashboard');
                    }
                    return;
                }
                $this->flash("Invalid credentials");
            } else {
                $this->flash("Invalid data, please correct");
            }
        }
        unset($_POST['user']['password']);
        
        $this->breadcrumbs['user/login']="Login";
        $this->renderView('user/login',[
            'title' => 'Login',
            'userLoginData' => $_POST['user'] ?? [],
            'userLoginErrors' => $errors,
            'p0' => $p0,
        ]);
    }

    public function logoutAction()
    {
        if (!empty($_SESSION['loggedUser']['name'])) {
            $this->flash("Goodbye, {$_SESSION['loggedUser']['name']}");
        }
        unset($_SESSION['loggedUser']);
        session_destroy();
        $this->redirect('user/login');
    }

    public function registerAction()
    {
        $p0 = $params[0] ?? ''; // "toOrder" when login/register is called from checkout (cart) page
        
        if (!empty($_SESSION['loggedUser'])) {
            $this->flash("Already logged in");
            if ($p0 == 'toOrder') {
                $this->redirect('cart');
            } else {
                $this->redirect('user/dashboard');
            }
            return;

        }
        $this->breadcrumbs['user/register']="Register";
        if (empty($_POST['user'])) {
            //render login page
            $this->renderView('user/login',[
                'title' => 'Register',
                'p0' => $p0,
            ]);
            return;
        }
        $user = $_POST['user'];

        
        /** @var UserModel $userModel */
        $userModel = App::getModel('user');
        if (!$userModel->register($user)) {
            $errors = $userModel->getErrrors();
            $this->flash(implode("\n", $errors));
            $this->renderView('user/login',[
                'title' => 'Register',
                'user'=>$user,
                'p0'=>$p0
            ]);
            return;
        }
        
        $this->flash("Successfully registered, please login");
        $this->redirect("user/login/{$p0}");
    }

    public function dashboardAction()
    {
        if (empty($_SESSION['loggedUser'])) {
            $this->flash("Please, login!");
            $this->redirect('user/login');
            return;
        }
  
        /** @var OrderModel $orderModel */
        $orderModel = App::getModel('order');
        $orders = $orderModel->getUserOrders($_SESSION['loggedUser']['id']);
        
        
        $this->breadcrumbs['user/dashboard']="Dashboard";
        $this->renderView('user/dashboard',[
            'title' => 'Dashboard',
            'noSidebar' => true,
            'orders' => $orders,
        ]);
    }

}