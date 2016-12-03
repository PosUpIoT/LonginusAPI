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

	public function getUserPost($id){
		$data = array('id' => $id);
		$query = $this->db->select('id,name,email,phone')->from('users')->where('id',$id)->get();
		if(count($query->result_array()) > 0)
		{
			return $query->result_array()[0];
		}else{
            return NULL;
		}
	}	


	public function insert_user($data) {
		return $this->db->insert('users', $data);
	}

	public function facebook_login($user, $access_token)
	{

		try{
		    //checar se ja existe um user com provider facebook e este ID 
			if($this->check_social_network_exists($user['id'],'facebook'))
			{
				//refresh user
				$this->db->set('name', $user['name']);
				$this->db->set('email', $user['email']);
				$this->db->set('avatar', 'https://graph.facebook.com/'.$user['id'].'/picture?type=large');
				$this->db->set('social_network_access_token', $access_token);
				$this->db->where('social_network_id', $user['id']);
				$this->db->where('provider', 'facebook');
				$this->db->update('users'); 
			}else{
				//new user
				$data = array(
			            	'role'=>1,
			            	'name'=> $user['name'],
			                'email' => $user['email'],
			                'social_network_id' => $user['id'],
			                'social_network_access_token'=>$access_token,
			                'password' => '',
			                'phone' => '',
			                'provider' => 'facebook',
			                'avatar'=>'https://graph.facebook.com/'.$user['id'].'/picture?type=large',
			                'create_date' => date('Y-m-d H:i:s')
			    );
			    $retorno = $this->user_model->insert_user($data);
			}
		    //logar
			$data = array('id' => $user['id'], 'provider'=>'facebook');
			$query = $this->db->get_where('users', $data);

			if($query->num_rows() > 0){
				$newdata = array(
		        'name'  => $query->first_row()->name,
		        'email'     => $query->first_row()->email,
		        'src'=>'facebook',
		        'logged_in' => true
				);
				$this->session->set_userdata($newdata);
				return true;
			}
		}catch(Exception $e){
			return $e;
		}

	}

	public function login($email, $password) {
		$data = array('email' => $email, 'password' => md5($password), 'provider'=>'internal');
		$query = $this->db->get_where('users', $data);

		if($query->num_rows() > 0){
			$newdata = array(
	        'name'  => $query->first_row()->name,
	        'email'     => $query->first_row()->email,
	        'src'=>'internal',
	        'logged_in' => true
			);
			$this->session->set_userdata($newdata);
			return true;
		}

		return false;
	}

	public function getToken($token){
		$query = $this->db->select('1')->from("users")->where('api_token', $token)->get();

		if($query->num_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}



}
?>