<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga del logueo de los usuarios del sistema
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

Load::lib('mkc_security');

class LoginController extends BackendController {
    
    /**
     * Limite de parámetros por acción
     */
    public $limit_params = FALSE;
    
    /**
     * Nombre de la página
     */
    public $page_title = 'Entrar';
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        View::template('backend/login');
    }
    
    /**
     * Método principal     
     */
    public function index() {        
        return MkcRedirect::toAction('entrar/');
    }
    
    /**
     * Método para iniciar sesión
     */
    public function entrar() {        
        if(Input::hasPost('login') && Input::hasPost('password') && Input::hasPost('mode')) {
            if(Usuario::setSession('open', Input::post('login'), Input::post('password'))) {
                return MkcRedirect::to('dashboard/');
            }                       
        } else if(MkcAuth::isLogged()) {
            return MkcRedirect::to('dashboard/');
        }
    }
    
    /**
     * Método para cerrar sesión
     */
    public function salir($js='') {        
        if(Usuario::setSession('close')) {
            MkcMessage::valid("La sesión ha sido cerrada correctamente.");
        }
        if($js == 'no-script') {
            MkcMessage::info('Activa el uso de JavaScript en su navegador para poder continuar.');
        }        
        return MkcRedirect::toAction('entrar/');
    }
    
}

