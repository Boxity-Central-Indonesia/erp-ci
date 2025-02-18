<?php
defined('BASEPATH') or exit('No direct script access allowed');

## update => https://github.com/Rizki36/datatables-codeigniter-3/blob/master/application/models/M_Datatables.php
## usage => https://github.com/Rizki36/datatables-codeigniter-3/blob/master/application/controllers/Datatables.php
class M_Datatables extends MY_Model
{

    public function __construct()
    {
        parent::__construct(null);
    }

    public function get_data($postData = null)
    {
        $response = array();

        $table = $postData['table'];
        $selectedColumn = isset($postData['selected_column']) ? $postData['selected_column'] : '*';

        ## custom order
        $useOrderByDatatables = isset($postData['use_order_by_datatable']) ? $postData['use_order_by_datatable'] : true; // disable order by datatable
        $useOrderByDatatables = isset($postData['order']) ? $useOrderByDatatables : false;
        $useCustomOrder = isset($postData['use_custom_order']) ? $postData['use_custom_order'] : false; // use it if you want custom order
        $customColumnNameOrder = isset($postData['custom_column_name_order']) ? $postData['custom_column_name_order'] : '';
        $customColumnSortOrder = isset($postData['custom_column_sort_order']) ? $postData['custom_column_sort_order'] : '';

        ## order by datatable
        if ($useOrderByDatatables === true) {
            $columnIndexOrder = $postData['order'][0]['column']; // Column index order
            $columnIndex = $postData['columns'][$columnIndexOrder]['data']; // Column index table
            $columnName = $postData['display_column'][$columnIndex]; // Column name
            $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        }

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page

        $whereQuery = "";

        ## Search  
        $searchValue = $postData['search']['value']; // Search value
        $search_arr = array();
        if ($searchValue != '') {
            $searchQuery = [];
            $displayColumn = $postData['display_column'];
            foreach ($displayColumn as $indexColumn => $column) {
                if ($column !== false) { // check display column is true column
                    if ($postData['columns'][$indexColumn]['searchable'] === 'true') { // check is searchable column
                        $searchQuery[] = "$column Like '%$searchValue%'";
                    }
                }
            }

            $searchQuery = implode(' OR ', $searchQuery);
            $search_arr[] = "($searchQuery)";
        }


        ## Filter
        if (isset($postData['filters'])) {
            /**
             * $postData['filters'] = [
             *    "key <= 'value'", // string filter, for column (last then, greather then, etc) value 
             *    ['key' => 'value'] // array filter, for column equal value
             * ]
             */
            foreach ($postData['filters'] as $filter) {
                if (is_string($filter)) {
                    $search_arr[] = $filter;
                } else if (is_array($filter)) {
                    foreach ($filter as $key => $value) {
                        $search_arr[] = "$key = '$value'";
                    }
                }
            }
        }
        if (count($search_arr) > 0) {
            $whereQuery = implode(" and ", $search_arr);
        }



        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        if (isset($postData['join'][0])) {
            foreach ($postData['join'] as $key => $value) {
                $this->db->join($value['table'], $value['on'], $value['param']);
            }
        }
        if (isset($postData['where'][0])) {
            foreach ($postData['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        $totalRecords = 0;
        if (isset($postData['group_by'])) {
            $this->db->group_by($postData['group_by']);
            $records = $this->db->get($table)->num_rows();
            $totalRecords = $records;
        } else {
            $records = $this->db->get($table)->result();
            $totalRecords = $records[0]->allcount;
        }

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        if (isset($postData['join'][0])) {
            foreach ($postData['join'] as $key => $value) {
                $this->db->join($value['table'], $value['on'], $value['param']);
            }
        }
        if (isset($postData['where'][0])) {
            foreach ($postData['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        if ($whereQuery != '') {
            $this->db->where($whereQuery);
        }
        if (isset($postData['group_by'])) {
            $this->db->group_by($postData['group_by']);
            $records = $this->db->get($table)->num_rows();
            $totalRecordwithFilter = $records;
        } else {
            $records = $this->db->get($table)->result();
            $totalRecordwithFilter = $records[0]->allcount;
        }

        ## Fetch records
        $this->db->select($selectedColumn);
        if (isset($postData['join'][0])) {
            foreach ($postData['join'] as $key => $value) {
                $this->db->join($value['table'], $value['on'], $value['param']);
            }
        }
        if (isset($postData['where'][0])) {
            foreach ($postData['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        if ($whereQuery != '') {
            $this->db->where($whereQuery);
        }
        if (isset($postData['group_by'])) $this->db->group_by($postData['group_by']);
        if ($useCustomOrder) {
            $this->db->order_by($customColumnNameOrder, $customColumnSortOrder);
        }
        if ($useOrderByDatatables) {
            $this->db->order_by($columnName, $columnSortOrder);
        }
        if ($rowperpage > 0) {
            $this->db->limit($rowperpage, $start);
        }
        $records = $this->db->get($table)->result();


        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "records" => $records
        );

        return $response;
    }

    public function get_data_assoc($postData = null)
    {
        $response = array();

        $table = $postData['table'];
        $selectedColumn = isset($postData['selected_column']) ? $postData['selected_column'] : '*';

        ## custom order
        $useOrderByDatatables = isset($postData['use_order_by_datatable']) ? $postData['use_order_by_datatable'] : true; // disable order by datatable
        $useOrderByDatatables = isset($postData['order']) ? $useOrderByDatatables : false;
        $useCustomOrder = isset($postData['use_custom_order']) ? $postData['use_custom_order'] : false; // use it if you want custom order
        $customColumnNameOrder = isset($postData['custom_column_name_order']) ? $postData['custom_column_name_order'] : '';
        $customColumnSortOrder = isset($postData['custom_column_sort_order']) ? $postData['custom_column_sort_order'] : '';

        ## order by datatable
        if ($useOrderByDatatables === true) {
            $columnIndexOrder = $postData['order'][0]['column']; // Column index order
            $columnName = $postData['columns'][$columnIndexOrder]['data']; // Column name
            $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        }

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page

        $whereQuery = "";

        ## Search  
        $searchValue = $postData['search']['value']; // Search value
        $search_arr = array();
        if ($searchValue != '') {
            $searchQuery = [];
            $searchColumn = $postData['display_column'];
            if (isset($postData['search_column'])) $searchColumn = $postData['search_column']; // migration from $postData['display_column'] to $postData['search_column']
            foreach ($searchColumn as $column) {
                if ($column !== false) { // check display column is true column
                    $searchQuery[] = "$column Like '%$searchValue%'";
                }
            }

            $searchQuery = implode(' OR ', $searchQuery);
            $search_arr[] = "($searchQuery)";
        }


        ## Filter
        if (isset($postData['filters'])) {
            /**
             * $postData['filters'] = [
             *    "key <= 'value'", // string filter, for column (last then, greather then, etc) value 
             *    ['key' => 'value'] // array filter, for column equal value
             * ]
             */
            foreach ($postData['filters'] as $filter) {
                if (is_string($filter)) {
                    $search_arr[] = $filter;
                } else if (is_array($filter)) {
                    foreach ($filter as $key => $value) {
                        $search_arr[] = "$key = '$value'";
                    }
                }
            }
        }
        if (count($search_arr) > 0) {
            $whereQuery = implode(" and ", $search_arr);
        }



        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        if (isset($postData['selectColumnCount'])) $this->db->select($postData['selectColumnCount']);

        if (isset($postData['join'][0])) {
            foreach ($postData['join'] as $key => $value) {
                $this->db->join($value['table'], $value['on'], $value['param']);
            }
        }
        if (isset($postData['where'][0])) {
            foreach ($postData['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        if (isset($postData['whereIn'])) {
            foreach ($postData['whereIn'] as $key => $value) {
                $this->db->where_in($value['param'], $value['value']);
            }
        }
        if (isset($postData['whereEscape'][0])) {
            foreach ($postData['whereEscape'] as $key => $value) {
                $this->db->where($value, null, false);
            }
        }
        if (isset($postData['having'][0])) {
            foreach ($postData['having'] as $key => $value) {
                $this->db->having($value);
            }
        }
        $totalRecords = 0;
        if (isset($postData['group_by'])) {
            $this->db->group_by($postData['group_by']);
            $records = $this->db->get($table)->num_rows();
            $totalRecords = $records;
        } else {
            $records = $this->db->get($table)->result();
            $totalRecords = $records[0]->allcount;
        }

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        if (isset($postData['selectColumnCount'])) $this->db->select($postData['selectColumnCount']);


        if (isset($postData['join'][0])) {
            foreach ($postData['join'] as $key => $value) {
                $this->db->join($value['table'], $value['on'], $value['param']);
            }
        }
        if (isset($postData['where'][0])) {
            foreach ($postData['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        if (isset($postData['whereIn'])) {
            foreach ($postData['whereIn'] as $key => $value) {
                $this->db->where_in($value['param'], $value['value']);
            }
        }
        if (isset($postData['whereEscape'][0])) {
            foreach ($postData['whereEscape'] as $key => $value) {
                $this->db->where($value, null, false);
            }
        }
        if (isset($postData['having'][0])) {
            foreach ($postData['having'] as $key => $value) {
                $this->db->having($value);
            }
        }
        if ($whereQuery != '') {
            $this->db->where($whereQuery);
        }
        if (isset($postData['group_by'])) {
            $this->db->group_by($postData['group_by']);
            $records = $this->db->get($table)->num_rows();
            $totalRecordwithFilter = $records;
        } else {
            $records = $this->db->get($table)->result();
            $totalRecordwithFilter = $records[0]->allcount;
        }

        ## Fetch records
        $this->db->select($selectedColumn);
        if (isset($postData['join'][0])) {
            foreach ($postData['join'] as $key => $value) {
                $this->db->join($value['table'], $value['on'], $value['param']);
            }
        }
        if (isset($postData['where'][0])) {
            foreach ($postData['where'] as $key => $value) {
                $this->db->where($value);
            }
        }
        if (isset($postData['whereIn'])) {
            foreach ($postData['whereIn'] as $key => $value) {
                $this->db->where_in($value['param'], $value['value']);
            }
        }
        if ($whereQuery != '') {
            $this->db->where($whereQuery);
        }
        if (isset($postData['group_by'])) $this->db->group_by($postData['group_by']);
        if ($useCustomOrder) {
            $this->db->order_by($customColumnNameOrder, $customColumnSortOrder);
        }
        if ($useOrderByDatatables) {
            $orderColumn = $postData['display_column'];
            if (in_array($columnName, $orderColumn)) $this->db->order_by($columnName, $columnSortOrder);
        }
        if (isset($postData['having'][0])) {
            foreach ($postData['having'] as $key => $value) {
                $this->db->having($value);
            }
        }
        if ($rowperpage > 0) {
            $this->db->limit($rowperpage, $start);
        }
        $records = $this->db->get($table)->result();


        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "records" => $records
        );

        return $response;
    }
}
