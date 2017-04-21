<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    /* Todos los atributos publicos se alojan previamente en el arreglo
     * $this->_datos_vista y luego se trasladan a la vista 
     */
    
    /*
     * Id del controlador coincidente con modelo 
     * application/models/{$this->_id}_Model.php
     */
    public $_id;
    
    /*
     * Instancia del modelo descrito en 
     * /application/models/{$this->_id}_Model.php,
     * cuando es !== NULL el objeto, mantiene los datos conseguido desde la base
     */
    public $_modelo;
    
    /*
     * Array de modelos leidos desde la base de datos por el controlador
     */
    public $_modelos;
    
    /*
     * Modelos seleccionados a traves del control multisel[] de la vista
     */
    public $_seleccionados;
    
    /*
     *  Titulo del controlador
     */
    public $_titulo;
    
    // filtros para se arplicados en la consulta
    public $_filtros;
    public $_busqueda_rapida;
    
    // Accion actual: index,editar,eliminar,agregar,ver,elim_varios,edit_varios
    public $_accion = 'index';
    
    /* La pagina apuntada por la clase Pagination */
    public $_pag;
    
    /*
     * Los datos que se pasan a la vista
     */
    public $_datos_vista;
    
    /*
     * Los nombres de las vistas alojados en /application/views/$this->_id.
     * Asociados a cada accion de la forma:
     * array('nombre_accion' => 'nombre_formulario')
     */
    protected $_forms = array(
            'index'     => 'index',
            'agregar'   => 'form',
            'editar'    => 'form',
            'edit_varios' => 'edit_varios',
            'eliminar'  => 'eliminar',
            'elim_varios' => 'elim_varios',
            'ver'       => 'form',
        );
    
    protected $_datos_session = array(
        '_pag', 'per_pag', '_filtros', '_busqueda_rapida'
    );
    
    /*
     *  Los templates usados por $this->despachar_vista() que se encuentran 
     *  en la carpeta application/views/default/
     */
    protected $_templates = array('header'=>'header','footer'=>'footer');
    
    /*
     * La vista que va a ser despachada como parte de la accion
     */
    protected $_vista;
    
    // Lista de campos obtenidos desde la base coincide con SQL-Select 
    protected $_select = '*';
    
    // Coincide con la clausula SQL-Order
    protected $_order;
    
    // Coincide con la clausula SQL-Join
    protected $_join;
    
    // La lista de campos a los que se aplica la busqueda rapida
    protected $_campos_busqueda_rapida;
    
    /*
     * El/los roles que los usuarios deben tener asignados para ejecutar las
     * acciones del controlador. Si no se encuentra Lista de acciones definidas
     * para este controlador, por defecto, se autoriza a acceder a las acciones
     * 'index' y 'ver solamente.
     * Para inhibir cualquier acceso salvo al supervisor, hacer NULL este
     * atribtuto o asignarle $_rol = array('supervisor'),
     */
    protected $_rol = array('predeterminado');
    
    /*
     * Los permisos establecidos leidos desde las ACL (Lista de acciones 
     * permitidos) al usuario, leidas desde la base
     */
    protected $_permisos = array(
        'admin' => array(
            'index'         => TRUE,
            'agregar'       => TRUE,
            'editar'        => TRUE,
            'edit_varios'   => TRUE,
            'elim_varios'   => TRUE,
            'eliminar'      => TRUE,
            'ver'           => TRUE,
        ),
        'delegado' => array(
            'index'         => TRUE,
            'agregar'       => TRUE,
            'editar'        => TRUE,
            'edit_varios'   => TRUE,
            'elim_varios'   => TRUE,
            'eliminar'      => TRUE,
            'ver'           => TRUE,
        ),
        'usuario' => array(
            'index'         => TRUE,
            'agregar'       => TRUE,
            'editar'        => TRUE,
            'edit_varios'   => FALSE,
            'elim_varios'   => FALSE,
            'eliminar'      => FALSE,
            'ver'           => TRUE,
        ),
        'visitante' => array(
            'index'         => TRUE,
            'agregar'       => FALSE,
            'editar'        => FALSE,
            'edit_varios'   => FALSE,
            'elim_varios'   => FALSE,
            'eliminar'      => FALSE,
            'ver'           => TRUE,
        ),
    );
    
    public function __construct() {
        parent::__construct();
        $this->_id = get_class($this);
        $this->load->helper(array('form','url','html'));
        $this->load->library(['pagination','table','session','form_validation']);
        $this->load->database();
        $this->_titulo = get_class($this);
        $this->inicio();
    }
    
    /*
     * Método reservado al desarrollador de la nueva clase para configurar el controlador, por ejemplo configurar la
     * lista de datos que se obtiene en la acción index a través de las propiedades _select, _join, _order, etc
     */
    public function inicio() {
        
    }

    protected function index() {
        $this->_accion = 'index';
        $this->cargar_modelo();
        
        if ($this->input->method(TRUE) == 'POST') {
            if ($this->input->Post('submit[salir]') !== NULL) {
                $this->salir();
            }
            if ($this->input->Post('submit[agregar]') !== NULL) {
                $this->agregar();
            }
            if ($this->input->Post('submit[edit_varios]') !== NULL) {
                if ($this->input->Post('multisel[]') !== NULL) {
                    $this->varios($this->input->Post('multisel[]'));
                } else {
                    // eligio submit[edit_varios] pero no una lista para procesar
                    // Debe publicarse un error
                    return;
                }
            }
            if ($this->input->Post('submit[eliminar]') !== NULL) {
                $this->eliminar($this->input->Post('submit[eliminar][id]'));
            }
            if ($this->input->Post('submit[elim_varios]') !== NULL) {
                if ($this->input->Post('multisel[]') !== NULL) {
                    $this->elim_varios($this->input->Post('multisel[]'));
                } else {
                    // eligio submit[elim_varios] pero no una lista para procesar
                    // Debe publicarse un error
                    return;
                }
            }
        }
        
        // LLama a la accion del usuario
        $this->accion_index();

        if ( ! empty($this->_vista)) {
            $this->despachar_vista();
        }
    }
    
    protected function agregar() {
        $this->_accion = 'agregar';
        $this->cargar_modelo();
        
        $this->set_vista($this->_accion);
        
        // LLama a la accion del usuario
        $this->accion_agregar();
        
        if ( ! empty($this->_vista)) {
            $this->despachar_vista();
        }
    }
    
    protected function editar($id=NULL) {
        $this->_accion = 'editar';
        if ($this->input->method(TRUE) == 'POST') {

            if ($this->input->Post('submit[salir]')) {
                $this->salir();
                return;
            }
            if ($this->input->Post('submit[cancelar]')) {
                redirect($this->_id);
                return;
            }
            if ($this->input->Post('submit[eliminar]')) {
                if ($this->input->Post('id') != NULL) {
                    $this->cargar_modelo($this->input->Post('id'));
                }
                if ($this->input->Post('id[]') != NULL) {
                    $this->cargar_modelo($this->input->Post('id[]'));
                }
                $this->{$this->_id}->delete();
                redirect($this->_id);
                return;
            }
        }
        
        $this->set_vista($this->_accion);
        
        // LLama a la accion del usuario
        $this->accion_editar();
        
        if ( ! empty($this->_vista)) {
            $this->despachar_vista();
        }
    }
    
    protected function ver($id) {
        $this->_accion = 'ver';
        $this->cargar_modelo($id);
        
        $this->set_vista($this->_accion);
        
        // LLama a la accion del usuario
        $this->accion_ver();
        
        if ( ! empty($this->_vista)) {
            $this->despachar_vista();
        }
    }
    
    protected function edit_varios(array $varios) {
        $this->_accion = 'edit_varios';
        $this->cargar_modelo();
        
        $this->set_vista($this->_accion);
        
        // LLama a la accion del usuario
        $this->accion_edit_varios();
        
        if ( ! empty($this->_vista)) {
            $this->despachar_vista();
        }
        
    }
    
    protected function eliminar($id) {
        $this->_accion = 'eliminar';
        $this->cargar_modelo($id);
        $this->set_vista($this->_accion);
        
        // LLama a la accion del usuario
        $this->accion_eliminar();
        
        if ( ! empty($this->_vista)) {
            $this->despachar_vista();
        }
    }
    
    protected function elim_varios(array $varios) {
        $this->_accion = 'elim_varios';
        $this->cargar_modelo();
        $this->set_vista($this->_accion);
        
        // LLama a la accion del usuario
        $this->accion_elim_varios();
        
        if ( ! empty($this->_vista)) {
            $this->despachar_vista();
        } else {
            redirect($this->_id);
        }
        
    }
    
    protected function recuperar_lista() {
        $this->db->select($this->_select)
            ->from($this->_id);            

        if ($this->pagination->per_pag > 0) {
            $this->db->offset($this->_desde);
        }
        if ($this->_per_pag > 0) {
            $this->db->limit($this->_per_pag);
        }
        if ( ! empty($this->_order)) {
            $this->db->order_by($this->_order);
        }

        if ( ! empty($this->_join)) {
            if (is_array($this->_join)) {
                foreach($this->_join as $join) {
                    $this->db->join($join);
                }
            }
            if (is_string($this->_join)) {
                $this->db->join($this->_join);
            }
        }
        if ( ! empty($this->_campos_busqueda_rapida)
                && ! empty($this->_busqueda_rapida)) {
            $tmp = array();
            $this->db->group_start();
                if (is_array($this->_campos_busqueda_rapida)) {
                    $tmp = $this->_campos_busqueda_rapida;
                }
                if (is_string(($this->_campos_busqueda_rapida))) {
                    $tmp = explode(',', $this->_campos_busqueda_rapida);
                }
                foreach($tmp as $campo) {
                    $this->db->or_like(array($campo=>$this->_busqueda_rapida));
                }
            $this->db->group_end();
        }
        $this->_modelos = $this->db->result($this->_id.'_Model');
    }
    
    protected function cargar_modelo($id=NULL) {
        if ( empty($this->_modelo) || ! is_object($this->_modelo)) {
            $this->load->model($this->_id.'_Model', $this->_id);
        }
        if ( ! is_null($id)) {
            $this->_modelo =
                $this->{$this->_id}->get($id);
        }
    }
    
    protected function despachar_vista() {
        $this->load->view($this->_templates['header']);
        $this->load->view($this->_vista, $this->_datos_vista);
        $this->load->view($this->_templates['footer']);
        $this->_vista = NULL;
    }
    
    protected function empaquetar_datos() {
        $ref = new ReflectionClass($this);
        $props = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($props as $prop) {
            $nombre = $prop->getName();
            if ($nombre !== '_datos_vista') {
                $this->_datos_vista[$nombre] = $this->{$nombre};
            }
        }
    }
    
    protected function set_vista($vista) {
        $this->_vista = $vista;
    }
    
    public final function salir() {
        $this->session->unset_userdata();
        parent::salir();
        redirect();
        return;
    }
    
    /*
     * Estos métodos públicos (con prefijo accion_) se reservan para que el desarrollador de la clase, incluya su propio
     * código, para complementar el método homónimo protegido sin el prefijo _accion, el controlador llama a estos métodos
     * luego de ejecutar el método protgegido y previo a despachar la vista.
     */
    public function accion_index() {
        
    }
    
    public function accion_agregar() {
        
    }
    
    public function accion_ver() {
        
    }
    
    public function accion_edit_varios() {
        
    }
    
    public function accion_elim_varios() {
        
    }
    
    public function accion_eliminar() {
        
    }
    
}
