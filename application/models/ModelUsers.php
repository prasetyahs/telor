<?php

    Class ModelUsers extends CI_Model{

        public function getAllDataUser($username,$password)
        {
            $sql = "SELECT tb_users.username,fname,lname,email,password,type,id_address,street,shop,kordinat,id_agent,id_address,is_verified
                        FROM tb_users,tb_address,tb_agent
                        WHERE 
                            tb_users.username = tb_address.username AND
                            tb_users.username = ? AND
                            tb_users.username = tb_agent.username AND
                            tb_users.password = ?";
            return $this->db->query($sql,array($username,md5($password)))->row_array();
        }

        public function getAllDataCourier($username,$password)
        {
            $sql = "SELECT tb_users.username,fname,lname,email,password,type,id_courier
                        FROM tb_users,tb_courier
                        WHERE tb_users.username = ? AND
                            tb_users.password = ?";
            return $this->db->query($sql,array($username,md5($password)))->row_array();
        }

        public function insertDataUsers($username,$fname,$lname,$email,$password)
        {
            $sql = "INSERT INTO tb_users VALUES(?,?,?,?,?,?)";
            return $this->db->query($sql,array($username,$fname,$lname,$email,md5($password),"agent"));
        }

        public function verifyUsername($username){
            $sql = "SELECT * from tb_users WHERE username = ?";
            return $this->db->query($sql,$username)->row_array();
        }

        public function verifyEmail($email){
            $sql = "SELECT * from tb_users WHERE email = ?";
            return $this->db->query($sql,$email)->row_array();
        }

        public function insertToken($email,$token){
            $sql = "INSERT INTO user_token (email,token) VALUES(?,?)";
            return $this->db->query($sql,array($email,$token));
        }

        public function changePassword($email,$password){
            $sql = "UPDATE tb_users SET password = ?
                        WHERE email = ?";
            return $this->db->query($sql,array(md5($password),$email));
        }

        public function verifyOTP($email,$otp){
            $sql = "SELECT * from user_token WHERE email = ? AND token = ? ";
            return $this->db->query($sql,array($email,$otp))->row_array();
        }

        public function changePasswordProfile($username,$newPassword){
            $sql = "UPDATE tb_users SET password = ?
                      WHERE username = ?";
            return $this->db->query($sql,array(md5($newPassword),$username));
        }  
        
        public function changeAddress($username,$address){
            $sql = "UPDATE tb_address SET street = ?
                        WHERE username = ?";
            return $this->db->query($sql,array($address,$username));
        }

        public function changeShop($username,$nameShop){
            $sql = "UPDATE tb_agent SET shop = ?
                        WHERE username = ?";
            return $this->db->query($sql,array($nameShop,$username));
        }

        public function changeProfile($username,$fname,$lname){
            $sql = "UPDATE tb_users SET fname = ?,lname = ?
                        WHERE username = ?";
            return $this->db->query($sql,array($fname,$lname,$username));
        }

        public function changeEmail($username,$newEmail){
            $sql = "UPDATE tb_users SET email = ?
                        WHERE username = ?";
            return $this->db->query($sql,array($newEmail,$username));
        }

        public function changeVerification($username){
            $sql = "UPDATE tb_agent SET is_verified = ?
                        WHERE username = ?";
            return $this->db->query($sql,array(1,$username));
        }
    }