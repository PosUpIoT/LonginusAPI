<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Authentication {




	static public function validateToken($token)
	{
		$CI =& get_instance();

		$CI->load->model("user_model");

		if($CI->user_model->getToken($token)){
			return TRUE;
		}else{
			return FALSE;
		}
	}


	static public function createToken($email, $password, $saltValue){
		$salt = $email . $password . $saltValue;
		$salt = md5($salt);

		return $salt;
	}


}