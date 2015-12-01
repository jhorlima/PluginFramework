<?php
/*
Plugin Name: PluginFramework
Plugin URI: http://github.com/jhorzyto/PluginFramework
Description: Um framework para plugin wordpress
Version: 1.0
Author: Jhordan Lima
Author URI: http://github.com/jhorzyto
License: GPLv2
*/
/*
 *      Copyright 2015 Jhordan Lima <jhordanlima.uema.dpd@gmail.com>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 3 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

use \Core\Service\Initialize;
use \Core\Controller\BaseController;
use \Core\Service\Request;
use \Core\Service\Log;
use \Core\Service\Assets;

define('PATH_PLUGIN', plugin_dir_path(__FILE__));
define('URL_PUBLIC', plugins_url('public/', __FILE__));
define('URL_PLUGIN', plugins_url('', __FILE__));
define('URL_INCLUDE', includes_url());
define('URL_ADMIN', admin_url());
define('URL_HOME', home_url());

try {
    require_once "vendor/autoload.php";
    add_action('plugins_loaded', function(){

        require_once "application/Config/dependencies.php";
        require_once "application/Config/activation.php";

        if (isset($init) && is_callable($init))
            add_action('init', $init);

        if (isset($admin_init) && is_callable($admin_init))
            add_action('admin_init', $admin_init);

        if (isset($wp_head) && is_callable($wp_head))
            add_action('wp_head', $wp_head);

        if (isset($admin_head) && is_callable($admin_head))
            add_action('admin_head', $admin_head);

        if (isset($wp_footer) && is_callable($wp_footer))
            add_action('wp_footer', $wp_footer);

        $processAction = function (BaseController $controller, $controllerName, $action) {

            $request = new Request();

            $assets = new Assets(URL_PUBLIC);

            $listPath = new stdClass();

            $listPath->pathPlugin = PATH_PLUGIN;
            $listPath->urlPublic = URL_PUBLIC;
            $listPath->urlPlugin = URL_PLUGIN;
            $listPath->urlInclude = URL_INCLUDE;
            $listPath->urlAdmin = URL_ADMIN;
            $listPath->urlHome = URL_HOME;

            if(!($controller instanceof BaseController))
                throw new InvalidArgumentException("{$controllerName} Invalido!");

            $controller->setViewPath($controllerName, PATH_PLUGIN);

            $controller->setWpPath($listPath);

            $controller->setAssets($assets);

            $request->setWpPath($listPath);

            $controller->setRequest($request);

            if(isset($request->getGetData()['action']) && method_exists($controller, "{$request->getGetData()['action']}Action"))
                $controller->{"{$request->getGetData()['action']}Action"}();

            elseif(method_exists($controller, "{$action}Action"))
                $controller->{"{$action}Action"}();

            else
                $controller->{"IndexAction"}();

            foreach($controller->getBeforeEvent() as &$beforeEvent)
                $beforeEvent();

            $controller->viewCompiller();

            foreach($controller->getAfterEvent() as &$afterEvent)
                $afterEvent();

            if ($controller->getViewType() !== "NO_VIEW")
                Initialize::showView($controller, PATH_PLUGIN);
        };

        $processController = function ($controller, $action = 'Index') use ($processAction) {

            $controllerName = "\\Application\\Controller\\{$controller}Controller";

            return function() use (&$processAction, &$controllerName, &$controller, &$action) {
                $processAction(new $controllerName(), $controller, $action);
            };
        };

        if (isset($menu) && is_array($menu))
            Initialize::setMenu($menu, isset($subMenu) ? $subMenu: [], $processController);

        if (isset($actions) && is_array($actions))
            foreach($actions as $action)
                Initialize::setActions($action, $processController);
    });

    if (isset($activation) && is_callable($activation))
        register_activation_hook(__FILE__, $activation);

    if (isset($deactivation) && is_callable($deactivation))
        register_deactivation_hook(__FILE__, $deactivation);

} catch ( InvalidArgumentException $e ) {
    Log::register(PATH_PLUGIN, $e->getMessage());
}