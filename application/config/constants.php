<?php
/* *********************************************************************************
Autor: CI
Fecha: 06/10/2020
/* ******************************************************************************** */
defined('BASEPATH') OR exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
/*
|--------------------------------------------------------------------------
| Versiones de recursos
|--------------------------------------------------------------------------
*/
define('CSS', 'v-1.0.0.0');
define('JS', 'v-1.0.0.0');
define('IMG', 'v-1.0.0.0');

/*
 * |--------------------------------------------------------------------------
 * | Datos de pagina
 * |--------------------------------------------------------------------------
 * |
 * | These modes are used when working with fopen()/popen()
 * |
 */

define('TITULO', 'Centro Quiropráctico – Schubel');
define('URL_IMAGE', 'http://localhost/translogint/images/');
define('URL_JS', 'http://localhost/translogint/system/application/views/javascript/');
define('URL_CSS', 'http://localhost/translogint/system/application/views/estilos/');
define('URL_BASE', 'http://localhost/translogint/');
define('FORMATO_IMPRESION', 1);

/*
 * FORMATO 1: FERRESAT
 * FORMATO 2: JIMMYPLAST
 * FORMATO 3: INSTRUMENTOS Y SISTEMAS
 * FORMATO 4: FERREMAX
 * FORMATO 5: DISTRIBUIDORA CYG
 * FORMATO 8: IMPACTO
 */

// TAMAÑO LIMITE DE DETALLES EN LOS DOCUMENTOS
define('VENTAS_GUIA', 10);

define('COMPRAS_GUIA', 10);

define('VENTAS_FACTURA', 10);
define('VENTAS_BOLETA', 10);
define('VENTAS_COMPROBANTE', 10);

define('COMPRAS_FACTURA', 40);
define('COMPRAS_BOLETA', 40);

define('COMPARTIR_PROVCOMPANIA', 1); // comparte provedores(todas las companias):1
define('COMPARTIR_CLICOMPANIA', 1); // comparte clientes (todas las companias):1
define('COMPARTIR_ARTCOMPANIA', 1); // comparte articulos(todas las companias):1
define('COMPARTIR_FAMCOMPANIA', 1); // comparte familias(todas las companias):1

define('CODIGO_CARGO_DOCTOR', 3);
define('CODIGO_TIPO_CONSULTA_PADRE', 6);
define('CODIGO_ESTADO_CONSULTA_ABIERTA', 4);
/* End of file constants.php */
/* Location: ./system/application/config/constants.php */