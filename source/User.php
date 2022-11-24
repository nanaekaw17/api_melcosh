<?php

use Slim\Http\Response;
use Slim\Http\Request;

class User extends Library{

    public function __construct($function)
    {
        parent::__construct();
        self::$function();
        return $this->app->run();
    }

    protected function login()
    {
        $this->app->get($this->pattern . "/{email}/{password}", function (Request $request, Response $response, $args) {
            $email = $args['email'];
            $password = md5($args['password']);
            $Query = "SELECT * FROM androiduser WHERE email = '$email' AND password = '$password'";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $Fetch[0]['status'] = "success";
                $Fetch[0]['apimessage'] = "selamat datang";
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "email atau password salah"]), 200);
            }
        });
    }


    protected function getUser()
    {
        $this->app->get($this->pattern . "/{email}", function (Request $request, Response $response, $args) {
            $email = $args['email'];
            $Query = "SELECT A.*, SUM(B.point) AS point FROM androiduser AS A
                        JOIN history AS B
                        ON A.email = B.email
                        WHERE A.email = '$email'";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $Fetch[0]['status'] = "success";
                $Fetch[0]['apimessage'] = "selamat datang";
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "email atau password salah"]), 200);
            }
        });
    }

    private function insertUser()
    {
        $this->app->post($this->pattern, function (Request $request, Response $response) {
            $value_data = $request->getParsedBody();
            $email = $value_data["email"];
            $cekemail = "SELECT * FROM androiduser WHERE email = '$email'";
            $Fetch = $this->db->query($cekemail)->fetchAll();
            if ($Fetch) {
                return $response->withJson(array(["status" => "failed", "apimessage" => "email sudah di gunakan sebelumnya"]), 200);
            } else {
                $sql = "INSERT INTO androiduser (email, name, dob, phone, password) VALUES (:email, :name, :dob, :phone, :password)";
                $stmt = $this->db->prepare($sql);
                $data = [
                    ":email"       => $value_data["email"],
                    ":name"        => $value_data["name"],
                    ":dob"         => $value_data["dob"],
                    ":phone"       => $value_data["phone"],
                    ":password"    => md5($value_data["password"])
                ];
                if ($stmt->execute($data)) {
                    return $response->withJson(array(["status" => "success", "apimessage" => "berhasil input user"]), 200);
                } else {
                    return $response->withJson(array(["status" => "failed", "apimessage" => "gagal input user"]), 200);
                }
            }
        });
    }

    private function updateUser()
    {
        $this->app->post($this->pattern, function (Request $request, Response $response) {
            $value_data = $request->getParsedBody();
            $sql = "UPDATE androiduser SET name=:name, dob=:dob, phone=:phone WHERE email=:email";
            $stmt = $this->db->prepare($sql);
            $data = [
                ":email"       => $value_data["email"],
                ":name"        => $value_data["name"],
                ":dob"         => $value_data["dob"],
                ":phone"       => $value_data["phone"]
            ];
            if ($stmt->execute($data)) {
                return $response->withJson(array(["status" => "success", "apimessage" => "berhasil update user"]), 200);
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "gagal update user"]), 200);
            }
        });
    }

    private function updatePassword()
    {
        $this->app->post($this->pattern, function (Request $request, Response $response) {
            $value_data = $request->getParsedBody();
            $email = $value_data['email'];
            $newpassword = md5($value_data['newpassword']);
            $oldpassword = md5($value_data['oldpassword']);

            $Query = "SELECT * FROM androiduser WHERE email = '$email' AND password = '$oldpassword'";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $sql = "UPDATE androiduser SET password=:password WHERE email=:email";
                $stmt = $this->db->prepare($sql);
                $data = [
                    ":email"       => $email,
                    ":password"    => $newpassword
                ];
                if ($stmt->execute($data)) {
                    return $response->withJson(array(["status" => "success", "apimessage" => "berhasil update password"]), 200);
                } else {
                    return $response->withJson(array(["status" => "failed", "apimessage" => "gagal update password"]), 200);
                }
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "password lama anda salah"]), 200);
            }
        });
    }

    private function resetPassword()
    {
        $this->app->get($this->pattern . "/{email}/{phone}", function (Request $request, Response $response, $args) {
            $email = $args['email'];
            $phone = $args['phone'];

            $Query = "SELECT * FROM androiduser WHERE email = '$email' AND phone = '$phone'";
            $Fetch = $this->db->query($Query)->fetchAll();
            if ($Fetch) {
                $sql = "UPDATE androiduser SET password=:password WHERE email=:email";
                $stmt = $this->db->prepare($sql);
                $data = [
                    ":email"       => $email,
                    ":password"    => md5("123")
                ];
                if ($stmt->execute($data)) {
                    return $response->withJson(array(["status" => "success", "apimessage" => "berhasil update password"]), 200);
                } else {
                    return $response->withJson(array(["status" => "failed", "apimessage" => "gagal update password"]), 200);
                }
            } else {
                return $response->withJson(array(["status" => "failed", "apimessage" => "email dan nomor telefon tidak di temukan"]), 200);
            }
        });
    }
}
