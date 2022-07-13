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
            } else {
                throw new Exception('ocorreu um erro! ao criar');
            }
        } catch (Exception $e) {
            return ['fail' => "não podemos atender a requisição", "type_error" => $e->getMessage()];
        }
    }

    //deletar user se existir
    public function delete(int $id)
    {
        try {

            $busca = $this->detalhes($id);
            if (!isset($busca['fail'])) {
                $conexao = $this->conexao->Connection();
                $conexao->beginTransaction();
                $conexao->exec("delete from user where id_user = {$id}");
                if ($conexao->commit()) {
                    return ['success' => "usuario deletado com sucesso!"];
                } else {
                    throw new Exception('ocoreu um erro ao deletar');
                }
            } else {
                throw new Exception($busca['type_error'] . ' ao deletar');
            }
        } catch (Exception $e) {
            return ['fail' => "não podemos atender a requisição", "type_error" => $e->getMessage()];
        }
    }

    //atualizar user
    public function update(int $id, array $dados = [])
    {
        try {

            $busca = $this->detalhes($id);
            if (!isset($busca['fail'])) {
                extract($dados);
                $data =  date('Y-m-d');
                $hora = date('H:i:s');
                $senha = md5($password_user);
                $conexao = $this->conexao->Connection();
                $conexao->beginTransaction();
                $conexao->exec("update user set name_user = '{$name_user}',
                            email_user ='{$email_user}',
                            password_user ='{$senha}',
                            date_create =  '{$data}',
                            hora_create = '{$hora}'
                            where id_user = {$id}");

                if ($conexao->commit()) {
                    return ['success' => "usuario atualizado com sucesso!"];
                } else {
                    throw new Exception('ocorreu um erro!');
                }
            } else {
                throw new Exception($busca['type_error'] . ' atualizar');
            }
        } catch (Exception $e) {
            return ['fail' => "não podemos atender a requisição", "type_error" => $e->getMessage()];
        }
    }

    //dealhes do user
    public function detalhes(int $id)
    {
        try {
            $conexao = $this->conexao->Connection();
            $res = $conexao->prepare("SELECT * FROM user where id_user = {$id}");
            $res->execute();
            $response = $res->fetch();
            if (!empty($response)) {
                return $response;
            } else {
                throw new Exception('ocorreu um erro!');
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
                throw new Exception('ocorreu um erro ao listar');
            }
        } catch (Exception $e) {
            return ['fail' => 'não podemos atender a requisição ', "type_error" => $e->getMessage()];
        }
    }
}
