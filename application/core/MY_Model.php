<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    private $table = '';
    public function __construct($table)
    {
        parent::__construct();
        $this->table = $table;
    }

    public function get($column = '*', $where = [], $table = null)
    {
        if ($table === null) $table = $this->table;
        return $this->db->select($column)
            ->from($table)
            ->where($where)
            ->get()
            ->result();
    }

    public function getPage($column = '*', $where = [], $page = 1, $limit = 10, $table = null)
    {
        $offset = ($page - 1) * $limit;

        if ($table === null) $table = $this->table;
        return $this->db->select($column)
            ->from($table)
            ->where($where)
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->result();
    }

    public function getById($id, $column = '*', $table = null)
    {
        if ($table === null) $table = $this->table;
        return $this->db->select($column)
            ->from($table)
            ->where(['id' => $id])
            ->get()
            ->row();
    }

    public function getOne($column, $where, $table = null)
    {
        if ($table === null) $table = $this->table;
        return $this->db->select($column)
            ->from($table)
            ->where($where)
            ->get()
            ->row();
    }
    public function insert($data, $table = null)
    {
        if ($table === null) $table = $this->table;
        return $this->db->insert($table, $data);
    }

    public function update($data, $where, $table = null)
    {
        if ($table === null) $table = $this->table;
        $this->db->set($data)
            ->where($where)
            ->update($table);
        return $this->db->affected_rows();
    }

    public function updateById($id, $data, $table = null)
    {
        if ($table === null) $table = $this->table;
        return $this->db->set($data)
            ->where(['id' => $id])
            ->update($table);
    }

    public function delete($where, $table = null)
    {
        if ($table === null) $table = $this->table;
        return $this->db->where($where)
            ->delete($table);
    }
    public function deleteById($id, $table = null)
    {
        if ($table === null) $table = $this->table;
        return $this->db->where(['id' => $id])
            ->delete($table);
    }
    public function builder($data)
    {
        $this->db->select((isset($data['select'])) ? $data['select'] : '*');
        if (isset($data['join'][0])) {
            foreach ($data['join'] as $key => $value) {
                $this->db->join($value['table'], $value['on'], $value['param']);
            }
        }
        if (isset($data['where'][0])) {
            foreach ($data['where'] as $key => $value) {
                $this->db->where($value);
            }
        }

        if (isset($data['from'])) $this->db->from($data['from']);
        else if (isset($data['table'])) $this->db->from($data['table']);
        else $this->db->from($this->table);

        if (isset($data['group_by']))  $this->db->group_by($data['group_by']);
        if (isset($data['order_by']))  $this->db->order_by($data['order_by']);
        if (isset($data['limit']))  $this->db->limit($data['limit']);
        if (isset($data['offset']))  $this->db->offset($data['offset']);
        return $this->db;
    }
}
