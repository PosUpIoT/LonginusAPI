<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Authentication {

	static public function validateToken($token)
	{

		if($token == "aaa"){
			return TRUE;
		}else{
			return FALSE;
		}
	}


}