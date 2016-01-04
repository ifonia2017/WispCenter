<?php
/**
* Dailyscript - Web | App | Media
*
* Descripcion: Controlador para el panel principal de los usuarios logueados
*
* @category    
* @package     Controllers 
* @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
* @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
*/

class IndexController extends BackendController {

    public $page_title = 'Dashboard';

    public $page_module = 'Dashboard';

    public function index() {

        if (Session::get('perfil_id') != Perfil::SUPER_USUARIO) {
            if (Session::get('perfil_id') != Perfil::OPERADOR) {
                   MkcRedirect::to('dashboard/cliente');
            } else {
                MkcRedirect::to('dashboard/operador');
            }
        } else {
            MkcRedirect::to('dashboard/admin');
        }
    }

}
