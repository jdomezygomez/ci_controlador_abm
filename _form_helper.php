<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Jose Daniel Gomez
 * @link		https://codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('form_field'))
{
    function form_field($model, $field, $value=NULL, $extra = array())
    {
		$CI =& get_instance();

        $defaults = array(
            'name'  => $field,
            'type'  => 'input',
            'value' => $value,
        );
        
        if (!isset($CI->$model)) {
            $CI->load->model($model);
        }
    }

}