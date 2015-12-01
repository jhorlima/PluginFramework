<?php
namespace Application\Controller;

use Core\Controller\BaseController;

class TesteController extends BaseController {

    public function indexAction() {

//        $this->setEvent('afterView', function(){
//            error_log("chegou", 0);
//            $this->redirectPage('inicio');
//        });

        $this->viewJson(['teste']);
    }
}