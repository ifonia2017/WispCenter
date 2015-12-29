<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de los recursos del sistema
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

class RecursoController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Recursos del sistema';
    }
    
    /**
     * Método principal
     */
    public function index() {
        MkcRedirect::toAction('listar');
    }
    
    /**
     * Método para listar
     */
    public function listar($order='order.modulo.asc', $page='pag.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $recurso = new Recurso();
        $this->recursos = $recurso->getListadoRecurso('todos', $order, $page);                
        $this->order = $order;        
        $this->page_title = 'Listado de recursos del sistema';
    }
    
    /**
     * Método para agregar
     */
    public function agregar() {
        if(Input::hasPost('recurso')) {
            if(Recurso::setRecurso('create', Input::post('recurso'), array('activo'=>Recurso::ACTIVO))){
                MkcMessage::valid('El recurso se ha registrado correctamente!');
                return MkcRedirect::toAction('listar');
            }          
        }
        $this->page_title = 'Agregar recurso';
    }
    
    /**
     * Método para editar
     */
    public function editar($key) {        
        if(!$id = MkcSecurity::isValidKey($key, 'upd_recurso', 'int')) {
            return MkcRedirect::toAction('listar');
        }
        
        $recurso = new Recurso();
        if(!$recurso->find_first($id)) {
            MkcMessage::get('id_no_found');
            return MkcRedirect::toAction('listar');
        }
        
        if($recurso->id <= 16) {
            MkcMessage::warning('Lo sentimos, pero este recurso no se puede editar.');
            return MkcRedirect::toAction('listar');
        }
        
        if(Input::hasPost('recurso')) {
            if(MkcSecurity::isValidKey(Input::post('recurso_id_key'), 'form_key')) {
                if(Recurso::setRecurso('update', Input::post('recurso'), array('id'>$id))){
                    MkcMessage::valid('El recurso se ha actualizado correctamente!');
                    return MkcRedirect::toAction('listar');
                }
            }
        }
            
        $this->recurso = $recurso;
        $this->page_title = 'Actualizar recurso';
        
    }
    
    /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key) {
        if(!$id = MkcSecurity::isValidKey($key, $tipo.'_recurso', 'int')) {
            return MkcRedirect::toAction('listar');
        }        
        
        $recurso = new Recurso();
        if(!$recurso->find_first($id)) {
            MkcMessage::get('id_no_found');            
        } else {
            if($recurso->id <= 16) {
                MkcMessage::warning('Lo sentimos, pero este recurso no se puede editar.');
                return MkcRedirect::toAction('listar');
            }
            if($tipo=='inactivar' && $recurso->activo == Recurso::INACTIVO) {
                MkcMessage::info('El recurso ya se encuentra inactivo');
            } else if($tipo=='reactivar' && $recurso->activo == Recurso::ACTIVO) {
                MkcMessage::info('El recurso ya se encuentra activo');
            } else {
                $estado = ($tipo=='inactivar') ? Recurso::INACTIVO : Recurso::ACTIVO;
                if(Recurso::setRecurso('update', $recurso->to_array(), array('id'=>$id, 'activo'=>$estado))){
                    ($estado==Recurso::ACTIVO) ? MkcMessage::valid('El recurso se ha reactivado correctamente!') : MkcMessage::valid('El recurso se ha inactivado correctamente!');
                }
            }                
        }
        
        return MkcRedirect::toAction('listar');
    }
    
    /**
     * Método para eliminar
     */
    public function eliminar($key) {         
        if(!$id = MkcSecurity::isValidKey($key, 'eliminar_recurso', 'int')) {
            return MkcRedirect::toAction('listar');
        }        
        
        $recurso = new Recurso();
        if(!$recurso->find_first($id)) {
            MkcMessage::get('id_no_found');
            return MkcRedirect::toAction('listar');
        }              
        try {
            if($recurso->delete()) {
                MkcMessage::valid('El recurso se ha eliminado correctamente!');
            } else {
                MkcMessage::warning('Lo sentimos, pero este recurso no se puede eliminar.');
            }
        } catch(KumbiaException $e) {
            MkcMessage::error('Este recurso no se puede eliminar porque se encuentra relacionado con otro registro.');
        }
        
        return MkcRedirect::toAction('listar');
    }
    
}

