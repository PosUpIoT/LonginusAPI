<?php
Class Post_model extends CI_Model{


	public function getAllPosts($offset = 0, $limit = 50){
		$query = $this->db->select('*')->from("posts")->order_by('latitude DESC', 'longitude DESC')->limit($limit, $offset)->get();

		return $query->result_array();
	}

	public function getCountAllPosts(){
		return $this->db->count_all("posts");
	}	
 
	public function getPostsSearch($myLatitude, $myLongitude, $max_distance = 10000, $offset = 0, $limit= 50){
		$this->load->model("user_model");
		$this->load->model("category_model");

		$query = $this->db->distinct()->select('id, title, description, type, status, latitude, longitude, id_category as category, id_user as user, create_date, update_date, delete_date , ( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) AS distance', FALSE)->from('posts')->having('distance <= '.$max_distance)->order_by('distance')->limit($limit, $offset)->get();

		return $this->completePost($query);

		//return $query->result_array();

	}	

	public function getCountCoordPosts($myLatitude, $myLongitude, $max_distance = 10000){
		$query = $this->db->distinct()->select('count(*) AS qtde', FALSE)->from('posts')->where('( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) <= '.$max_distance)->get();


		return $query->row_array();
	}

	public function getPostsSearchTitle($myLatitude, $myLongitude, $title, $max_distance = 10000, $offset = 0, $limit= 50){

		$query = $this->db->select('id, title, description, type, status, latitude, longitude, id_category as category, id_user as user, create_date, update_date, delete_date , ( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) AS distance')->from("posts")->like('title',$title)->having('distance <=' . $max_distance)->order_by('distance')->limit($limit, $offset)->get();

		return $this->completePost($query);

	}	

	public function getCountPostsSearchTitle($myLatitude, $myLongitude, $title, $max_distance = 10000){

		$query = $this->db->select('count(*) AS qtde')->from("posts")->like('title',$title)->where('( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) <= '.$max_distance)->get();

		return $query->row_array();

	}	

	public function getPostsSearchCategory($myLatitude, $myLongitude, $category, $max_distance = 10000, $offset = 0, $limit= 50){

		$query = $this->db->select('id, title, description, type, status, latitude, longitude, id_category as category, id_user as user, create_date, update_date, delete_date , ( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) AS distance')->from("posts")->where('id_category in (' .$category . ')')->having('distance <=' . $max_distance)->order_by('distance')->limit($limit, $offset)->get();

		return $this->completePost($query);

	}	

	public function getCountPostsSearchCategory($myLatitude, $myLongitude, $category, $max_distance = 10000){

		$query = $this->db->select('count(*) AS qtde')->from("posts")->like('title',$title)->where('( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) <= '.$max_distance)->where('id_category in (' .$category . ')')->get();

		return $query->row_array();

	}	

	public function getPostsSearchTitleCategory($myLatitude, $myLongitude, $title, $category, $max_distance = 10000, $offset = 0, $limit= 50){

		$query = $this->db->select('id, title, description, type, status, latitude, longitude, id_category as category, id_user as user, create_date, update_date, delete_date , ( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) AS distance')->from("posts")->like('title',$title)->where('id_category in (' .$category . ')')->having('distance <=' . $max_distance)->order_by('distance')->limit($limit, $offset)->get();

		return $this->completePost($query);

	}	

	public function getCountPostsSearchTitleCategory($myLatitude, $myLongitude, $title, $category, $max_distance = 10000){

		$query = $this->db->select('count(*) AS qtde')->from("posts")->like('title',$title)->where('( 6371 * acos( cos( radians('.$myLatitude.') ) * cos( radians( posts.latitude ) ) * cos( radians( posts.longitude ) - radians('.$myLongitude.') ) + sin( radians('.$myLatitude.') ) * sin( radians( posts.latitude ) ) ) ) <= '.$max_distance)->like('title',$title)->where('id_category in (' .$category . ')')->get();

		return $query->row_array();

	}	

	public function getPost($id){
		$data = array('id' => $id);
		$query = $this->db->get_where('posts',$data);
		return $query->row();
	}	

	public function newPost($data){
		return $this->db->insert('posts', $data);
	}

	public function deletePost($postId) {
		$this->db->where('id', $postId);
		return $this->db->update('posts', ['delete_date' => date('Y-m-d H:i:s')]);
	}

	public function putPost($id, $data){
		$this->db->where('id', $id);
		return $this->db->update('posts', $data);
	}



	private function completePost($query){
		$result = $query->result_array();

		foreach ($result as $key => $value) {
			$user = $this->user_model->getUserPost($value['user']);
			$category = $this->category_model->getByIdPost($value['category']);

			$result[$key]['user'] = $user;
			$result[$key]['category'] = $category;
		}
		return $result;
	}
}
?>
