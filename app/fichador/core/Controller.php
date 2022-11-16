<?php
namespace app\fichador\core;
use \app\fichador\models\{User};

/**
 * Clase controladora que devuelve una vista o devuelve datos
 * 
 */
class Controller
{
    private string $main_view;
    private string $folder_view;
    private string $folder_model;
    private string $ext_view;
    private array $env; 
    // Se declara la pagina de inicio de la aplicación
    
    function __construct(string $folder_view, string $folder_model, string $main_view, string $ext_view, array $env)
    {
        // Configuración variables de la aplicación
        $this->main_view = $main_view; 
        $this->folder_view = $folder_view;
        $this->folder_model = $folder_model;
        $this->ext_view = $ext_view;        
        $this->env = $env;
    }

    /**
     * Decide cual es la vista que hay que cargar
     */
    public function route($request = null): void
    {
        if ($request) {
            // Solicitud de entrada de datos
            if($_POST){
                // Fichando entrada o salida
                if(in_array("action",array_keys($request))){
                    if($request['action'] == 'singin'){
                        $user = new User($request['user']);
                        if($user->pass() === $request['pass']){
                            echo 'AUTORIZADO';
                        } else {
                            echo 'NO AUTORIZADO';
                        }
                    }
                }
            }else{
                var_dump('Solicita vista');
            }

            exit();
        }else{
            // Inicio de aplicación

            $this->load_view($this->main_view, $this->env); 
        }
    }

    /**
     * Carga la vista
     */
    public function load_view(string $view, $data = null): void
    {
        try {
            if ($data){
                foreach($data as $key => $value){
                    define($key, $value);
                }
            }
            $path = $this->folder_view . $view . '.' . $this->ext_view;
            include($path);
        } catch (\Exception $e) {
            echo 'No se ha encontrado la ruta: ',  $e->getMessage(), "\n";
        }
    }

    /**
     * Sale a la pagina inicial y destruimos la session actual
     */
    public function exit(): void
    {
        $this->auth = false;
        session_destroy();
    }
    /** 
     * GETTERS AND SETTERS
     */
    public function main_view(string $value = null){
        $name_fun = explode('::',__METHOD__)[1];
        if($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    public function ext_view(string $value = null){
        $name_fun = explode('::',__METHOD__)[1];
        if($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    public function folder_view(string $value = null){
        $name_fun = explode('::',__METHOD__)[1];
        if($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    function getAuth()
    {
        return $this->auth;
    }
}
