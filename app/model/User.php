<?php

namespace app\model;

use app\database\Connection;
use Exception;



class User
{

    protected $conexao = null;

    public function __construct()
    {
        $this->conexao = new Connection();
    }

    //cirar user
    public function create(array $dados = [])
    {


        try {
            extract($dados);
            $senha = md5($password_user);
            $conexao = $this->conexao->Connection();
            $conexao->beginTransaction();
            $conexao->exec("insert into user( name_user,email_user,password_user)
            values('{$name_user}','{$email_user}','{$senha}');");

            if ($conexao->commit()) {
                return ['success' => "usuario cadastrado com sucesso!"];
            }else{
                throw new Exception('usuario não cadastrado');
            }
        } catch (Exception $e) {
            return ['fail' => "não podemos atender a requisição", "type_error" => $e->getMessage()];
        }
    }

    //em dev
    public function delete(int $id)
    {
    }

    //em dev
    public function update(int $id, array $dados = [])
    {
       
    }
 
    //dealhes do user
    public function detalhes(int $id)
    {
        try {
            $conexao = $this->conexao->Connection();
            $res = $conexao->prepare("SELECT * FROM user where id_user = {$id}");
            $res->execute();
            $response = $res->fetch();
            if(!empty($response)){
                 return $response;
            }else{
                throw new Exception('usuario não encontrado');
            }
        } catch (Exception $e) {
            return ['fail' => 'não podemos atender a requisição ', "type_error" => $e->getMessage()];
        }
    }
 //lista de user
    public function list()
    {

        try {
            $res = $this->conexao->Connection()->prepare("SELECT * FROM api_php.user;");
            $res->execute();
            $response = $res->fetchAll();
            if (!empty($response)) {
                return $response;
            } else {
                throw new Exception('sem usuarios cadastrados');
            }
        } catch (Exception $e) {
            return ['fail' => 'não podemos atender a requisição ', "type_error" => $e->getMessage()];
        }
    }

    //em dev
    public function login()
    {
    }
    //em dev
    public function loggout()
    {
    }
}
