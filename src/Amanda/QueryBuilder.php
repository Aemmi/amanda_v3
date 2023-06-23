<?php
namespace Src\Amanda;

use Src\Amanda\DB;

class QueryBuilder extends DB
{
    private $table;
    private $whereConditions = [];
    private $selectColumns = '*';
    private $insertData = [];
    private $updateData = [];

    public function __construct(){
        parent::__construct();
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select($columns)
    {
        $this->selectColumns = $columns;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->whereConditions[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        $this->where[] = "OR {$column} {$operator} :{$column}";
        $this->params[":{$column}"] = $value;
        return $this;
    }

    public function insert($data)
    {
        $this->insertData = $data;
        return $this;
    }

    public function update($data)
    {
        $this->updateData = $data;
        return $this;
    }

    public function delete()
    {
        // Perform the actual DELETE query based on the builder settings
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->whereConditions)) {
            $sql .= ' WHERE ';

            foreach ($this->whereConditions as $condition) {
                $sql .= "{$condition['column']} {$condition['operator']} '{$condition['value']}' AND ";
            }

            $sql = rtrim($sql, 'AND ');
        }

        // Execute the DELETE query
        // ...

        return $this->delete($sql);
    }

    public function get()
    {
        // Perform the actual SELECT query based on the builder settings
        $sql = "SELECT {$this->selectColumns} FROM {$this->table}";

        if (!empty($this->whereConditions)) {
            $sql .= ' WHERE ';

            foreach ($this->whereConditions as $condition) {
                $sql .= "{$condition['column']} {$condition['operator']} '{$condition['value']}' AND ";
            }

            $sql = rtrim($sql, 'AND ');
        }

        // Execute the SELECT query and return the result
        // ...

        return $this->select($sql);
    }
}
