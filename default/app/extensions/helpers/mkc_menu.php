<?php
/**
 * Dailyscript - Web | App | media
 *
 * Extension para renderizar los menús
 *
 * @category    Helpers
 * @author      Iván D. Meléndez
 * @package     Helpers
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

Load::models('sistema/menu');

class MkcMenu {
    
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
                    $html.= '<li class="'.$active.'">'.MkcHtml::link('dashboard/','Dashboard',NULL,'dashboard', FALSE).'</li>'.'<li class="'.$active.' treeview">'.MkcHtml::link($main->url, $main->menu, array('class'=>'header', 'data-filter'=>"sub-menu-".MkcUtils::getSlug($main->menu)), $main->icono).'</li>'.PHP_EOL;
                } else {
                    if(!array_key_exists($main->menu, self::$_items)) {
                        $text = '<i class="fa fa-'.$main->icono.'"></i>'.$main->menu.PHP_EOL;
                        $html.= '<li class="treeview">';                        
                        $html.= MkcHtml::link('#', $text, array('class'=>'treeview-menu', 'data-toggle'=>'dropdown'), NULL, FALSE);
                        $html.= '<ul class="treview-menu dropdown-menu">';
                        foreach(self::$_items[$main->menu] as $item) {                        
                            $active = ($item->url==$route) ? 'active' : null;
                            $html.= '<li class="'.$active.' treview-menu">'.MkcHtml::link($item->url, $item->menu, NULL, $item->icon, APP_AJAX).'</li>'.PHP_EOL;
                        }                        
                        $html.= '</ul>'.PHP_EOL;
                        $html.= '</li>'.PHP_EOL;
                    } else {
                        $html.= '<li class="'.$active.'">'.MkcHtml::link($main->url, $main->menu, NULL, $main->icono, APP_AJAX).'</li>'.PHP_EOL;
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
                    $html.= '<li class="'.$active.'">'.MkcHtml::link($main->url, $main->menu, array('class'=>'header', 'data-filter'=>"sub-menu-".MkcUtils::getSlug($main->menu)), $main->icono).'</li>'.PHP_EOL;
                } else {
                    if(!array_key_exists($main->menu, self::$_items)) {
                        $text = '<i class="fa fa-'.$main->icono.'"></i>'.$main->menu.PHP_EOL;
                        $html.= '<li class="treeview dropdown">';                        
                        $html.= MkcHtml::link('#', $text, array('class'=>'treeview-menu', 'data-toggle'=>'dropdown'), NULL, FALSE);
                        $html.= '<ul class="treview-menu dropdown-menu">';
                        foreach(self::$_items[$main->menu] as $item) {                        
                            $active = ($item->url==$route) ? 'active' : null;
                            $html.= '<li class="'.$active.'">'.MkcHtml::link($item->url, $item->menu, NULL, $item->icon, APP_AJAX).'</li>'.PHP_EOL;
                        }                        
                        $html.= '</ul>'.PHP_EOL;
                        $html.= '</li>'.PHP_EOL;
                    } else {
                        $html.= '<li class="'.$active.'">'.MkcHtml::link($main->url, $main->menu, NULL, $main->icono, APP_AJAX).'</li>'.PHP_EOL;
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
            $html.= '<ul class="sidebar-menu">'.PHP_EOL.'<li>'.MkcHtml::link('dashboard/','Dashboard',NULL,'dashboard', FALSE).'</li>'.PHP_EOL;
            foreach(self::$_main as $main) {
                $text = '<i class="fa fa-'.$main->icono.' fa-lg"></i>&nbsp;&nbsp;'.$main->menu.'<i class="fa fa-angle-left pull-right"></i>'.PHP_EOL;
                $html.= '<li class="treeview">';
                $html.= MkcHtml::link('#', $text, array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'), NULL, FALSE).PHP_EOL;
                if(array_key_exists($main->menu, self::$_items)) {
                    $html.= '<ul class="treeview-menu" style="position: relative;">'.PHP_EOL;
                    foreach(self::$_items[$main->menu] as $item) { 
                        if(!APP_OFFICE && $item->id == Menu::SUCURSAL) {
                            continue;
                        }
                        $active = ($item->url==$route) ? 'active' : null;
                        $text2 = '<i class="fa fa-'.$item->icono.'"></i>'.$item->menu.PHP_EOL;
                        $html.= '<li class="'.$active.'">'.MkcHtml::link($item->url, $text2, NULL, NULL, TRUE).'</li>'.PHP_EOL;
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
            $html.= '<div id="sub-menu-'.MkcUtils::getSlug($menu).'" class="subnav hidden">'.PHP_EOL;
            $html.= '<ul class="treview-menu nav nav-pills">'.PHP_EOL;
            if(array_key_exists($menu, self::$_items)) {
                foreach(self::$_items[$menu] as $item) {
                    if(!APP_OFFICE && $item->id == Menu::SUCURSAL) {
                        continue;
                    }
                    $active = ($item->url==$route or $item->url=='principal') ? 'active' : null;                    
                    $submenu = $item->getListadoSubmenuPorPerfil(self::$_entorno, self::$_perfil, $item->id);
                    if($submenu) {
                        $html.= '<li class="'.$active.'treview dropdown">';
                        $html.= MkcHtml::link($item->url, ' <i class="fa fa-'.$main->icono.'"></i>'.$item->menu, array('class'=>'dropdown-toggle', 'role'=>"button", "data-toggle"=>"dropdown"), $item->icono);                        
                        $html.= '<ul class="treview-menu dropdown-menu" role="menu">';
                        foreach($submenu as $tmp) {
                            $html.= '<li>'.MkcHtml::link($tmp->url, $tmp->menu, null, $tmp->icono).'</li>'.PHP_EOL;
                        }
                        $html.= '</ul>'.PHP_EOL;
                        $html.= '</li>'.PHP_EOL;
                    } else {
                        $html.= '<li class="'.$active.'">'.MkcHtml::link($item->url, $item->menu, null, $item->icono).'</li>'.PHP_EOL;
                    }                                        
                }
            }
            $html.= '</ul>'.PHP_EOL;
            $html.= '</div>'.PHP_EOL;
        }
        return $html;  
    }
    
}
