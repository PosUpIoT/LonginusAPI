<?php
Class Picture_model extends CI_Model{
	public function getPostPictures($postId, $offset = 0, $limit = 50){
		$query = $this->db
			->select('*')
			->from("pictures")
			->where('id_post', $postId)
			->order_by('create_date', 'DESC')
			->limit($limit, $offset)->get();

		return $query->result_array();
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
 }  
?>
