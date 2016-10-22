<?php 
Class Pictures_model extends CI_Model{ 
        public function getAllPictures($offset = 0, $limit = 50){ 
                $query = $this->db->select('*')->from("pictures")->order_by('create_date', 'DESC')->limit($limit, $offset)->get(); // seleciona todas as figuras por ordem de data de criacao
 
                return $query->result_array(); 
        } 
 
        public function getCountAllPictures(){ 
                 return $this->db->count_all("pictures"); 
         }        
   
 
 ?>
