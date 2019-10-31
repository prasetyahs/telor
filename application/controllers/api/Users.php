<?php

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';

    Class Users extends CI_Controller{

        use REST_Controller {
            REST_Controller::__construct as private __resTraitConstruct;
        }

        public function __construct()
        {
           // Construct the parent class
           parent::__construct();
           $this->__resTraitConstruct();
   
           //$this->load->library('session');
           $this->load->model('ModelUsers');
           $this->load->model('ModelAddress');
           $this->load->model('ModelAgent');
   
           
        }
        
       

        public function login_post()
        {
            $username = $this->post('username');
            $password = $this->post('password');

            if(empty($username))
            {
                $this->response(['message' => "Required Username"],200);
            }

            if(empty($password))
            {
                $this->response(['message' => "Required Password"],200);
                
            }

            $checkUsername = $this->db->get_where('tb_users',array('username'=> $username))->result_array();
           
            if($checkUsername == null){
                $this->response(['message' => "Username not Found !"],200);
            }

            $checkUser = $this->ModelUsers->getAllDataUser($username,$password);
            if($checkUser == null)
            {
                $this->response(['message' => "Wrong Username or Password !"],200);
            }else{
                $this->set_response([
                    'message'       => "Success",
                    'userdata'      => $checkUser
                ],200);
            }


        }

        public function loginCourier(){
            $username = $this->post('username');
            $password = $this->post('password');
            
        }

        public function register_post()
        {
            $username = $this->post('username');
            $fname = $this->post('fname');
            $lname = $this->post('lname');
            $email = $this->post('email');
            $password = $this->post('password');

            $cekUsername = $this->ModelUsers->verifyUsername($username);
            $cekEmail = $this->ModelUsers->verifyEmail($email);
            
            if(empty($username) || empty($email) || empty($password)){
                $this->response(['message' => "Harap lengkapi data yang ada !"],200);
            }


            if($cekUsername != null)
            {
                $this->response(['message' => "Username sudah digunakan !"],200);
            }else{
                
            }

            if($cekEmail != null)
            {
                $this->response(['message'=> "Email sudah digunakan !"],200);
            }

            $insertUser = $this->ModelUsers->insertDataUsers($username,$fname,$lname,$email,$password);
            

            if($insertUser){
               $this->response([
                   'message' => "Register sukses, Silahkan Masukan detail alamat",
                   'user_data'  => $username
               ],200);
            }

        }

        public function address_post(){
            $username = $this->post('username');
            $street = $this->post('street');
            $id_agent = $this->post('id_agent');
            $nameShop = $this->post('name_shop');
            $coordinate = $this->post('coordinate');

            if(empty($username) || empty($street) || empty($nameShop) || empty($coordinate)){
                $this->response([
                    'message'   => "Tolong lengkapi data!"
                ],200);
            }

            $insertAddress = $this->ModelAddress->insertDataAddress($street,$username);
            $insertAgent = $this->ModelAgent->insertDataAgent($id_agent,$username,$nameShop,$coordinate);

            if($insertAddress && $insertAgent){
                $this->response([
                    'message'   => "Register sukses"
                ],200);
            }
            
        }

        public function forget_post(){
            $email = $this->post('email');
            if(empty($email)){
                $this->response([
                    'message'   => "Tolong masukan email !",
                    'status'=>false
                ],200);
            }

            $cekEmail = $this->ModelUsers->verifyEmail($email);
            if($cekEmail == null){
                $this->response([
                    'message'   => "email tidak terdaftar",
                    'status'=>false
                ],200);
            }

            $token = mt_rand(100000, 999999);
            $response = "Silahkan cek email untuk reset password";
            $header = "Reset Password";
            $inserToken = $this->ModelUsers->insertToken($email,$token);
            if($inserToken){
                $this->_sendEmail($email,$token,$response,$header);
            }

        }

        public function _sendEmail($email,$token,$response,$header){
            $this->load->library('email');
            $data['token'] = $token;
            $data['header'] = $header;
            $message = $this->load->view('email/emailmoeladi',$data,true);
            $config = [
                'protocol'  => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_user' => 'webzakat1122@gmail.com',
                'smtp_pass' => 'Webzakat123',
                'smtp_port' => 465,
                'mailtype' => 'html',
                'charset'   => 'utf-8',
                'newline'   => "\r\n" 
            ];
            $this->email->initialize($config);
            $this->email->from('AdminPTMoeladi@gmail.com','PT MOELADI PETERNAKAN');
            $this->email->to($email);
            $this->email->subject($header);
            $this->email->message($message);
        
            if($this->email->send()){
                $this->response([
                    'message'   => $response,
                    'status'=>true
                ],200);
            }else{
                $this->response([
                    'message'   => "Reset Password gagal",
                    'status'=>false
                ],200);
            }
            

        }

        public function verifyotp_post(){
            $email = $this->post('email');
            $otp = $this->post('otp');
            if(empty($otp)){
                $this->response([
                    'message'   => "OTP tidak boleh kosong",
                    'status'=>false
                ],200);
            }

            $verifyOTP = $this->ModelUsers->verifyOTP($email,$otp);
            if($verifyOTP != null){
                $this->response([
                    'message' => "Kode OTP benar",
                    'status'=>true
                ],200);
            }else{
                $this->response([
                    'message' => "Kode OTP tidak cocok",
                    'status'=>false
                ],200);
            }
        }

        public function reset_post(){
            $email = $this->post('email');
            $password = $this->post('password');

            if(empty($email)){
                $this->response([
                    'message'   => "Harap isi Email",
                    'status'=>false
                ],200);
            }

            if(empty($password)){
                $this->response([
                    'message'   => "Harap isi Password",
                    'status'=>false
                ],200);
            }

            $changePassword = $this->ModelUsers->changePassword($email,$password);
            if($changePassword){
                $this->response([
                    'message'   => "Password berhasil diganti",
                    'status'=>true
                ],200);
            }
        }

        public function changePassword_post(){

            $username = $this->post('username');
            $oldPassword = md5($this->post('oldPassword'));
            $newPassword = $this->post('newPassword');
            $checkPassword = $this->db->get_where('tb_users',array('username'  => $username))->row_array();
            $confirmPassword = $checkPassword['password'];
            if($oldPassword == $confirmPassword){
                $changePassword = $this->ModelUsers->changePasswordProfile($username,$newPassword);
                if($changePassword){
                    $this->response([
                        'message' => "Password berhasil diubah",
                        'status' => true
                    ],200);
                }
            }else{
                $this->response([
                    'message'   => "Password lama tidak sesuai",
                    'status'    => false
                ],200);
            } 
            
        }

        public function changeAddress_post(){
            $username = $this->post('username');
            $fname = $this->post('fname');
            $lname = $this->post('lname');
            $nameShop = $this->post('nameShop');
            $newEmail = $this->post('email');
            $address = $this->post('address');

            $cekEmail = $this->db->get_where('tb_users',array('username'=>$username))->row_array();
            $rowEmail = $cekEmail['email'];
            if($newEmail == $rowEmail){
                $this->ModelUsers->changeAddress($username,$address);
                $this->ModelUsers->changeShop($username,$nameShop);
                $this->ModelUsers->changeProfile($username,$fname,$lname);
                $this->response([
                    'message'   => "Ubah Profile Berhasil",
                    'status'    => true
                ],200);

            }else{
                $cekEntryEmail = $this->db->get_where('tb_users',array('email' => $newEmail))->row_array();
                if($cekEntryEmail == null){
                    $this->ModelUsers->changeAddress($username,$address);
                    $this->ModelUsers->changeShop($username,$nameShop);
                    $this->ModelUsers->changeProfile($username,$fname,$lname);
                    $this->ModelUsers->changeEmail($username,$newEmail);
                    $this->response([
                        'message'   => "Ubah Profiles berhasil",
                        'status'    => true
                    ],200);
                }else{
                        $this->response([
                            'message'   => "Email sudah ada,Silahkan masukan email yang lain"
                        ],200);
                   
                   
                }
                
            }
        }

        public function emailVerification_post(){
            $newEmail = $this->post('email');
            $token = mt_rand(100000, 999999);
            $response = "Silahkan cek email untuk Konfirmasi Email";
            $header = "Konfirmasi Email";
            $this->_sendEmail($newEmail,$token,$response,$header);
        }

        public function verifyOtpEmail_post(){
            $otp = $this->post('otp');
            $email = $this->post('email');
            $username = $this->post('username');

            if(empty($otp)){
                $this->response([
                    'message'   => "OTP tidak boleh kosong"
                ],200);
            }

            $cekOtp = $this->ModelUsers->verifyOTP($email,$otp);
            if($cekOtp != null){
                $changeVerification = $this->ModelUsers->changeVerification($username);
                if($changeVerification){
                    $this->response([
                        'message'   => "Email berhasil dikonfirmasi",
                        'status'    => true
                    ],200);
                }
            }else{
                $this->response([
                    'message'   => "OTP tidak cocok",
                    'status'    => false
                ],200);
            }
        }


    }