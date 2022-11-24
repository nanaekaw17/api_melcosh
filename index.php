<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 14/01/2019
 * Time: 17:29
 */

require __DIR__.'/Settings.php';
require __DIR__.'/Library.php';

function __autoload($classname) {
    require_once __DIR__.'/source/'.$classname.'.php';
}

/**
 * Memecah REQUEST URI menjadi
 * Nama API = permit_API
 * Class API = URI ke 2
 * Function API = URI ke 3
 */
$URI = explode('/', $_SERVER['REQUEST_URI']);
$__NAME_API__ = $URI[1];
$__CLASS_API__ = $URI[2];
$__FUNCTION_API__ = $URI[3];

/**
 * Running API
 *
 * Cara pemanggilan API di URL :
 * <host>/<route_API>/$Modul/function()
 */
$Run = new $__CLASS_API__($__FUNCTION_API__);

