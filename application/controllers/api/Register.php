<?php
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
    class Register extends CI_Controller
    {
        use REST_Controller {
            REST_Controller::__construct as private __resTraitConstruct;
        }

        public function __construct()
        {
            parent::__construct();
            $this->__resTraitConstruct();
            $this->load->model('ModelAddress');
            $this->load->model('ModelUsers');
        }

        public function index_post()
        {
            $username = $this->post('username');
            $fname = $this->post('fname');
            $lname = $this->post('lname');
            $email = $this->post('email');
            $password = $this->post('password');
            $type = $this->post('type');
            $street = $this->post('street');
        
            
            if(empty($username) || empty($fname) || empty($lname) || empty($email) || empty($password) || empty($type) || empty($street))
            {
                $this->response(['message' => 'Required Value'],404);
            }

            $insertUser = $this->ModelUsers ->insertDataUsers($username,$fname,$lname,$email,$password,$type);
            $insertAddress = $this->ModelAddress->insertDataAddress($street,$username);

            if($insertUser && $insertAddress)
            {
                $this->response(['message' => "Register Succesfully"],200);
            }


        }
    }