<?php

    class ModelAddress extends CI_Model{

        public function insertDataAddress($street,$username)
        {
            $sql = "INSERT INTO tb_address VALUES(?,?,?)";
            return $this->db->query($sql,array("",$street,$username));
        }
    }