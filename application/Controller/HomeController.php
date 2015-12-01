<?php
namespace Application\Controller;

use Core\Controller\BaseController;

class HomeController extends BaseController {

    public function testeAction() {
        $this->viewJson([ $this->getRequest()->getMethod() ]);
    }

    public function indexAction() {
        $this->getAssets()->setJs('js/teste.js');

        if( $this->getRequest()->isGet()){
            $this->setView('HomeView', [
                'dados' => $this->getRequest()->requestGet('http://gpu/gpu/local', [
                    'headers' => [
                        'app-token' => 'coordenadoriaplanejamentofisico',
                        'app-name'  => 'cpf',
                    ]
                ])['body']
            ]);
        }
    }
}
