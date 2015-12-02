<?php
namespace Core\Controller;

use Core\Service\Request;
use \Core\Service\Assets;

abstract class BaseController {

	private $viewCompiled;
	private $viewPath;
	private $viewType;
    private $view;
    private $viewArguments;
    private $request;
    private $wpPath;
    private $assets;
    private $beforeEvent;
    private $afterEvent;

    public final function __construct()
    {
        //Construct
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getWpPath()
    {
        return $this->wpPath;
    }

    public function setWpPath(\stdClass $wpPath)
    {
        $this->wpPath = $wpPath;
    }

    public function getAssets()
    {
        return $this->assets;
    }

    public function setAssets(Assets $assets)
    {
        $this->assets = $assets;
    }

    public function getViewPath()
    {
        return $this->viewPath;
    }

    public function getViewType()
    {
        return $this->viewType;
    }

    public function viewCompiller() {
        switch ($this->viewType) {
            case "HTML" :
                ob_start();

                foreach ($this->viewArguments as $key => &$value){
                    if (!is_numeric($key))
                        $$key = $value;
                }

                include_once $this->view;

                $this->viewCompiled = ob_get_contents();
                ob_end_clean();
            break;

            case "JSON":
                $this->viewCompiled = json_encode($this->viewArguments);
            break;

            default:
                $this->viewType = "NO_VIEW";
            break;
        }
    }

	protected function setView($view, array $argument = []) {
		if(!is_string($view))
            throw new \InvalidArgumentException("O primeiro parametro de \"viewCompiller\" precisa ser uma string!");

		if (!file_exists("{$this->viewPath}/{$view}.php"))
            throw new \InvalidArgumentException("A view \"{$view}.php\" não foi encontrada!");

		$this->view = "{$this->viewPath}/{$view}.php";
        $this->viewArguments = $argument;
		$this->viewType = "HTML";
	}

	protected function viewJson(array $data) {
		$this->viewArguments = $data;
		$this->viewType = "JSON";
	}

	public function setViewPath($controllerName, $path){
		$this->viewPath = $path . "application/View/" . str_replace("Controller", "", $controllerName);
	}

	public function getViewCompiled() {
		return $this->viewCompiled;
	}

    protected function redirectPage($slug, $action = null, $statusCode = 303){
        $url = "{$this->wpPath->urlAdmin}?page={$slug}";
        $url = !is_null($action) ? "{$url}&action={$action}" : $url;
        header('Location: ' . $url, true, $statusCode);
        die();
    }

    protected function setEvent($event, $callback){
        if (is_callable($callback))
            switch ($event){
                case 'beforeView':
                    $this->beforeEvent[] = $callback;
                break;
                case 'afterView':
                    $this->afterEvent[] = $callback;
                break;
            }
    }

    public function getBeforeEvent(){
        return is_null($this->beforeEvent) ? [] : $this->beforeEvent;
    }

    public function getAfterEvent(){
        return is_null($this->afterEvent) ? [] : $this->afterEvent;
    }

	abstract public function indexAction();
}