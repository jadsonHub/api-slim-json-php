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

            $auxSenha = md5($dados['password_user']);
            $dados['password_user'] = $auxSenha;
            $keyArr = array_keys($dados);
            $insert = 'insert into user ';
            $colum = '(';
            $values = 'values(';
            $sql = '';
            
            foreach($keyArr as $indice => $value){
                   $colum .= $value.',';
            };
            foreach($dados as $indice => $value){
                     $values.="'".$value. "'".',';
            }
            $sql.= $insert.rtrim($colum,',').')'.rtrim($values,',').');';
           
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

                $auxSenha = md5($dados['password_user']);
                $data = date('Y-m-d');
                $hora = date('H:i:s');
                $dados['password_user'] = $auxSenha;
                $keyArr = array_keys($dados);
                $update = "update user set date_create = '{$data}', hora_create = '{$hora}',";
                $colum = '';
                $sql = '';

                foreach($keyArr as $indice => $value){
                    $colum.=($value."='{$dados[$value]}',");
                };
                $sql = $update.rtrim($colum,','). " where id_user = {$id}";
                
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
