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
}
?>