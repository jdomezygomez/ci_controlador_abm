<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('get_moneda_vigente')) {
    
    function get_moneda_vigente() {
        return 5;
    }
    
}

if ( ! function_exists('get_fecha_habil')) {
    
    function get_fecha_habil($fecha, $modificador=null) {

        if (is_string($fecha)) {
            $fecha = strtotime($fecha);
        }
        switch (date('w', $fecha)) {
            case (0): // Domingo
                if (is_null($modificador) || ! is_numeric($modificador) ||
                    $modificador == 0) {
                    $fecha = sumar_dias($fecha,1);
                }
                else if (is_numeric($modificador) && $modificador >=  1) {
                    $fecha = sumar_dias($fecha,1);
                }
                else if (is_numeric($modificador) && $modificador <= -1) {
                    $fecha = sumar_dias($fecha,-2);
                }
                break;
            case (6): // Sabado
                if (is_null($modificador) || ! is_numeric($modificador) ||
                    $modificador == 0) {
                    $fecha = sumar_dias($fecha,2);
                }
                else if (is_numeric($modificador) && $modificador >=  1) {
                    $fecha = sumar_dias($fecha,2);
                }
                else if (is_numeric($modificador) && $modificador <= -1) {
                    $fecha = sumar_dias($fecha,-1);
                }
                break;
        }

        // A la fecha obtenida, la someto a la tabla Feriado
        $ci =& get_instance();
        $ci->load('db');

        while($feriado = $ci->db->select('*')
            ->from('feriado')
            ->where(array('fericod' => sql_datetime($fecha)))
            ->get()
            ->row()) 
        {

            switch (date('w', strtotime($fecha))) {
                case (5): // Viernes
                    $fecha = sumar_dias($fecha,3);
                    break;
                case (6): // Sabado
                    $fecha = sumar_dias($fecha,2);
                    break;
                default:  // Resto de los dias incluido el domingo
                    $fecha = sumar_dias($fecha,1);
            }
        }

        return $fecha;

    }
    
}

if ( ! function_exists('es_dia_habil')) {
    
    // Esta funcion es identica a es_fecha_habil
    function es_dia_habil($fecha) {
        return es_fecha_habil($fecha);
    }
    
}
    
if ( ! function_exists('es_fecha_habil')) {
    
    function es_fecha_habil($fecha) {
        $result = FALSE;
        if (is_string($fecha)) {
            $fecha = strtotime($fecha);
        }
        switch (date('w', $fecha)) {
            case (0): // Domingo
            case (6): // Sabado
                $result = TRUE;
                break;
            default:
                $ci =& get_instance();
                $ci->load('db');
                $fila = $ci->db->where(array('fericod'=>sql_datetime($fecha)))
                        ->get('feriado')
                        ->row();
                $result = ($fila) ? TRUE : FALSE ;
        }
        return $result;
    }
    
}
    
if ( ! function_exists('cargar_ajuste')) {
    
    function cargar_ajuste($ajcod, $fecha=NULL) {
        $ci =& get_instance();
        $ci->load('db');
        
        $where = array('ajcod' => $ajcod);
        if ($fecha !== NULL) {
            //$where['ajanio']
        }
        
        return $ci->db
            ->select('ajcod,ajdsc,ajintermes,ajinterdia,ajtpo,ajaplinter,'
                    . 'ajanio,ajmes,ajinter,ajresult')
            ->where($where)
            ->get('ajuste');
    }
    
}

if ( ! function_exists('get_ajuste_factor')) {
    
    function get_ajuste_factor($ajuste=NULL,$vencimiento,$retorno=NULL) {
        
        if ($ajuste === NULL || ! is_object($ajuste)) {
            $ajuste = cargar_ajuste(1);
        }
        
        $anio = (int)substr($vencimiento, 0, 4);
        $mes  = (int)substr($vencimiento, 5, 2);
        foreach($ajuste->result() as $obj) {
            if ($obj->anio == $anio && $obj->mes == $mes) {
                $result = $obj->ajresult;
                break;
            }
        }
        /*
        $result = 1;
        if ($vencimiento < hoy()) {
            if (! array_key_exists('9999-99', $Aajuste)) {
                $indice = 1;
                $clave  = substr($vencimiento, 0, 7);
                if (array_key_exists($clave, $Aajuste)) {
                        if ($Aajuste['ajtpo'] == 'PORCENTUAL')
                                $indice = round($Aajuste[$clave]/100, 6);
                        if ($Aajuste['ajtpo'] == 'PRODUCTO')
                                $indice = round($Aajuste[$clave], 6);
                        $ultimo_dia =
                        udiames($Aajuste['ultimo_mes'].'-01');

                        if ($ultimo_dia < hoy()) {
                            // El Ultimo mes de ajuste de la tabla es menor
                            // a hoy(), correponde calcular el interes diario entre
                            // $ultimo_dia y hoy()
                            //$dias = utilSgrm::dias(hoy(), $ultimo_dias);
                            $dias = 0;
                        }
                } else {
                    // No encontro un indice mensual, ya que la la tabla
                    // de indices no abarca hasta ese mes, procede con el
                    // interes diario
                    $dias = dias(hoy(), $vencimiento);
                }
                if ($Aajuste['ajtpo'] == 'PORCENTUAL') {
                    $result = round($indice + 
                        ($dias * $Aajuste['ajinterdia']/100), 6);
                }
                if ($Aajuste['ajtpo'] == 'PRODUCTO') {
                    $result = round($indice + 
                        ($dias * $Aajuste['ajinterdia']), 6);
                }

            } else {
                // mantiene un porcentual anual, que sera dividido por 365
                // para obtener el porcentual de ajuste diario.
                $result = 1;
            }
        }
        */

        if (is_null($retorno) || is_string($retorno) &&
                strtolower($retorno) == 'porcentual') {
            $result = round(($result - 1) * 100, 6);
        }
        
        return $result;
    }
    
}

if ( ! function_exists('get_ajuste_valor')) {
    
    function get_ajuste_valor($Aajuste=NULL, $vencimiento, $importe) {
        if ($ajuste === NULL || ! is_object($ajuste)) {
            $ajuste = cargar_ajuste(1);
        }
        
        $porcentual = get_ajuste_factor($Aajuste, $vencimiento);
        $result     = round($importe * $porcentual/100, 2);
        return $result;
    }
    
}

if ( ! function_exists('get_deuda_ajustada')) {
    
    function get_deuda_ajustada($ajuste=NULL, $importe, $vencimiento) {
        if ($ajuste === NULL || ! is_object($ajuste)) {
            $ajuste = cargar_ajuste(1);
        }
        
        $ajuste_valor = get_ajuste_valor($ajuste, $vencimiento, $importe);
        $result = $importe + $ajuste_valor;
        return $result;
    }
    
}
    
if ( ! function_exists('get_valor_ajustado')) {
    
    function get_valor_ajustado($valor_nominal, $vencimiento, $ajuste=NULL) {
        if ($ajuste !== NULL || ! is_object($ajuste)) {
            $ajuste = cargar_ajuste(1);
        }
        
        $result = $valor_nominal;
        $anio = (int)substr($vencimiento, 0, 4);
        $mes  = (int)substr($vencimiento, 5, 2);
        foreach($ajuste->result() as $obj) {
            if ($obj->anio == $anio && $obj->mes == $mes) {
                $result = round($obj->ajresult * $valor_nominal, 2);
                break;
            }
        }
        
        return $result;
    }
    
}
    
if ( ! function_exists('array_str_cprb')) {
    
    function array_str_cprb($str_cprb) {
        $result     = array('tcprbcod' => 0, 'cprbnro' => 0);
        $posGuion   = 4;
        $posSerie   = 5;
        $posBarra   = 12;
        $posDigito  = strlen($str_cprb) - 1;
        $largoSerie = 7;
        $tcprb      = 0;
        $ncprb      = 0;
        if (strlen($str_cprb) > 4 && strlen($str_cprb) < 15) {
            $tcprb = substr($str_cprb,0,4);
            
            $tcprb = (int)$tcprb;

            // Pregunta si tiene el segundo separador en $str_cprb
            $pb = strpos('/', $str_cprb);
            if ($pb === FALSE) {
                // No esta el segundo separador (/) en $str_cprb
                $posBarra   = 0;
                $largoSerie = strlen($str_cprb) - $posSerie - 1;
            } else {
                $posBarra   = $pb;
                $largoSerie = strlen($str_cprb) - $posSerie - 1;
            }
            $ncprb = substr($str_cprb,$posSerie,$largoSerie);
            
            $ncprb = (int)$ncprb;
            $ncprb = ($ncprb*10) 
                + (int)(substr($str_cprb,$posDigito,1));
            $result   = array(
                'tcprbcod'  => $tcprb,
                'cprbnro'   => $ncprb
            );
        }

        return $result;
    }
    
}
    
if ( ! function_exists('calcular_total_ajustado')) {
    
    function calcular_total_ajustado(
        $monto, $fv, $ajCodCprb, $ajCodTcprb, $ajCodCpag, $mndCod, 
        $ajustes=NULL)
    {
        $elAjuste  = $ajCodCpag;
        $resultado = $monto;
        if ($ajCodCprb != 0) {
            $elAjuste = $ajCodCprb;
        }
        elseif ($ajCodTcprb != 0) {
            $elAjuste = $ajCodTcprb;
        }
        else {
            $elAjuste = $ajCodCpag;
        }

        if (is_array($ajustes)) {
            $tmpAjuste = $ajustes;
        }
        if (empty($ajustes) || $ajustes['ajcod'] != $elAjuste) {
            $tmpAjuste = cargar_ajuste($elAjuste);
        }
        $resultado = get_deuda_ajustada($tmpAjuste, $fv, $monto);
        if ($mndCod != get_moneda_vigente()) {
            $resultado = get_moneda_convertida($mndCod, $resultado);
        }
        
        return $resultado;
    }
    
}
    
if ( ! function_exists('es_una_infraccion')) {
    
    function es_una_infraccion($tcprbcod, $cprbnro) {
        $result = array();
        $ci = get_instance();
        $ci->load('db');
        $where = array(
            'actatcprbc' => $tcprbcod, 
            'actacprbnr' => $cprbnro
        );
        return $ci->db->where($where)
                ->as_array()
                ->get('actas')
                ->row();
        /*
        $acta_Modelo = TablaModeloBase::nuevaInstancia('actas');
        $filtro = array('actatcprbc'=>$tcprbcod, 'actacprbnr'=>$cprbnro);
        $acta_Modelo->setFiltro($filtro);
        $acta_Datos  = $acta_Modelo->select();
        if (!empty($acta_Datos)) {
            $result = $acta_Datos[0];
        }
        */
        
        //return $result;
    }
    
}
    
if ( ! function_exists('get_moneda_convertida')) {
    
    function get_moneda_convertida($mndCod, $monto) {
        $ci =& get_instance();
        $ci->load('db');
        $resultado = $monto;
        
        $moneda = $ci->db->select('mndoper,mndfactor')
            ->from('moneda')
            ->where(array('mndcod' => strtoupper($prmid)))
            ->row();

        if (! empty($moneda)) {
            if ($moneda->mndoper == 'MULTIPLO') {
                $resultado = round($monto * $moneda->mndfactor, 2);
            }
            if ($moneda->mndoper == 'COSCIENTE') {
                $resultado = round($monto / $moneda->mndfactor, 2);
            }
            if ($resultado == 0) {
                $resultado = .01;
            }
        }

        return $resultado;
    }
    
}

if ( ! function_exists('get_parametro')) {
    
    function get_parametro($prmcod, $predeterminado=NULL) {
    
        $ci =& get_instance();
        $ci->load('db');
        $result = $predeterminado;
        $param = $ci->db->from('params')
                ->where(array('prmcod' => strtoupper($prmcod)))
                ->select('prmval')
                ->row();
        if (! empty($param)) {
            $result = $param->prmval;
        }

        return $result;
    }
    
}

if ( ! function_exists('get_ultimo_numero')) {
    
    function get_ultimo_numero($numerador) {
        $ci =& get_instance();
        $ci->load('db');
        $fila = $ci->db->get('unuunu')
                ->from('unumero')
                ->where(array('unucod' => strtoupper($numerador)))
                ->row();
        
        return (! empty($fila)) ? $fila->ununu : 0;
    }
    
}

if ( ! function_exists('reservar_numero')) {
    
    function reservar_numero($numerador) {
        return solicitar_numero($numerador);
    }
    
}

if ( ! function_exists('solicitar_numero')) {
    
    function solicitar_numero($numerador) {
        $ci =& get_instance();
        $ci->load('db');
        $db = $ci->db;
        $result = 1;
        $db->set('unuunu', 'unuunu+unuinter', FALSE);
        $db->set('unuufch', sql_datetime(hoy()));
        $db->update('unumero');
        if ($db->affected_rows() !== 1) {
            $fila = $db->select('unuunu')
                    ->from('unumero')
                    ->where(array('unucod' => strtoupper($numerador)))
                    ->get();
            $result = $fila->ununu;
        } else {
            $datos = array(
                'unucod'    => strtoupper($numerador),
                'unudsc'    => strtoupper($numerador),
                'unuinic'   => 1,
                'unuinter'  => 1,
                'unuunu'    => 1,
                'unuufch'   => sql_datetime(hoy()),
            );
            $db->insert('unumero', $datos);
            $result = $db->affected_rows();
        }

        return $result;
    }
    
}

if ( ! function_exists('idv_cprb')) {
    
    function idv_cprb($tcomprob, $ncomprob, $calculo=null) {
        $cadena  = str_pad((int)$tcomprob, 4, "0", STR_PAD_LEFT);
        $cadena .= str_pad((int)$ncomprob, 7, "0", STR_PAD_LEFT);
        $dv      = dv($cadena, $calculo); 
        $result  = $ncomprob * 10 + strval($dv);
        return $result;
    }
    
}

if ( ! function_exists('str_domicilio')) {
    
    // Utilidades para formar cadenas en diferentes formas
    function str_domicilio($calle, $nro=NULL,
        $edif=NULL, $piso=NULL, $dpto=NULL, $otro=NULL) 
    {
			
        $result = trim($calle);
        if ($nro > 0) 
            {$result .= " ";      $result .= trim($nro);}
        if (! $edif) 
            {$result .= " EDIF "; $result .= trim($edif);}
        if (! $piso) 
            {$result .= " PISO "; $result .= trim($piso);}
        if (! $dpto)
            {$result .= " DPTO "; $result .= trim($dpto);}
        if (! $otro)
            {$result .= " ";      $result .= trim($otro);}

        return $result;
    }
    
}
		
if ( ! function_exists('str_ciudad')) {
    
    function str_ciudad($cp, $localidad, $pcia) {
        $result  = "(";
        $result .= trim($cp);
        $result .= ") ";
        $result .= trim($localidad);
        $result .= " - ";
        $result .= trim($pcia);
			
        return $result;
    }
    
}
		
if ( ! function_exists('str_arranque')) {
    
    function str_arranque($calle1, $arranque, $calle2) {
        $result = "";
        if (!$calle1) {
            $result = "INEXISTENTE";
        }
        else {
            $result = trim($calle1);
            if ($arranque > 0.0) {
                $result .= " a ";
                $result .= trim(sprintf('%12.2f', strval($arranque)));
                $result .= "m. de ";
                $result .= trim($calle2);
            } else {
                $result .= " ESQ. ";
                $result .= trim($calle2);
            }
        }
        return $result;
    }
    
}
		
if ( ! function_exists('str_cprb')) {
    
    function str_cprb($tcomprob, $ncomprob, $sep1='-', $sep2='/') {
        $result  = str_pad($tcomprob, 4, "0", STR_PAD_LEFT);
        $result .= $sep1;
        $result .= str_pad((int)($ncomprob/10), 7, "0", STR_PAD_LEFT);
        $result .= $sep2;
        $result .= str_pad((int)($ncomprob%10), 1, "0", STR_PAD_LEFT);

        return $result;
    }
    
}
    
if ( ! function_exists('str_cod_bar')) {
    
    /**
     *  Devuelve el codigo de barras del comprobante, si el comprobante esta
     *  autorizado para ser cobrado bajo SAM 2000, devuelve este codigo
     *  de barras, de lo contrario, devuelve el codigo de barras interno dado
     *  por la funcion get_cod_bar_interno().
     *
     *  @param mixed $datos
     *
     *  @return string
     */
    function str_cod_bar($datos)
    {

        $empresa  = 0;
        $convenio = 0;
        $relleno  = '000';
        // La longitud de la mascara para importes
        $largoMasc= 6;
        $result   = '';
        $cantVencimientos = 3;
        $vencimientoVacio = '0000000000';

        if (is_object($datos)) {
            $tcprbcod     = $datos->tcprbcod;
            $cprbnro      = $datos->cprbnro;
            $cprbper      = $datos->cprbper;
            $cprbtotal    = $datos->cprbtotal;
            $vencimientos = $datos->vencimientos;
        }
        else {
            $tcprbcod     = $datos['tcprbcod'];
            $cprbnro      = $datos['cprbnro'];
            $cprbper      = $datos['cprbper'];
            $cprbtotal    = $datos['cprbtotal'];
            $vencimientos = $datos['vencimientos'];
        }

        switch ($tcprbcod) {
        case 1:
        case 5:
        case 6:
        case 7:
        case 21:
        case 105:
        case 106:
        case 107:
        case 108:
        case 9915:
        case 9919:
        case 9921:
        case 9940:
        case 9954:
        case 9955:
        case 9958:

            if (($tcprbcod == 9940 || $tcprbcod == 9954 || $tcprbcod == 9958) 
                && $trbcod == 3)
            {

                $empresa          = 666;
                $convenio         = 512;
                $cantVencimientos = 2;

            }
            else {

                $empresa  = 666;
                $convenio = 2020;
                // Para los comprobantes de DRI el caracter 27 debe valer '1'
                // y las mascaras tienen una longitud de hasta 10 digitos,
                // ya que solo tienen hasta dos vencimientos.
                if ($tcprbcod==5 || $tcprbcod==6 || $tcprbcod==7) {
                    $relleno            = '100';
                    $largoMasc          = 10;
                    $cantVencimientos   = 2;
                    $vencimientoVacio   = '00000000000000';
                }

            }
            break;

        case 20: case 9916:

            $empresa  = 666;
            $convenio = 512;
            break;

        default:

            if ($cprbtotal > 0)
            	return str_cod_bar_interno($tcprbcod, $cprbnro);
            else
                return '';

        } // switch( $tcomprob )

        $okSam2000 = TRUE;
        $result  = str_pad($empresa,          3, "0", STR_PAD_LEFT);
        $result .= str_pad($convenio,         4, "0", STR_PAD_LEFT);
        // El código de moneda
        $result .= "1";
        $result .= str_pad($tcprbcod,4, "0", STR_PAD_LEFT);
        $result .= str_pad($cprbnro, 8, "0", STR_PAD_LEFT);
        $result .= str_pad($cprbper, 6, "0", STR_PAD_LEFT);
        // relleno
        $result .= $relleno;
        
        // Los vencimientos
        for ($i=0; $i < count($vencimientos); $i++) {
            if ($vencimientos[$i]['cprbfvtot'] <= 0) {
                // Puede ser una nota de credito con valores negativos.
                $okSam2000 = FALSE;
                break;
            }
            if ($i >= $cantVencimientos) // Limita la cantidad de vencimientos
                break;
            if ($trbcod != 2
                && $vencimientos[$i]['cprbfvtot'] > 9999.99) {
                $okSam2000 = FALSE;
                break;
            }
            
            $dia = str_fecha_sam2000($vencimientos[$i]['cprbfv1']);
            $importe = $vencimientos[$i]['cprbfvtot'];
            $valor   = trim(number_format(round($importe,2)*100, 0, '.', ''));
            
            $result .= str_pad($dia, 4, "0", STR_PAD_LEFT);
            $result .= str_pad($valor, $largoMasc, '0', STR_PAD_LEFT);
            
        }
        for ($z=$i; $z<$cantVencimientos; $z++) {
            $result .= $vencimientoVacio;
        }
        if ( ! $okSam2000) {
            // Alguno de los importes supera los limites de Sam2000, establece
            // el codigo de barra interno
            
            if ($cprbtotal > 0)
            	return str_cod_bar_interno($tcprbcod, $cprbnro);
            else
                return '';
        }
        
        // La ultima etapa del codigo de barras.
        for ($z=strlen($result); $z<59; $z++)
            $result .= '0';
            
        // El DV extraido de la posicion nro 20 alojado aqui (nro 60)
        $result .= substr($result,19,1);

        return $result;
    }

}
		
if ( ! function_exists('str_cod_bar_interno')) {
    
    function str_cod_bar_interno($tcomprob, $ncomprob) {

        $result  = str_pad($tcomprob, 4, "0", STR_PAD_LEFT);
        $result .= str_pad($ncomprob, 8, "0", STR_PAD_LEFT);

        return $result;

    }
    
}

if ( ! function_exists('str_fecha_sam2000')) {
    
    function str_fecha_sam2000($fecha) {
        $result = 0;
        $momento= $fecha;
        if (is_string($fecha)) {
            $momento= strtotime($fecha);
        }
        if ($momento>0) {
            $anio    = date("y", $momento) % 10;
            $result  = str_pad($anio, 1, "0", STR_PAD_LEFT);
            $result .= str_pad(date("z",$momento)+1, 3, "0", STR_PAD_LEFT);
        }

        return $result;
    }
    
}
		
if ( ! function_exists('encriptar')) {
    
    function encriptar($cadena) {
        $tabla1[0]="A";
        $tabla1[1]="B";
        $tabla1[2]="C";
        $tabla1[3]="D";
        $tabla1[4]="E";
        $tabla1[5]="F";
        $tabla1[6]="G";
        $tabla1[7]="H";
        $tabla1[8]="I";
        $tabla1[9]="J";
        $tabla1[10]="K";
        $tabla1[11]="L";
        $tabla1[12]="M";
        $tabla1[13]="N";
        $tabla1[14]="O";
        $tabla1[15]="P";
        $tabla1[16]="Q";
        $tabla1[17]="R";
        $tabla1[18]="S";
        $tabla1[19]="T";
        $tabla1[20]="U";
        $tabla1[21]="V";
        $tabla1[22]="W";
        $tabla1[23]="X";
        $tabla1[24]="Y";
        $tabla1[25]="Z";
        $tabla1[26]="0";
        $tabla1[27]="1";
        $tabla1[28]="2";
        $tabla1[29]="3";
        $tabla1[30]="4";
        $tabla1[31]="5";
        $tabla1[32]="6";
        $tabla1[33]="7";
        $tabla1[34]="8";
        $tabla1[35]="9";
        $tabla1[36]="�";

        $tabla2[0]="T";
        $tabla2[1]="J";
        $tabla2[2]="H";
        $tabla2[3]="9";
        $tabla2[4]="S";
        $tabla2[5]="1";
        $tabla2[6]="N";
        $tabla2[7]="Z";
        $tabla2[8]="Q";
        $tabla2[9]="7";
        $tabla2[10]="G";
        $tabla2[11]="Y";
        $tabla2[12]="5";
        $tabla2[13]="M";
        $tabla2[14]="0";
        $tabla2[15]="C";
        $tabla2[16]="F";
        $tabla2[17]="R";
        $tabla2[18]="X";
        $tabla2[19]="8";
        $tabla2[20]="I";
        $tabla2[21]="4";
        $tabla2[22]="U";
        $tabla2[23]="L";
        $tabla2[24]="A";
        $tabla2[25]="6";
        $tabla2[26]="E";
        $tabla2[27]="3";
        $tabla2[28]="�";
        $tabla2[29]="9";
        $tabla2[30]="V";
        $tabla2[31]="O";
        $tabla2[32]="2";
        $tabla2[33]="D";
        $tabla2[34]="P";
        $tabla2[35]="K";
        $tabla1[36]="B";
        /*
            Posee un error en el programa original
            ya que es incorrecta la instruccion anterior
            $tabla1[36]="B"; deberia decir $tabla2[36]="B"
            Se soluciona de la
            siguiente manera --VER LA LINEA DE ABAJO
        */
        $tabla2[36]="";

        $result = '';
        for ($i=0; $i < strlen($cadena); $i++) {
            $car = strtoupper(substr($cadena,$i,1));
            for ($j=0; $j < count($tabla1); $j++) {
                if ($tabla1[$j]==$car) {
                    // para hacer un poco mas dificil el hackeo.... :p
                    //If &J+len(trim(&ClaveIn)) > 37
                    if ($j+$i+1 > 36)
                        $x = $j-$i-1;//+1;
                    else
                        $x = $j+$i+1;
                    //&ClaveOut=trim(&ClaveOut)+$tabla2[&xInd)
                    $result .= $tabla2[$x];
                    break;
                }
            }
        }
        
        return $result;
    }
    
}
    
if ( ! function_exists('leyenda_link_pagos')) {
    
    function leyenda_link_pagos($trbCod, $cliCod, 
            $prefijo = 'CODIGO DE LINK PAGOS:') 
    {
        $result = '';
        
        if (is_numeric($trbCod) 
            /*&& is_numeric($cliCod)*/
            && ($trbCod == 1 || $trbCod == 3)) 
        {
            
            $result  = trim($prefijo, ' ') . ' ';
    		$result .= str_pad(trim($trbCod), 4, '0', STR_PAD_LEFT);
            $result .= str_pad(trim($cliCod), 8, '0', STR_PAD_LEFT);
        }
        
        return $result;
    }
    
}
    