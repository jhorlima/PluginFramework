<?php
namespace Core\Service;

class Initialize {

	private static $request;

	private static function loadRequest(){
		if(is_null(self::$request))
			self::$request = new Request();
	}

	public static function setMenu(array $menu, array $subMenus = [], $method){

		self::loadRequest();

		add_action('admin_menu', function () use ($menu, $subMenus, $method) {
			add_menu_page(
				$menu[ 'name' ],
				$menu[ 'title' ],
				'manage_options',
				$menu[ 'slug' ],
				$method($menu[ 'Controller' ], isset($menu[ 'action' ]) ? $menu[ 'action' ] : 'index'),
				$menu[ 'icon' ], 6
			);

			foreach ($subMenus as $subMenu) {
				add_submenu_page(
						isset($subMenu['hidden']) && $subMenu['hidden'] ? null : $menu['slug'],
						$subMenu['name'],
						$subMenu['title'],
						'manage_options',
						$subMenu['slug'],
						$method($subMenu[ 'Controller' ], isset($subMenu[ 'action' ]) ? $subMenu[ 'action' ] : 'index')
				);
			}
		});

        if (isset($menu['post']) && $menu['post'])
            self::setActionsPost($menu, $method);

        if (isset($menu['ajax']) && $menu['ajax'])
            self::setActionsAjax($menu, $method);

        foreach ($subMenus as $subMenu) {
            if ((isset($subMenu['post']) && $subMenu['post']))
                self::setActionsPost($subMenu, $method);

            if ((isset($subMenu['ajax']) && $subMenu['ajax']))
                self::setActionsAjax($subMenu, $method);

            if( isset($subMenu['login']) && !$subMenu['login'] && !is_user_logged_in())
                self::setActions($subMenu, $method);
        }
	}

	public static function setActions(array $action, &$method){
		self::loadRequest();
		if(isset(self::$request->getGetData()['page']) && self::$request->getGetData()['page'] == $action[ 'slug' ])
			add_action('init', $method($action[ 'Controller' ], isset($action[ 'action' ]) ? $action[ 'action' ] : 'index'));

        if(isset($action['shortCode']) && is_string($action['shortCode']))
            self::setShortCode($action, $method);
	}

	public static function setShortCode(array $action, &$method){
        add_shortcode($action['shortCode'], $method($action[ 'Controller' ], isset($action[ 'action' ]) ? $action[ 'action' ] : 'index'));
	}

    public static function setActionsAjax(array $action, &$method){
        self::loadRequest();
        if(isset(self::$request->getGetData()['page']) && self::$request->getGetData()['page'] == $action[ 'slug' ])
            add_action("wp_ajax_{$action[ 'slug' ]}", $method($action[ 'Controller' ]));
    }

    public static function setActionsPost(array $action, &$method){
        self::loadRequest();
        if(isset(self::$request->getGetData()['page']) && self::$request->getGetData()['page'] == $action[ 'slug' ])
            add_action("admin_post_{$action[ 'slug' ]}", $method($action[ 'Controller' ]));
    }

	public static function showView($controller, $path = "./"){
        $container = $controller->getViewCompiled();
        $assets = $controller->getAssets();
		switch ($controller->getViewType()) {
			case 'HTML':

                self::processAssets($assets);

                ob_start();

				include_once "{$path}/public/index.php";
                $pageCompiled = ob_get_contents();

                ob_end_clean();
                echo $pageCompiled;

			break;
			case 'JSON':
                wp_send_json($container);
			break;
		}
	}

    public static function processAssets($assets){
        add_action('wp_head_plugin', function() use ($assets) {
            foreach ($assets->getCss() as $css)
                echo "<link rel='stylesheet' type='text/css' href='{$css}'>" . PHP_EOL;
        });

        add_action('wp_footer_plugin', function() use ($assets) {
            foreach ($assets->getJs() as $js)
                echo "<script type='text/javascript' src='{$js}'></script>" . PHP_EOL;
        });
    }
}