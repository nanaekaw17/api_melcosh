<?php

use Slim\Http\Response;
use Slim\Http\Request;

class Cafe extends Library{

    public function __construct($function)
    {
        parent::__construct();
        self::$function();
        return $this->app->run();
    }

    protected function getAll()
    {
        $this->app->get($this->pattern, function (Request $request, Response $response) {
            $Query = "SELECT * FROM cafe";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $Fetch[0]['status'] = "success";
                $Fetch[0]['apimessage'] = "selamat datang";
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "gagal mendapat data"]), 200);
            }
        });
    }

    protected function getCategory()
    {
        $this->app->get($this->pattern . "/{id}", function (Request $request, Response $response, $args) {
            $id = $args['id'];
            $Query = "SELECT DISTINCT category FROM item WHERE cafe_id = $id ORDER BY category";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $Fetch[0]['status'] = "success";
                $Fetch[0]['apimessage'] = "data diterima";
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "gagal mendapat data"]), 200);
            }
        });
    }

    protected function getItem()
    {
        $this->app->get($this->pattern . "/{id}/{category}", function (Request $request, Response $response, $args) {
            $id = $args['id'];
            $category = $args['category'];
            $Query = "SELECT * FROM item WHERE cafe_id = $id AND category = '$category'";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $Fetch[0]['status'] = "success";
                $Fetch[0]['apimessage'] = "data diterima";
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "gagal mendapat data"]), 200);
            }
        });
    }
}
