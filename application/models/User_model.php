<?php
Class User_model extends CI_Model{
	public function getUsers(){
		//$query = $this->db->query("SELECT * FROM longinusapp.users;");
		$query = $this->db->get('users');
		return $query->result_array();
	}

	public function getUser($id){
		$data = array('id' => $id);
		$query = $this->db->get_where('users',$data);
		return $query->row();
	}	

	public function insertUser($data){
		$this->db->insert('users', $data);
	}

	public function login($email, $password){
		$data = array('email' => $email, 'password' => md5($password));
		$query = $this->db->get_where('users',$data);
		

		if($query->num_rows() > 0){
			return true;
		}else{
			return false;
		}
		
	}	

}
?>