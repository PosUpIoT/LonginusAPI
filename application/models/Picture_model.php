<?php 
Class Pictures_model extends CI_Model{ 
        public function getAllPictures($offset = 0, $limit = 50){ 
                $query = $this->db->select('*')->from("pictures")->order_by('create_date', 'DESC')->limit($limit, $offset)->get(); // seleciona todas as figuras por ordem de data de criacao
 
                return $query->result_array(); 
        } 
 
        public function getCountAllPictures(){ 
                 return $this->db->count_all("pictures"); 
         }  
        
         	public function getPicturesSearch($idPicture, $offset = 0, $limit= 50){ 
  
 		$query = $this->db->distinct()->select('*', FALSE)->from('pictures')->where('id_post = '.$idPicture)->limit($limit, $offset)->get(); 
  
  
 		return $query->result_array(); 
  
 	}	 
  
 	public function getPictures($id){ 
 		$data = array('id' => $id); 
 		$query = $this->db->get_where('pictures',$data); 
 		return $query->row(); 
 	}	 
  
 	public function newPictures($data){  						// adicionar nova figura 
 		return $this->db->insert('pictures', $data); 
 	} 
        
        	public function deletePictures($id) {   					// deletar figura 
 		$this->db->where('id', $id); 
 		return $this->db->update('pictures', ['delete_date' => date('Y-m-d H:i:s')]); 
 	} 
  
 	public function putPicture($id, $data){ 
 		$this->db->where('id', $id); 
 		$this->db->update('pictures', $data); 
 		return $this->db->update; 
 	} 
 } 
 ?>
