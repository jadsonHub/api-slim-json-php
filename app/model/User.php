<?php

namespace app\model;

use app\database\Connection;
use Exception;

class User
{

    protected $conexao = null;
    protected $token = null;

    public function __construct()
    {
        $this->conexao = new Connection();
    }

    //cirar user
    public function create(array $dados = [])
    {

        try {


            if (!isset($dados['id']) && !isset($dados['token']) || empty($dados)) {
                throw new Exception('Não autorizado');
            }

            if (!$this->verificarToken($dados['token'], $dados['id'])) {
                throw new Exception('Token invalido');
            }

            unset($dados['id']);
            unset($dados['token']);

            $auxSenha = md5($dados['password_user']);
            $dados['password_user'] = $auxSenha;
            $keyArr = array_keys($dados);
            $insert = 'insert into user ';
            $colum = '(';
            $values = 'values(';
            $sql = '';

            foreach ($keyArr as $indice => $value) {
                $colum .= $value . ',';
            };
            foreach ($dados as $indice => $value) {
                $values .= "'" . $value . "'" . ',';
            }
            $sql .= $insert . rtrim($colum, ',') . ')' . rtrim($values, ',') . ');';

            $conexao = $this->conexao->Connection();
            $conexao->beginTransaction();
            $conexao->exec($sql);
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
    public function delete(int $id ,$dados = [])
    {
        try {

            if (!isset($dados['id']) && !isset($dados['token']) || empty($dados)) {
                throw new Exception('Não autorizado');
            }

            if (!$this->verificarToken($dados['token'], $dados['id'])) {
                throw new Exception('Token invalido');
            }

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

            if (!isset($dados['id']) && !isset($dados['token']) || empty($dados)) {
                throw new Exception('Não autorizado');
            }

            if (!$this->verificarToken($dados['token'], $dados['id'])) {
                throw new Exception('Token invalido');
            }


            unset($dados['id']);
            unset($dados['token']);

            $busca = $this->detalhes($id);
            if (!isset($busca['fail'])) {

                $auxSenha = md5($dados['password_user']);
                $data = date('Y-m-d');
                $hora = date('H:i:s');
                $dados['password_user'] = $auxSenha;
                $keyArr = array_keys($dados);
                $update = "update user set date_create = '{$data}', hora_create = '{$hora}',";
                $colum = '';
                $sql = '';

                foreach ($keyArr as $indice => $value) {
                    $colum .= ($value . "='{$dados[$value]}',");
                };
                $sql = $update . rtrim($colum, ',') . " where id_user = {$id}";

                $conexao = $this->conexao->Connection();
                $conexao->beginTransaction();
                $conexao->exec($sql);

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
    public function detalhes(int $id, $dados = [])
    {
        try {

            if (!isset($dados['id']) && !isset($dados['token']) || empty($dados) ) {
                throw new Exception('Não autorizado');
            }

            if (!$this->verificarToken($dados['token'], $dados['id'])) {
                throw new Exception('Token invalido');
            }


            unset($dados['id']);
            unset($dados['token']);

            $conexao = $this->conexao->Connection();
            $res = $conexao->prepare("SELECT * FROM user where id_user = {$id}");
            $res->execute();
            $response = $res->fetch();
            if ($response) {
                return $response;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return ['fail' => 'não podemos atender a requisição ', "type_error" => $e->getMessage()];
        }
    }

    //lista de user
    public function list(array $dados = [])
    {

        try {

          
            if (!isset($dados['id']) && !isset($dados['token']) || empty($dados)) {
                throw new Exception('Não autorizado');
            }

            if (!$this->verificarToken($dados['token'], $dados['id'])) {
                throw new Exception('Token invalido');
            }


            unset($dados['id']);
            unset($dados['token']);

            $res = $this->conexao->Connection()->prepare("SELECT * FROM api_php.user;");
            $res->execute();
            $response = $res->fetchAll();
            if ($response) {
                return $response;
            } else {
                throw new Exception('ocorreu um erro ao listar');
            }
        } catch (Exception $e) {
            return ['fail' => 'não podemos atender a requisição ', "type_error" => $e->getMessage()];
        }
    }

    public function login(array $dados = [])
    {

        try {

            $conexao = $this->conexao->Connection();
            $senha = md5($dados['password_user']);
            $res =  $conexao->prepare("select * from user where email_user = '{$dados['email_user']}' and password_user = '{$senha}';");
            $res->execute();
            $response = $res->fetch();
            if ($response) {
                $this->token = $this->setToken($dados['email_user'], $response['id_user']);
                $response['token'] = $this->token;
                return $response;
            } else {
                throw new Exception('ocorreu um erro ao logar');
            }
        } catch (Exception $e) {
            return ['fail' => 'não podemos atender a requisição ', "type_error" => $e->getMessage()];
        }
    }

    protected function setToken($tokenPass, $id)
    {

        try {

            $token = base64_encode(substr(md5('dbzsuperedu'), 0, 10) . substr(md5($tokenPass), 0, 10));

            $conexao = $this->conexao->Connection();
            $res =  $conexao->prepare("update user set token = '{$token}' where id_user = {$id};");
            $response =  $res->execute();
            if ($response) {
                
                return $this->token = $token;
            } else {
                throw new Exception('ocorreu um erro ao logar');
            }
        } catch (Exception $e) {
            return ['fail' => 'não podemos atender a requisição ', "type_error" => $e->getMessage()];
        }
    }

    protected function verificarToken($tokenPass, $id)
    {
        try {

            $conexao = $this->conexao->Connection();
            $res =  $conexao->prepare("select token from user  where id_user = {$id} and token = '{$tokenPass}';");
            $res->execute();
            $response = $res->fetch();
            if ($response) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return ['fail' => 'não podemos atender a requisição ', "type_error" => $e->getMessage()];
        }
    }

    //em dev
    public function loggout($tokenPass,$id){

    }
}
