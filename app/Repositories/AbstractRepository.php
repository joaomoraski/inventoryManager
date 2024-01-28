<?php

namespace App\Repositories;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

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
        // TODO arrumar a filtragem pelos fields, puxar as entidades relacionais e etc
        // Converte a string de campos em um array
        $fieldsArray = explode(',', $fields);

        foreach ($fieldsArray as $field) {
            // Se o campo for uma relação aninhada
            if (Str::contains($field, '.')) {
                // Usa Eager Loading para carregar as relações aninhadas
                list($relation, $column) = explode('.', $field);
                // Adiciona as colunas específicas da relação ao select
                $this->query->with([$relation => function ($query) use ($column) {
                    $query->addSelect($column);
                }]);
            } else {
                // Se não for uma relação aninhada, adiciona ao select
                $this->query->addSelect($field);
            }
        }

        return $this;
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


    public function update($id, array $data): Model
    {
        // TODO: Adicionar tratamento de erro, retorno para a api e etc
        return $this->model->find($id)->update($data);
    }

    public function destroy($id): bool
    {
        // TODO: Adicionar tratamento de erro, retorno para a api e etc
        return $this->model->findOrFail($id)->delete();
    }

    public function setManagerIdFilter()
    {
        // TODO Validar pro adm
        $this->query->where("manager_id", "=", auth()->user()->manager->id);
        return $this;
    }

    public function getResults()
    {
        // Retorna os resultados da consulta
        return $this->query->get();
    }
}
