<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_CRUD extends CI_Model
{
    public $table; // SET TABLE

    public function get_by_id($id)
    {
        $data = $this->db->get_where($this->table, array('id' => $id));
        if ($data->num_rows() == 0) {
            return false;
        } else {
            return $data->row();
        }
    }

    public function get_rows($data = [])
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
        if (isset($data['group_by'])) $this->db->group_by($data['group_by']);
        if (isset($data['order_by'])) $this->db->order_by($data['order_by']);
        if (isset($data['limit'])) $this->db->limit($data['limit']);
        if (isset($data['offset'])) $this->db->offset($data['offset']);
        return $this->db->get((isset($data['from'])) ? $data['from'] : $this->table)->result_array();
    }

    public function get_one_row($data = [])
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
        if (isset($data['group_by'])) $this->db->group_by($data['group_by']);
        if (isset($data['order_by'])) $this->db->order_by($data['order_by']);
        if (isset($data['limit'])) $this->db->limit($data['limit']);
        if (isset($data['offset'])) $this->db->offset($data['offset']);
        return $this->db->get((isset($data['from'])) ? $data['from'] : $this->table)->row_array();
    }


    public function get_count($data = [])
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
        if (isset($data['group_by'])) $this->db->group_by($data['group_by']);
        return $this->db->get((isset($data['from'])) ? $data['from'] : $this->table)->num_rows();
    }

    public function get_count_row($data = [])
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
        if (isset($data['group_by'])) $this->db->group_by($data['group_by']);
        return $this->db->get((isset($data['from'])) ? $data['from'] : $this->table)->row_array();
    }

    public function get_row($where, $from = '')
    {
        $data = $this->db->get_where(($from != '' ? $from : $this->table), $where);
        if ($data->num_rows() == 0) {
            return false;
        } else {
            return $data->row_array();
        }
    }

    public function insert($data, $table)
    {
        $data = array_map("trim", array_map("strip_tags", $data));
        $data = array_map(
            function($member) {
                if(is_numeric($member)){
                    if(strlen($member) < 2){
                        settype($member, 'integer');
                    }else{
                        settype($member, 'string');
                    }   
                }elseif($member == null){
                    settype($member, 'null');
                }else{
                    settype($member, 'string');
                }
                return $member;
            },
            $data
        );
        $this->db->set($data);
        $this->db->insert($table);
        return $this->db->affected_rows() > 0;
    }

    public function update($data, $where, $table)
    {
        $data = array_map("trim", array_map("strip_tags", $data));
        $data = array_map(
            function($member) {
                if(is_numeric($member)){
                    if(strlen($member) < 2){
                        settype($member, 'integer');
                    }else{
                        settype($member, 'string');
                    } 
                }elseif($member == null){
                    settype($member, 'null');
                }else{
                    settype($member, 'string');
                }
                return $member;
            },
            $data
        );
        $this->db->set($data);
        $this->db->where($where);
        return $this->db->update($table);
    }

    public function delete($where, $table)
    {
        $this->db->where($where);
        return $this->db->delete($table);
    }

    public function is_empty()
    {
        return $this->db->truncate($this->table);
    }

    public function get_kode($data = [], $num = 7)
    {
        $kode = 1;
        $this->db->select((isset($data['select'])) ? $data['select'] : '*');
        if (isset($data['where'][0])) {
            foreach ($data['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        if (isset($data['order_by'])) $this->db->order_by($data['order_by']);
        if (isset($data['limit'])) $this->db->limit($data['limit']);
        $hasil = $this->db->get((isset($data['from'])) ? $data['from'] : $this->table)->row_array();
        if ($hasil && $hasil != null) {
            $kode = $hasil['KODE'] + 1;
        } else {
            $kode = 1;
        }

        $bikin_kode = str_pad($kode, $num, "0", STR_PAD_LEFT);

        $kode_jadi = @$data['prefix'] . "-" . $bikin_kode;

        return $kode_jadi;
    }

    public function get_kode_barang($data = [], $num = 6)
    {
        $kode = 1;
        $this->db->select((isset($data['select'])) ? $data['select'] : '*');
        if (isset($data['where'][0])) {
            foreach ($data['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        if (isset($data['order_by'])) $this->db->order_by($data['order_by']);
        if (isset($data['limit'])) $this->db->limit($data['limit']);
        $hasil = $this->db->get((isset($data['from'])) ? $data['from'] : $this->table)->row_array();
        if ($hasil && $hasil != null) {
            $kode = $hasil['KODE'] + 1;
        } else {
            $kode = 1;
        }

        $bikin_kode = str_pad($kode, $num, "0", STR_PAD_LEFT);

        $kode_jadi = @$data['prefix'] . "-" . $bikin_kode;

        return $kode_jadi;
    }

    public function get_kode_produksi($data = [], $num = 5)
    {
        $kode = 1;
        $this->db->select((isset($data['select'])) ? $data['select'] : '*');
        if (isset($data['where'][0])) {
            foreach ($data['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        $this->db->order_by("SUBSTRING(KodeProduksi, 2, 5) DESC");
        if (isset($data['limit'])) $this->db->limit($data['limit']);
        $hasil = $this->db->get((isset($data['from'])) ? $data['from'] : $this->table)->row_array();
        if ($hasil && $hasil != null) {
            $kode = substr($hasil['KODE'], 0, 5) + 1;
        } else {
            $kode = 1;
        }

        $bikin_kode = str_pad($kode, $num, "0", STR_PAD_LEFT);

        $kode_jadi = @$data['prefix'] . $bikin_kode . date('y');

        return $kode_jadi;
    }

    public function insert_or_update($value = [], $table)
    {
        $value = array_map("trim", array_map("strip_tags", $value));
        $value = array_map(
            function($member) {
                if(is_numeric($member)){
                    if(strlen($member) < 2){
                        settype($member, 'integer');
                    }else{
                        settype($member, 'string');
                    } 
                }elseif($member == null){
                    settype($member, 'null');
                }else{
                    settype($member, 'string');
                }
                return $member;
            },
            $value
        );
        return $this->db->on_duplicate($table, $value);
    }
}
