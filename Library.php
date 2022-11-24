<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 14/01/2019
 * Time: 20:21
 */

use Slim\Http\Response;
use Slim\Http\Request;

class Library extends Settings {

    /**
     * Library constructor.
     * Berguna untuk pembuatan function tambahan yang digunakan API
     *
     * Cara pemanggilan API di URL :
     * <host>/<route_API>/$Modul/function()
     *
     * Cara inisialisasi SLIM menggunakan $this->app
     * Lihat Class Settings
     *
     * function(Request $request, Response $response, $args)
     * Parameter ketiga adalah inputan masukkan dari url (bagian dari $this->app)
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->deklarasi = $this->container;
        $this->pattern = self::configUri();
    }

    private function configUri() {
        $URI = explode('/', $_SERVER['REQUEST_URI']);
        $modul = $URI[1];
        $class = $URI[2];
        $function = $URI[3];

        $config = '/'.$class.'/'.$function;
        return $config;
    }

    /**
     * Middleware berguna untuk otentikasi API Key
     * Penjelasan selengkapnya tentang konsep middleware dapat dilihat
     * Doc : http://www.slimframework.com/docs/v3/concepts/middleware.html
     */
    protected function middleware() {
        $middleware = function (Request $request, Response $response, $next, $encode = true) {
            $dataParsed = $request->getParsedBody();
            if ($encode == true) {
                $data = self::decode_str($dataParsed['idUser']);
            } if ($encode == false) {
                $data = $dataParsed['idUser'];
            }
            $Query = "SELECT * FROM vw_mstuser WHERE idUser = '$data'";
            $Fetch = $this->db->query($Query)->fetch(PDO::FETCH_OBJ);
            if (empty($Fetch)) {
                return $response->withJson(['status' => 'API key required'], 401);
            }
            if (!empty($Fetch)) {
                if ($Fetch->statusAPI == false) {
                    return $response->withJson(['status' => 'API key is wrong'], 401);
                } else if ($Fetch->statusAPI == true) {
                    return $response = $next($request, $response);
                }
            }
        };
        return $middleware;
    }

    protected function encode_str($value, $gembok = '') {
        $skey = (trim($gembok) == '' ? '1z2ben45tyu56yup' : $gembok);
        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim(self::safe_b64encode($crypttext));
    }

    protected function decode_str($value, $gembok = '') {
        $skey = (trim($gembok) == '' ? '1z2ben45tyu56yup' : $gembok);
        if (!$value) {
            return false;
        }
        $crypttext = self::safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    private function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    private function safe_b64decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    protected function cekOtorisasi() {}

    protected function getAll() {}

    protected function getData() {}

    protected function post() {}

    protected function put() {}

    protected function delete() {}

}