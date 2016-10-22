<?php
Class Property_model extends CI_Model {

	public function getAll($category_id, $page = 0, $limit = 10, $query = null) {
		if($query == null || $query == '')
		{
			$db_query = $this->db->get_where('category_properties', array('id_category'=>$category_id), $limit, ($page <= 0 ? 0 : $page*$limit));
		}else{
			$where = "id_category=".$category_id." AND (property_name='". $query ."')";
			$db_query = $this->db->where($where)->limit($limit, ($page <= 0 ? 0 : $page*$limit))->get('category_properties');
		}
		return $db_query->result_array();
	}

	public function getById($category_id, $id) {
		$query = $this->db
			->select('*')
			->from('category_properties')
			->where(array('id' => $id,'id_category' => $category_id))
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

	public function insert($property) {
		$property['create_date'] = date('Y-m-d H:i:s');
		return $this->db->insert('category_properties', $property);
	}

	public function update($property) {
		$property['update_date'] = date('Y-m-d H:i:s');
		return $this->db->update('category_properties', $property, array('id' => $property['id'], 'id_category' => $property['id_category']));
	}

	public function delete($category_id, $property_id) {
		return $this->db->update('category_properties', array('delete_date' => date('Y-m-d H:i:s')), array('id' => $property_id,'id_category'=>$category_id));
	}

}
?>
