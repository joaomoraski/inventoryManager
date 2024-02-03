<?php

namespace App\Repositories;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use PhpParser\Builder;

abstract class AbstractRepository
{

    // Filter, fields, sort, order
    protected $model;
    protected $query;

    public function __construct(Model|Authenticatable $model) // Null caso seja criado so para listagem ou localizar.
    {
        $this->model = $model;
        $this->query = $this->model->newQuery();
    }

    public function selectWithFields($fields)
    {
        // Verifica se a request esta chegando de forma correta.
        // Caso tenha { ou } no campos, é para considerar que o mesmo tanto que abriu precisa fechar.
        // Caso seja 0 nos dois vai seguir normal.
        $nestedRelationPatternSearchOpen = "/{/m";
        $nestedRelationPatternSearchClose = "/}/m";
        if (preg_match_all($nestedRelationPatternSearchOpen, $fields) !=
            preg_match_all($nestedRelationPatternSearchClose, $fields)) {
            return response()->json([
                "message" => "O campo 'fields' possui um padrão não reconhecido. Verifique antes de prosseguir."
            ], 400);
        }

        if (preg_match("/{|}/m", $fields)) {
            $pattern = '/,(?![^{]*})/';
            $splitFieldsAndNestedFields = preg_split($pattern, $fields);
            $onlyNestedFields = [];
            foreach ($splitFieldsAndNestedFields as $index => $field) {
                if (preg_match("/{|}/m", $field)) {
                    $onlyNestedFields[] = $field;
                    array_splice($splitFieldsAndNestedFields, $index, $index);
                }
            }
            $onlyNestedFields = str_replace("{", ",", $onlyNestedFields);
            $onlyNestedFields = str_replace("}", "", $onlyNestedFields);
            $preg_split = preg_split("/,/m", $onlyNestedFields[0]);
            $relation = $preg_split[0];
            $columns = array_splice($preg_split, 1);
            $splitFieldsAndNestedFields[] = $relation.'_id';
            $this->query->addSelect($splitFieldsAndNestedFields);
            $this->query->with([$relation => function ($query) use ($columns) {
                $query->select($columns);
            }]);
        }
        return response()->json([
            "message" => "Erro ao realizar sua consulta, tente novamente mais tarde"
        ], 500);
    }

    public function selectWithFilter($filters)
    {
        $filters = explode(";", $filters);

        foreach ($filters as $filter) {
            $filterParts = explode(':', $filter);
            // Verifica se o filtro tem o formato esperado
            if (count($filterParts) === 3) {
                list($field, $operator, $value) = $filterParts;
                // Adiciona o filtro à consulta
                $this->query->where($field, $operator, $value);
            } else {
                Log::error("Filtro inválido: $this->model com a request: $filter");
                throw new Exception("Ocorreu um erro ao tentar executar o filtro atual, verifique novamente.\n
                                            Se o problema persistir entre em contato com o nosso suporte.");
            }
        }

        return $this;
    }

    public function sortBy($column, $direction = 'asc')
    {
        // Adiciona ordenação à consulta
        $this->query->orderBy($column, $direction);
        return $this;
    }

    public function findById($id, $relationFields = []): Model
    {
        $model = $this->model->findOrFail($id);
        // Se tiver passado os campos das entidades relacionadas junto
        if (count($relationFields) > 0) {
            // Carrega com o lazy eager loading
            $model->load($relationFields);
        }
        return $model;
    }

    public function create(array $data): Model
    {
        // TODO: Adicionar tratamento de erro, retorno para a api e etc
        return $this->model->create($data);
    }


    public function update($id, array $data): bool
    {
        // TODO: Adicionar tratamento de erro, retorno para a api e etc
        return $this->model->findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        // TODO: Adicionar tratamento de erro, retorno para a api e etc
        return $this->model->findOrFail($id)->delete();
    }

    public function getResults()
    {
        // Retorna os resultados da consulta
        return $this->query->get();
    }
}
