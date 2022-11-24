<?php

use Slim\Http\Response;
use Slim\Http\Request;

class History extends Library{

    public function __construct($function)
    {
        parent::__construct();
        self::$function();
        return $this->app->run();
    }

    protected function getHistory()
    {
        $this->app->get($this->pattern . "/{email}", function (Request $request, Response $response, $args) {
            $email = $args['email'];
            $Query = "SELECT A.id, B.name, A.point, A.createddate FROM history AS A JOIN cafe AS B ON A.cafe_id = B.id WHERE A.email = '$email'";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $Fetch[0]['status'] = "success";
                $Fetch[0]['apimessage'] = "data diterima";
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "data tidak ditemukan"]), 200);
            }
        });
    }

    protected function getHistoryDetail()
    {
        $this->app->get($this->pattern . "/{id}", function (Request $request, Response $response, $args) {
            $id = $args['id'];
            $Query = "SELECT A.createddate, C.name, D.name AS itemname, D.price AS itemprice, A.point
                FROM history AS A
                JOIN history_detail AS B
                ON A.id = B.history_id
                JOIN cafe AS C
                ON A.cafe_id = C.id
                JOIN item AS D
                ON B.item_id = D.id
                WHERE B.history_id = '$id'";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $Fetch[0]['status'] = "success";
                $Fetch[0]['apimessage'] = "data diterima";
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "data tidak ditemukan"]), 200);
            }
        });
    }
}
