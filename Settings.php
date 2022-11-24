<?php

class Settings {

    public function __construct()
    {
        require __DIR__.'/vendor/autoload.php';
        $this->app = new Slim\App([
            'settings' => [
                'displayErrorDetails' => true,
                'db' => [
                    'driver'    => 'mysql',
                    'host'      => '127.0.0.1',
                    'database'  => 'melcosh',
                    'username'  => 'root',
                    'password'  => '',
                    'charset'   => 'utf8',
                    'collation' => 'utf8_general_ci',
                    'prefix'    => '',
                ]
            ]
        ]);

        $this->container = $this->app->getContainer();
        /**
         * Pilih salah satu (Required)
         */
        self::PDO(true, $this->container);
        self::ELOQUENT(true, $this->container);
    }

    /**
     * @param $status
     *
     * Jika menggunakan stored procedure gunakan PDO
     * Karena Eloquent tidak mendukung eksekusi query dengan stored procedure
     *
     * Doc : http://php.net/manual/en/class.pdo.php
     */
    private function PDO($status, $container) {
        if ($status == true) {
            $container['db'] = function ($container) {
                $db = new PDO(
                    $container['settings']['db']['driver'].':host='.$container['settings']['db']['host'].';dbname='.$container['settings']['db']['database'].'; charset=UTF8',
                    $container['settings']['db']['username'],
                    $container['settings']['db']['password']
                );
                $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                $db->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
                return $db;
            };
        }
    }



    /**
     * Inisialisasi database menggunakan Eloquent ORM (Object Relational Mapping)
     * Dokumentasi Eloquent dapat dilihat pada dokumentasi Laravel
     * Eloquent merupakan Active Record yang digunakan Laravel
     *
     * Query Builder dari database juga bisa digunakan, dan juga di rekomendasikan
     * menggunakan query builder daripada menggunakan eloquent karena dari segi eksekusi
     * dan keamanan data query builder lebih baik
     *
     * Doc : https://laravel.com/docs/
     *       http://www.slimframework.com/docs/
     *       https://github.com/illuminate/database
     */
    private function ELOQUENT($status, $container) {
        if ($status == true) {
            $container['qb'] = function ($container) {
                $capsule = new \Illuminate\Database\Capsule\Manager;
                $capsule->addConnection($container['settings']['db']);
                $capsule->setAsGlobal();
                $capsule->bootEloquent();
                return $capsule;
            };
        }
    }

}