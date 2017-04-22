<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('hoy')) {
    
    function hoy($forma=null) {
        $result = '';
        if (is_null($forma))
            $result = date('Y-m-d') . ' 00:00:00';
        else
            $result = date($forma);
        return $result;
    }
    
}

if ( ! function_exists('hora')) {
    
    function hora($forma=null) {
        $result = '';
        if (is_null($forma))
            $result = date('H:i:s');
        else
            $result = date($forma);
        return $result;
    }
    
}

if ( ! function_exists('ahora')) {
    
    function ahora($forma=null) {
        $result = '';
        if (is_null($forma))
            $result = date('Y-m-d H:i:s');
        else
            $result = date($forma);
        return $result;
    }
    
}

if ( ! function_exists('anteayer')) {
    
    function anteayer($forma=null) {
        $result = sumarDias(hoy(), -2);
        return $result;
    }

}
if ( ! function_exists('ayer')) {
    
    function ayer($forma=null) {
        $f = strtotime(hoy('Y-m-d') . '00:00:00');
        $f--;
        if (is_null($forma))
            $result = date('Y-m-d', $f);
        else
            $result = date($forma,  $f);
        return $result;
    }
    
}

if ( ! function_exists('mañana')) {
    
    /**
     * Calcula una nueva fecha, sumándole un día a la fecha actual, utiliza la
     * función sumarDias(hoy(),1).
     * 
     * @return string
     *      La nueva fecha en formato Y-m-d.
     */
    function mañana($forma=null) {
        $f = strtotime(hoy('Y-m-d') . '23:59:59');
        $f++;
        if (is_null($forma))
            $result = date('Y-m-d', $f);
        else
            $result = date($forma,  $f);
        return $result;
    }
    
}

if ( ! function_exists('pasado_mañana')) {
    
    /**
     * Calcula una nueva fecha, sumándole dos días a la fecha actual, utiliza la
     * función sumarDias(hoy(),2).
     * 
     * @return string
     *      La nueva fecha en formato Y-m-d.
     */
    function pasado_mañana() {
        $result = sumarDias(hoy(), 2);
        return $result;
    }
    
}

if ( ! function_exists('dia')) {
    
    function dia($fecha=NULL) {
        if (empty($fecha))
            $fecha = hoy();
        return (int)date('d', strtotime($fecha));
    }
    
}

if ( ! function_exists('dias')) {
    
    /**
     * Calcula la diferencia en días entre la $fechaMayor y la $fechaMenor, se
     * condice con la forma de cálculo $fechaMayor - $fechaMenor.
     * 
     * @param string $fechaMayor
     *      La fecha mayor en formato Y-m-d
     * @param string $fechaMenor
     *      La fecha menor en formato Y-m-d
     * @return int
     */
    function dias($fechaMayor, $fechaMenor) {
        $f2   = strtotime($fechaMayor);
        $f1   = strtotime($fechaMenor);
        $dif  = (int)(($f2 - $f1) / 86400);
        return $dif;
    }
    
}

if ( ! function_exists('sumar_dias')) {
    
    /**
     * Calcula una nueva fecha a partir de sumar al parámetro $fecha los n días
     * dados en el parámetro $dias.
     *
     * @param string $fecha
     *      Fecha en formato Y-m-d a la cual se sumarán los $dias.
     * @param int $dias
     *      La cantidad de días (positivos o negativos) que hay que sumar a la fecha
     *      para obtener el nuevo resultado. Valor predeterminado $dias=1.
     * @return string
     *      El resultado en formato Y-m-d.
     */
    function sumar_dias($fecha, $dias=1) {
        $f = strtotime($fecha);
        if ($dias>0)
            $nuevaFecha = strtotime("+$dias days", $f);
        else
            $nuevaFecha = strtotime("-$dias days", $f);
        //echo $nueva_fecha, " ";
        return date('Y-m-d', $nuevaFecha);
    }
    
}

if ( ! function_exists('sumar_meses')) {
    
    /**
     * Calcula una nueva fecha a partir de sumar al parámetro $fecha los n meses
     * dados en el parámetro $meses.
     *
     * @param string $fecha
     *      Fecha en formato Y-m-d a la cual se sumarán los $dias.
     * @param int $meses
     *      Cantidad de meses (positivos o negativos) que hay que sumar a la fecha
     *      para obtener el nuevo resultado. Valor predeterminado $meses=1.
     * @return string
     *      El resultado en formato Y-m-d.
     */
    function sumar_meses($fecha, $meses=1) {
        $f = strtotime($fecha);
        if ($meses>0)
            $nuevaFecha = strtotime("+$meses month", $f);
        else
            $nuevaFecha = strtotime("-$meses month", $f);
        //echo $nueva_fecha, " ";
        return date('Y-m-d', $nuevaFecha);
    }
    
}

if ( ! function_exists('pdm')) {
    
    /**
     * Retorna el primer día del mes de la fecha dada en formato Y-m-d por el 
     * parámetro $fecha.
     * 
     * @param string $fecha
     *      Fecha en formato Y-m-d, si no se suministra, se utiliza la función
     *      hoy() para establecer la fecha.
     * @return string
     *      La fecha del primer día del mes en formato Y-m-d
     */
    function pdm($fecha=null) {
        // Retrona el primer dia del mes segun la fecha dada
        if (is_null($fecha)) $fecha = hoy();
        return substr($fecha, 0, 7) . '-01';
    }

}
if ( ! function_exists('udm')) {
    
    /**
     * Retorna el último día del mes de la fecha dada en formato Y-m-d por el
     * parámetro $fecha.
     *
     * @param string $fecha
     *      Fecha en formato Y-m-d, si no se suministra, se utiliza la función
     *      hoy() para establecer la fecha.
     * @return string
     *      La fecha del último día del mes en formato Y-m-d
     */
    function udm($fecha=null) {
        //funcion sinonimo de udiames()
        if (is_null($fecha)) $fecha = hoy();
        $f      = strtotime($fecha);
        $uanio  = date('Y', $f);
        $umes   = date('m', $f);
        $umes++;
        if ($umes > 12) {
            $umes = '01';
            $uanio++;
        }
        $udiames= strtotime(sprintf("%s-%s-%s 00:00:00", $uanio, $umes, '01'));
        $udiames--;
        $result = date('Y-m-d', $udiames);
        return $result;
    }
    
}

if ( ! function_exists('udiames')) {

    /*
     * Esta funcion es identica a udm()
     */
    function udiames($fecha=NULL) {
        
        return udm($fecha);
        
    }
    
}

if ( ! function_exists('pda')) {
    
    /**
     * Retorna el primer día del año de la fecha dada en formato Y-m-d por el 
     * parámetro $fecha.
     * 
     * @param string $fecha
     *      Fecha en formato Y-m-d, si no se suministra, se utiliza la función
     *      hoy() para establecer la fecha.
     * @return string
     *      La fecha del primer día del año en formato Y-m-d
     */
    function pda($fecha=null) {
        if (is_null($fecha))
            $fecha = hoy();
        return date('Y', strtotime($fecha)) . '-01-01';
    }
    
}

if ( ! function_exists('uda')) {
    
    /**
     * Retorna el primer última del año de la fecha dada en formato Y-m-d por el 
     * parámetro $fecha.
     * 
     * @param string $fecha
     *      Fecha en formato Y-m-d, si no se suministra, se utiliza la función
     *      hoy() para establecer la fecha.
     * @return string
     *      La fecha del último día del año en formato Y-m-d
     */
    function uda($fecha=null) {
        if (is_null($fecha))
            $fecha = hoy();
        return date('Y', strtotime($fecha)) . '-12-31';
    }
    
}

if ( ! function_exists('es_fecha_nula')) {
    
    /**
     * Evalúa si la fecha es nula o sea anterior a 1970-01-01 00:00:00
     * 
     * @param date $fecha
     *      Fecha en formato Y-m-d.
     */
    function es_fecha_nula($fecha) {
        $resultado = FALSE;
        //$strFecha = date('Y-m-d H:i:s', strtotime($fecha));
        if (empty($fecha) || is_string($fecha) && substr($fecha, 0, 4) == '1969')
            $resultado = TRUE;
        return $resultado;
    }
    
}

if ( ! function_exists('ultimatum')) {
    
    /**
     * Devuelve una fecha en formato Y-m-d H:i:s que se obtiene de sumar al momento 
     * actual la cantidad de milisegundos dados en $delta. Es util para guardar
     * en la sesion un momento limite, se puede utilizar para establecer un 
     * vencimiento para los valores mantenidos en la sesion.
     * 
     * @param int $delta=60000
     *      La cantidad de milisegundos transcurridos a partir del 
     *      momento actual, por defecto diez minutos
     * 
     * @return string 
     *      Description El nuevo momento surgido luego de agregar los milisegundos
     *      dados en $delta a partir del momento actual.
     */
    function ultimatum($delta=60000) {
        $resultado = ahora();
        return date('Y-m-d H:i:s', strtotime($resultado)+$delta);
    }
    
}

if ( ! function_exists('fecha_larga')) {
    
    /**
     * 
     * @param string $fecha
     *      Fecha en formato Y-m-d, si no se suministra, se utiliza la funcion
     *      hoy() para establecer la fecha.
     * @param string $formato
     *      En forma predeterminada se publica en forma completa el nombre de los
     *      dias y de los meses. Si este parametro posee el valor 'abrev|abreviado'
     *      solamente se utilizan los primeros tres caracteres del dia y del mes.
     * @param string $leng
     *      Lenguaje, en forma predeterminada equivale a 'es' (español). Otros 
     *      lenguajes disponibles son 'en' '
     * @return string
     *      Una cadena con la forma predeterminada por el param $leng='es':
     *       'cadenaDia' nDia de 'cadenaMes' de nAnio 
     *      
     *      Ejemplos : Domingo 23 de Julio de 2014, si $forma=['abrev|abreviado']
     *          Dom 23 de Jul de 2014
     */
    function fecha_larga($fecha=null, $formato='completo', $leng='es') {
        if (is_null($fecha)) {
            $fecha = hoy();
        }
        $ndia = date('j', strtotime($fecha));
        $ndia_semana = date('w', strtotime($fecha));
        $nmes = date('m', strtotime($fecha));
        switch ($leng) {
        case 'es':
            $conector = ' de ';
            switch ($ndia_semana) {
            case 0:
                $dia_semana = 'Domingo';
                break;
            case 1:
                $dia_semana = 'Lunes';
                break;
            case 2:
                $dia_semana = 'Martes';
                break;
            case 3:
                $dia_semana = 'Miercoles';
                break;
            case 4:
                $dia_semana = 'Jueves';
                break;
            case 5:
                $dia_semana = 'Viernes';
                break;
            case 6:
                $dia_semana = 'Sabado';
                break;
            }
            switch ($nmes) {
            case 1:
                $mes = 'Enero';
                break;
            case 2:
                $mes = 'Febrero';
                break;
            case 3:
                $mes = 'Marzo';
                break;
            case 4:
                $mes = 'Abril';
                break;
            case 5:
                $mes = 'Mayo';
                break;
            case 6:
                $mes = 'Junio';
                break;
            case 7:
                $mes = 'Julio';
                break;
            case 8:
                $mes = 'Agosto';
                break;
            case 9:
                $mes = 'Setiembre';
                break;
            case 10:
                $mes = 'Octubre';
                break;
            case 11:
                $mes = 'Noviembre';
                break;
            case 12:
                $mes = 'Diciembre';
                break;
            }
            break;
        }
        if  (!is_null($formato)
            && (
                    stripos('abrev',$formato) !== FALSE
                        || stripos('abreviado',$formato) !== FALSE
                    )
            )
        {
            $dia_semana = substr($dia_semana, 0, 3);
            $mes = substr($mes, 0, 3);
        }
        return $dia_semana 
                . ' ' . $ndia . $conector . $mes 
                . $conector . date('Y', strtotime($fecha));
    }
    
}

if ( ! function_exists('sql_datetime')) {
    
    function sql_datetime($valor) {
        // configuracion
        $entorno = 'mssql'; //'predeterminado'; 'linux'; 'unix'
        $momento = strtotime($valor);
        if (strtolower($entorno) != 'mssql') {

            $frmFecha = 'Y-m-d H:i:s';
            $result   = date($frmFecha, $momento) . ' ';

        }
        if (strtolower($entorno) == 'mssql') {

            $frmFecha = 'd/m/Y H:i:s';
            $fecha    = date($frmFecha, $momento);
            $result   = "Cast('$fecha' as datetime) ";

        }
        // fin configuracion

        return $result;
    }
    
}

if ( ! function_exists('imprimir_fecha')) {
    
    function imprimir_fecha($fecha, $patron='d/m/Y') {
        $momento = strtotime($fecha);
        return date($patron, $fecha);
    }
    
}
  
if ( ! function_exists('validar_cbu')) {
    
    function validar_cbu($cbu) {
        $result = true;
        if (!isset($cbu) || !is_numeric($cbu) || 
            substr(strval($cbu),0,5) == '00000' || 
            strlen(trim(strval($cbu))) <> 22)
            $result = false;
        return result;
    }
    
}

if ( ! function_exists('validar_cuit')) {
    
    function validarCuit($cuit) {
        $result = true;
        if (!isset($cuit) 
            || !is_numeric($cuit) 
            || strlen($cuit) != 11 
            || (substr($cuit,0,2) != 20 
                && substr($cuit,0,2) != 23    
                && substr($cuit,0,2) != 24 
                && substr($cuit,0,2) != 27 
                && substr($cuit,0,2) != 30 
                && substr($cuit,0,2) != 33))

            $result = false;

        else {
            //           20160925125
            $nummagic = '5432765432';
            $suma     = 0;
            for ($i = 0; $i <= 9; $i++) {
                $suma += (int)substr($nummagic,$i,1) * (int)substr($cuit,$i,1);
            }
            $dv = (11 - ($suma % 11)) % 11;
            if (substr($cuit,10,1) != $dv)
                $result = false;
        }
        return $result;
    }
    
}

if ( ! function_exists('caso_imput')) {
    
    function caso_imput($caso_imput) {
        $result = null;
        if (stripos('d;deb;debito;', trim($caso_imput).';') !== false)
            $result = 'debito';
        if (stripos('c;crd;cred;credito;h;hab;haber;',
            trim($caso_imput).';') !== false)
            $result = 'credito';
        return $result;
    }
    
}

if ( ! function_exists('plan_de_pago')) {
    
    function plan_de_pago(
        $capital, $anticipo, $tasa, $fv_dia, $interes='saldos',
        $sistema='frances') {

        $result = array();
        $fv_anticipo = '';
        $fv_cuota1 = '';
        $fv_cuotan = '';
        return $result;

    }
    
}

if ( ! function_exists('dv')) {
    
    function dv($cadena, $calculo=NULL) {
        $num_magic = '713971397139713';
        $result='';
        if (is_null($calculo) or ($calculo === strtolower('base9713pon10'))) {
            $wcadena= str_pad(trim($cadena), 15, '0', STR_PAD_LEFT);
            $suma = 0;
            for ($i=0; $i <= 14; $i++) {
                $num = (int)substr($num_magic, $i, 1) * 
                        (int)substr($wcadena, $i, 1);
                $num  %= 10;
                $suma += $num;
            }
            $suma %= 10;
            if ($suma == 0) {
                        $result = '0';
            }
            else {
                $result = sprintf('%d', 10 - $suma);
            }
        }
        else if (is_string($calculo) &&
            strtolower(trim($calculo)) == 'modulo11') {
        }
        else if (is_string($calculo) &&
            strtolower(trim($calculo)) == 'modulo10') {
        }
        return $result;
    }
    
}

/*
 * Identica a dv
 */
if ( ! function_exists('dig_verif')) {
    
    function dig_verif($cadena, $calculo=NULL) {
        return dv($cadena, $calculo);
    }
}

if ( ! function_exists('aplicar_dv')) {
    
    function aplicar_dv($valor, $rutina) {
        $dv = dv($valor, $rutina);
        if (is_string($valor))
            $result = trim($valor) . $dv;
        if (is_numeric($valor))
            $result = $valor * 10 + int($dv);
        return $result;
    }
    
}

