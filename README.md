# ci_controlador_abm
Una clase derivada de CI_Controller para CodeIgniter, especializada en operaciones ABM (CRUD). Esta clase debe ser alojada en cualquier  
proyecto CodeIgniter bajo la carpeta BASEPATH\application\core\MY_Controller.php.

Cada nuevo controlador que se cree en el proyecto, debe derivar de esta nueva clase:

    ejemplo: 
    class Nuevo_controlador extends MY_Controller {
        ...
    }

El nombre de la clase es importante, debe coincidir con el nombre de un modelo alojado en BASEPATH\models\Cualquier_modelo.php.
A su vez, este modelo debe dirivar de otra clase extendida alojada en BASEPATH\core\MY_Model.php, ambas clases colaboran entre si
para llevar a cabo las tareas de ABM (Altas,Bajas,Modificaciones) también llamadas CRUD por su sigla en ingles.

En forma predefinida, se encuentras las acciones "index,editar,edit_varios,agregar,eliminar,elim_varios" y obviamente se pueden crear
otras acciones en función a las nececidades de la aplicación.

Características:
    - Cualquier propiedad pública (public) creada en esta clase Nuevo_Controlador, es visible en la capa de la vista. En forma
      predeterminada, existen las siguiente propiedades públicas que se trasladan a la vista: _id, _titulo, _pag, _pagination, 
      _modelo, _modelos, _seleccionados, _filtros, _busqueda_rapida, _accion
      
    - Posee una propiedad $_datos_session, un array que enumera las propiedades del controlador que se mantienen en _SESSION, 
      en forma predeterminada, mantiene las siguientes propiedades de la clase en SESSION: _pag, _filtros, _busqueda_rapida
      
    - Se cargan los siguientes helpers: form, html, url
    
    - Se cargan las siguientes librerías: pagination , table , session, form_validatio

    - Procesa en forma predeterminada, cualquier formulario enviado con los siguientes comandos traidos desde el requerimiento:
      submit[agregar], submit[editar], submit[edit_varios], submit[eliminar], submit[elim_varios], submit[salir]
