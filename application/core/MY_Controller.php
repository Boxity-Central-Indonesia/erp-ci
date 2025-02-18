<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
       
    }
    function render_public($content, $data = null)
    {
        //  if (empty(website_config('is_website_under_maintenance')) || website_config('is_website_under_maintenance') != 'on') {
        //  	exit('Maintance');
        // }
        $this->visitor_counter();
        $data['config'] = $this->config->item('web');
        $data['content'] = $this->load->view($content, $data, true);
        $this->load->view('public/main', $data);
    }
    function render_auth($content, $data = null)
    {
        $data['config'] = $this->config->item('web');
        $data['content'] = $this->load->view($content, $data, true);
        $this->load->view('public/auth_view', $data);
    }
    function render_admin($content, $data = null)
    {
        $data['config'] = $this->config->item('web');
        $data['content'] = $this->load->view($content, $data, true);
        $this->load->view('admin/main', $data);
    }
    function visitor_counter()
    {
        $this->load->model('visitor_model');
        if ($this->agent->is_browser()) {
            $user_agent = $this->agent->browser() . ' ' . $this->agent->version();
        } elseif ($this->agent->is_robot()) {
            $user_agent = $this->agent->robot();
        } elseif ($this->agent->is_mobile()) {
            $user_agent = $this->agent->mobile();
        } else {
            $user_agent = 'Unidentified';
        }
        $insert = false;
        if ($this->visitor_model->get_row(['ip_address' => $this->input->ip_address(), 'user_agent' => ucwords(explode(' ', $user_agent)[0]), 'created_at' => date('Y-m-d')]) == true) {
            $insert = false;
        } else {
            $insert =  $this->visitor_model->insert([
                'ip_address' => $this->input->ip_address(),
                'user_agent' => ucwords(explode(' ', $user_agent)[0]),
                'counter' => 1,
                'created_at' => date('Y-m-d')
            ]);
        }
        return $insert;
    }
    protected function response($data = [], $http_code = null)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
        $this->output->set_status_header($http_code);
        $this->output->_display();
        exit();
    }
}
