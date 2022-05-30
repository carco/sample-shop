<?php
namespace Shop\Controller\Admin;
use Exception;
use Shop\App;
use Shop\Model\UserModel;

class UserController extends AbstractController {

    protected $curSection = 'user';


    public function listAction($params) {
        
        $curPage = $params[0] ?? 1;
        
        /** @var UserModel $userModel */
        $userModel = App::getModel('user');
        
        
        $perPage = 15;
        $count = $userModel->count();
        $pages = ceil($count / $perPage);
        if (!is_numeric($curPage) || $curPage<0 || $curPage>$pages) {
            throw new Exception("Invalid page number");
        }
        
        $users = $userModel->findAll($curPage, $perPage);
        $this->renderView('user/list',[
            'title' => 'Users',
            'users' => $users,
            'pages'=>$pages,
            'curPage'=>$curPage
        ]);
    }

}