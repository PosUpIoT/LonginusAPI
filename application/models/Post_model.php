<?php
Class Post_model extends CI_Model{

	// Pega todos os posts somente trabahand com o offset e o limit
	public function getAllPosts($offset = 0, $limit = 50){
		$query = $this->db->select('*')->from("posts")->order_by('create_date', 'DESC')->limit($limit, $offset)->get();

		return $query->result_array();
	}

    // Realiza a contagem dos posts
	public function getCountAllPosts(){
		return $this->db->count_all("posts");
	}	
 	
 	// Procura por um posts repassando latitude, longitude , distancia e offset
	public function getPostsSearch($myLatitude, $myLongitude, $max_distance = 10, $offset = 0, $limit= 50){

		$query = $this->db->distinct()->select('* , ( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) AS distance', FALSE)->from('posts')->having('distance <= '.$max_distance)->limit($limit, $offset)->get();


		return $query->result_array();

	}	

	public function getCountCoordPosts($myLatitude, $myLongitude, $max_distance = 10){
		$query = $this->db->distinct()->select('count(*) AS qtde', FALSE)->from('posts')->where('( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) <= '.$max_distance)->get();


		return $query->row_array();
	}

    // Pega um post de acordo com o id
	public function getPost($id){
		$data = array('id' => $id);
		$query = $this->db->get_where('posts',$data);
		return $query->row();
	}	
     
    //Realiza um novo posts
	public function newPost($data){
		return $this->db->insert('posts', $data);
	}

	// Deleta um posts
	public function deletePost($postId) {
		$this->db->where('id', $postId);
		return $this->db->update('posts', ['delete_date' => date('Y-m-d H:i:s')]);
	}
    
    // Altera um posts repassando o id e os dados do posts
	public function putPost($id, $data){
		$this->db->where('id', $id);
		return $this->db->update('posts', $data);
	}
}
?>
