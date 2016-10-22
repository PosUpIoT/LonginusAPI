<?php
Class Category_model extends CI_Model {


	public function getAll($page = 0, $limit = 10) {
		$query = $this->db->get('categories', $limit, ($page <= 0 ? 0 : $page*$limit));
		return $query->result_array();
	}


	public function getCategory($id){
		$data = array('id' => $id);
		$query = $this->db->get_where('categories',$data);
		return $query->row();
	}	

	public function getProperties($category)
	{
		$query = $this->db->select('categories.*, category_properties.*')
			->from('categories')
			->where(array('id' => $id))
			->get();
		if(count($query->result_array()) > 0)
		{
			return $query->result_array()[0];
		}else{
            return NULL;
		}
	}

	public function save($category) {
		if (isset($category['id'])) {
			return update($category);
		}

		return insert($category);
	}

	public function insert($category) {
		return $this->db->insert('categories', $category);
	}

	public function update($category) {
		return $this->db->update('categories', $category, array('id' => $category['id']));
	}

	public function delete($category_id) {
		//INSERIR
		return $this->db->delete('categories', array('id' => $category_id));
	}

}
?>