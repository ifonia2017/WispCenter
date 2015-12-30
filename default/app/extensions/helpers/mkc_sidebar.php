<?php
/**
* KumbiaPHP web & app Framework
*
* LICENSE
*
* This source file is subject to the new BSD license that is bundled
* with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://wiki.kumbiaphp.com/Licencia
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@kumbiaphp.com so we can send you a copy immediately.
*
* Helpers HTML
*
* @category   MkcSidebar
* @package    Helpers
* @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
* @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
*/
Load::models('sistema/menu');

class MkcSidebar 
{
    /**
    * Variable que contiene los menús 
    */
    protected static $_main = null;

    /**
    * Variable que contien los items del menú
    */        
    protected static $_items = null;

    /**
    * Variabla para indicar el entorno
    */
    protected static $_entorno;

    /**
    * Variable para indicar el perfil
    */
    protected static $_perfil;


    /**
    * Método para cargar en variables los menús
    * @param type $perfil
    */
    public static function load($entorno, $perfil) {        
        self::$_entorno = $entorno;
        self::$_perfil = $perfil;
        $menu = new Menu();
        if(self::$_main==NULL) {                        
            self::$_main = $menu->getListadoMenuPorPerfil($entorno, $perfil);
        }        
        if(self::$_items==NULL && self::$_main) {
            foreach(self::$_main as $menu) {                
                self::$_items[$menu->menu] = $menu->getListadoSubmenuPorPerfil($entorno, $perfil, $menu->id);
            }
        }
    }

     /**
     * Método para renderizar el menú de escritorio
     */
    public static function desktop() {
        $route = trim(Router::get('route'), '/');
        $html = '';
        if(self::$_main) {
            $html.= '<ul class="sidebar-menu">'.PHP_EOL;
            foreach(self::$_main as $main) {         
                $active = ($main->url==$route) ? 'active' : null;
                if(self::$_entorno==Menu::BACKEND) {
                    $html.= '<li class="'.$active.'">'.DwHtml::link($main->url, $main->menu, array('class'=>'header', 'data-filter'=>"sub-menu-".DwUtils::getSlug($main->menu)), $main->icono).'</li>'.PHP_EOL;
                } else {
                    if(!array_key_exists($main->menu, self::$_items)) {
                        $text = '<i class="fa fa-'.$main->icono.'"></i>'.$main->menu.PHP_EOL;
                        $html.= '<li class="treeview">';                        
                        $html.= DwHtml::link('#', $text, array('class'=>'treeview-menu', 'data-toggle'=>'dropdown'), NULL, FALSE);
                        $html.= '<ul class="dropdown-menu">';
                        foreach(self::$_items[$main->menu] as $item) {                        
                            $active = ($item->url==$route) ? 'active' : null;
                            $html.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, NULL, $item->icon, APP_AJAX).'</li>'.PHP_EOL;
                        }                        
                        $html.= '</ul>'.PHP_EOL;
                        $html.= '</li>'.PHP_EOL;
                    } else {
                        $html.= '<li class="'.$active.'">'.DwHtml::link($main->url, $main->menu, NULL, $main->icono, APP_AJAX).'</li>'.PHP_EOL;
                    }
                }
            }
            $html.= '</ul>'.PHP_EOL;
        }        
        return $html;
    }

    /**
     * Método para renderizar el menú de escritorio
     */
    public static function frontend() {
        $route = trim(Router::get('route'), '/');
        $html = '';
        if(self::$_main) {
            $html.= '<ul class="sidebar-menu">'.PHP_EOL;
            foreach(self::$_main as $main) {         
                $active = ($main->url==$route) ? 'active' : null;
                if(self::$_entorno==Menu::FRONTEND) {
                    $html.= '<li class="'.$active.'">'.DwHtml::link($main->url, $main->menu, array('class'=>'header', 'data-filter'=>"sub-menu-".DwUtils::getSlug($main->menu)), $main->icono).'</li>'.PHP_EOL;
                } else {
                    if(!array_key_exists($main->menu, self::$_items)) {
                        $text = '<i class="fa fa-'.$main->icono.'"></i>'.$main->menu.PHP_EOL;
                        $html.= '<li class="treeview">';                        
                        $html.= DwHtml::link('#', $text, array('class'=>'treeview-menu', 'data-toggle'=>'dropdown'), NULL, FALSE);
                        $html.= '<ul class="dropdown-menu">';
                        foreach(self::$_items[$main->menu] as $item) {                        
                            $active = ($item->url==$route) ? 'active' : null;
                            $html.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, NULL, $item->icon, APP_AJAX).'</li>'.PHP_EOL;
                        }                        
                        $html.= '</ul>'.PHP_EOL;
                        $html.= '</li>'.PHP_EOL;
                    } else {
                        $html.= '<li class="'.$active.'">'.DwHtml::link($main->url, $main->menu, NULL, $main->icono, APP_AJAX).'</li>'.PHP_EOL;
                    }
                }
            }
            $html.= '</ul>'.PHP_EOL;
        }        
        return $html;
    }
    
    /**
     * Método para renderizar el menú de dispositivos móviles     
     */
    public static function phone() {
        $route = trim(Router::get('route'), '/');
        $html = '';
        if(self::$_main) {
            $html.= '<ul class="sidebar-menu">';
            foreach(self::$_main as $main) {
                $text = '<i class="fa fa-'.$main->icono.'"></i>'.$main->menu.PHP_EOL;
                $html.= '<li class="treeview">';
                $html.= DwHtml::link('#', $text, array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'), NULL, FALSE);
                if(array_key_exists($main->menu, self::$_items)) {
                    $html.= '<ul class="treeview-menu" style="position: relative;">';
                    foreach(self::$_items[$main->menu] as $item) { 
                        if(!APP_OFFICE && $item->id == Menu::SUCURSAL) {
                            continue;
                        }
                        $active = ($item->url==$route) ? 'active' : null;                        
                        $html.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, NULL, $item->icon, TRUE).'</li>'.PHP_EOL;
                    }
                    $html.= '</ul>'.PHP_EOL;
                }
                $html.= '</li>'.PHP_EOL;
            }
            $html.= '</ul>'.PHP_EOL;

        }
        return $html;
    }
    
     /**
     * Método para listar los items en el backend
     */
    public static function getItems() {
        $route = trim(Router::get('route'), '/');
        $html = '';        
        foreach(self::$_items as $menu => $items) {
            $html.= '<div id="sub-menu-'.DwUtils::getSlug($menu).'" class="subnav hidden">'.PHP_EOL;
            $html.= '<ul class="nav nav-pills">'.PHP_EOL;
            if(array_key_exists($menu, self::$_items)) {
                foreach(self::$_items[$menu] as $item) {
                    if(!APP_OFFICE && $item->id == Menu::SUCURSAL) {
                        continue;
                    }
                    $active = ($item->url==$route or $item->url=='principal') ? 'active' : null;                    
                    $submenu = $item->getListadoSubmenuPorPerfil(self::$_entorno, self::$_perfil, $item->id);
                    if($submenu) {
                        $html.= '<li class="'.$active.'dropdown">';
                        $html.= DwHtml::link($item->url, ' <b class="fa fa-'.$main->icono.'"></b>'.$item->menu, array('class'=>'dropdown-toggle', 'role'=>"button", "data-toggle"=>"dropdown"), $item->icono);                        
                        $html.= '<ul class="dropdown-menu" role="menu">';
                        foreach($submenu as $tmp) {
                            $html.= '<li>'.DwHtml::link($tmp->url, $tmp->menu, null, $tmp->icono).'</li>'.PHP_EOL;
                        }
                        $html.= '</ul>'.PHP_EOL;
                        $html.= '</li>'.PHP_EOL;
                    } else {
                        $html.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, null, $item->icono).'</li>'.PHP_EOL;
                    }                                        
                }
            }
            $html.= '</ul>'.PHP_EOL;
            $html.= '</div>'.PHP_EOL;
        }
        return $html;  
    }
}