<?php

namespace App\Repositories;

use Closure;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;


abstract class Base
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * BaseRepository constructor.
     * @param Application $app
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->makeModel();
    }

    /**
     * @return Model|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Returns the current Model instance
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        return $this->model->first($columns);
    }

    /**
     * Alias of All method
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function get($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * @param null $limit
     * @param array $columns
     * @param int $page
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $page = 1)
    {
        $results = $this->model->paginate($limit, $columns, 'page', $page);

        $results->appends(app('request')->query());

        return $results;
    }

    /**
     * create a model in repository
     *
     * @param array $attributes
     *
     * @return mixed
     *
     */
    public function create(array $attributes)
    {
        $model = $this->model->create($attributes);

        return $model;
    }

    /**
     * find a model in repository
     *
     * @param array $attributes
     *
     * @return mixed
     *
     */
    public function find($id, $columns = ['*'])
    {
        $model = $this->model->find($id, $columns);

        return $model;
    }

    /**
     * Update a entity in repository by id
     *
     * @param array $attributes
     * @param       $id
     *
     * @return mixed
     *
     */
    public function update(array $attributes, $id)
    {
        $model = $this->model->findOrFail($id);

        $model->fill($attributes);

        $model->save();

        return $model;
    }

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id)
    {
        $model = $this->model->find($id);

        return $model->delete();
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function take($limit)
    {
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * @param array $where
     * @return $this
     * @throws \Exception
     */
    public function filter(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;

                // if (!in_array($field, $this->fieldSearchable)) continue;

                $condition = preg_replace('/\s\s+/', ' ', trim($condition));

                $operator = explode(' ', $condition);

                if (count($operator) > 1) {
                    $condition = $operator[0];

                    $operator = $operator[1];
                } else $operator = null;

                switch (strtoupper($condition)) {
                    case 'IN':
                        if (!is_array($val)) throw new \Exception("Input {$val} mus be an array");
                        $this->model = $this->model->whereIn($field, $val);
                        break;
                    case 'NOTIN':
                        if (!is_array($val)) throw new \Exception("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotIn($field, $val);
                        break;
                    case 'DATE':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereDate($field, $operator, $val);
                        break;
                    case 'DAY':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereDay($field, $operator, $val);
                        break;
                    case 'MONTH':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereMonth($field, $operator, $val);
                        break;
                    case 'YEAR':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereYear($field, $operator, $val);
                        break;
                    case 'EXISTS':
                        if (!($val instanceof Closure)) throw new \Exception("Input {$val} must be closure function");
                        $this->model = $this->model->whereExists($val);
                        break;
                    case 'HAS':
                        if (!($val instanceof Closure)) throw new \Exception("Input {$val} must be closure function");
                        $this->model = $this->model->whereHas($field, $val);
                        break;
                    case 'HASMORPH':
                        if (!($val instanceof Closure)) throw new \Exception("Input {$val} must be closure function");
                        $this->model = $this->model->whereHasMorph($field, $val);
                        break;
                    case 'DOESNTHAVE':
                        if (!($val instanceof Closure)) throw new \Exception("Input {$val} must be closure function");
                        $this->model = $this->model->whereDoesntHave($field, $val);
                        break;
                    case 'DOESNTHAVEMORPH':
                        if (!($val instanceof Closure)) throw new \Exception("Input {$val} must be closure function");
                        $this->model = $this->model->whereDoesntHaveMorph($field, $val);
                        break;
                    case 'BETWEEN':
                        if (!is_array($val)) throw new \Exception("Input {$val} mus be an array");
                        $this->model = $this->model->whereBetween($field, $val);
                        break;
                    case 'BETWEENCOLUMNS':
                        if (!is_array($val)) throw new \Exception("Input {$val} mus be an array");
                        $this->model = $this->model->whereBetweenColumns($field, $val);
                        break;
                    case 'NOTBETWEEN':
                        if (!is_array($val)) throw new \Exception("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotBetween($field, $val);
                        break;
                    case 'NOTBETWEENCOLUMNS':
                        if (!is_array($val)) throw new \Exception("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotBetweenColumns($field, $val);
                        break;
                    case 'RAW':
                        $this->model = $this->model->whereRaw($val);
                        break;
                    default:
                        $this->model = $this->model->where($field, $condition, $val);
                }
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }

        return $this;
    }

    /**
     * count
     *
     * @return int
     */
    public function count()
    {
        $model = $this->model->count();

        return $model;
    }

    /**
     * sum
     *
     * @return int
     */
    public function sum($column)
    {
        return $this->model->sum($column);
    }

    /**
     * Summary of orderBy
     * @param mixed $column
     * @param mixed $direction
     * @return Model
     */
    public function orderBy($column, $direction = 'asc')
    {
        return $this->model->orderBy($column, $direction);
    }
}
