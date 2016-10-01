<?php
Class Post_model extends CI_Model{
	public function getPosts($latitude, $longitude){
		$query = $this->db->get('posts');
		return $query->result_array();
	}
	public function getPostsInit($latitudeIni, $longitudeIni,$latitudeFim, $longitudeFim){
		$query = $this->db->get('posts');
		return $query->result_array();
	}	

	public function getPost($id){
		$data = array('id' => $id);
		$query = $this->db->get_where('posts',$data);
		return $query->result_array();
	}	

	public function newPost($data){
		$this->db->insert('posts', $data);
	}
}
?>