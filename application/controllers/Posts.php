<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Authentication.php';

class Posts extends REST_Controller{

	function __construct()
	{
		// Construct the parent class
		parent::__construct();



		// Configure limits on our controller methods
		// Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
		$this->methods['posts_get']['limit'] = 500; // 500 requests per hour per user/key
		$this->methods['posts_post']['limit'] = 100; // 100 requests per hour per user/key
		$this->methods['posts_delete']['limit'] = 50; // 50 requests per hour per user/key
		$this->methods['posts_put']['limit'] = 50; // 50 requests per hour per user/key

		$this->load->model("post_model");
		$this->load->model("picture_model");
		$this->load->model("user_model");
		$this->load->model("category_model");


	}

	public function posts_get() {


		$id = $this->get('id');
		$query = $this->query();

		$posts = NULL;

		if ($id === NULL) {
			if (count($query) > 0) {
				if (isset($query['lon']) && isset($query['lat'])){
					$lon = $query['lon'];
					$lat = $query['lat'];
				}
				if (isset($query['offset']) && isset($query['limit'])){
					$offset = $query['offset'];
					$limit = $query['limit'];
				}
				if(isset($query['title'])){
					$titleSearch = $query['title'];
				}
				if(isset($query['category'])){
					$categorySearch = $query['category'];
				}
				if(isset($query['distance'])){
					$distance = $query['distance'];
				}				
			}

            if (!isset($limit)){
                $limit = 50;
            }

            if (!isset($offset)){
                $offset = 0;
            } 

			if (!isset($distance)){
				$distance = 10000;
			}

			if ((!isset($lat) && !isset($lon)) && !isset($titleSearch) && !isset($categorySearch)){
				$result = $this->post_model->getAllPosts($offset, $limit);
				$countAll = $this->post_model->getCountAllPosts();
			} else {
				if (isset($lat) && isset($lon)){
					if (isset($titleSearch) && !isset($categorySearch)){
						$result = $this->post_model->getPostsSearchTitle($lat, $lon, $titleSearch, $distance, $offset, $limit);
						$qtde = $this->post_model->getCountPostsSearchTitle($lat, $lon, $titleSearch, $distance);

						$countAll = $qtde['qtde'];
					}else{
						if (isset($categorySearch) && !isset($titleSearch)){
							$result = $this->post_model->getPostsSearchCategory($lat, $lon, $categorySearch, $distance, $offset, $limit);
							$qtde = $this->post_model->getCountPostsSearchCategory($lat, $lon, $categorySearch, $distance);

							$countAll = $qtde['qtde'];
						}else{
							if (isset($titleSearch) && isset($categorySearch)){
								$result = $this->post_model->getPostsSearchTitleCategory($lat, $lon, $titleSearch, $categorySearch, $distance, $offset, $limit);
								$qtde = $this->post_model->getCountPostsSearchTitleCategory($lat, $lon, $titleSearch, $categorySearch, $distance);

								$countAll = $qtde['qtde'];
							}else{

								$result = $this->post_model->getPostsSearch($lat, $lon, $distance, $offset, $limit);
								$qtde = $this->post_model->getCountCoordPosts($lat, $lon, $distance);

								$countAll = $qtde['qtde'];
							}
						}
					}

				}else{
					$this->response([
						'status'  => REST_Controller::HTTP_NOT_FOUND,
						'errorCode'=> 404,
						'message' => 'No posts were found, enter with latitude and longitude to filter'
					], REST_Controller::HTTP_NOT_FOUND);
				}
			}

			$posts = [
				'metadata' =>
				[
					'resultset' => 
					[
						'count' => $countAll,
						'offset' => $offset,
						'limit' => $limit,
					]
				],
				'results' => $result
			];

			if ($posts)
			{
				$this->response($posts, REST_Controller::HTTP_OK);
			}
			else
			{
				$this->response([
					'status'  => REST_Controller::HTTP_NOT_FOUND,
					'errorCode'=> 404,
					'message' => 'No posts were found'
				], REST_Controller::HTTP_NOT_FOUND);
			}

		}else{
			$id = (int) $id;
			if ($id <= 0)
			{
				$this->set_response([
					'status'  => REST_Controller::HTTP_NOT_FOUND,
					'errorCode'=> 404,
					'message' => 'Post could not be found'
				], REST_Controller::HTTP_NOT_FOUND);
			}else{
				$posts = $this->post_model->getPost($id);

				if (!empty($posts))
				{
					$this->set_response($posts, REST_Controller::HTTP_OK);
				}
				else
				{
					$this->set_response([
						'status'  => REST_Controller::HTTP_NOT_FOUND,
						'errorCode'=> 404,
						'message' => 'Post could not be found'
					], REST_Controller::HTTP_NOT_FOUND);
				}                
			}
		}
	}

	public function posts_post(){

		if ($this->isAuthenticated()){

			//Validação de dados
			$title 			= $this->post('title');
			$description 	= $this->post('description');
			$type 			= $this->post('type');
			$status 		= $this->post('status');
			$latitude 		= $this->post('latitude');
			$longitude 		= $this->post('longitude');
			$id_category 	= $this->post('id_category');
			$id_user 		= $this->post('id_user');

			$category = $this->category_model->getById($id_category);
			$user = $this->user_model->getUser($id_user);
			
			if($category) {

				if($user) {

					if($title && $description && $type && $status && $latitude && $longitude) {
						$retorno = $this->post_model->newPost(
							[
								'title' 		=> $title,
								'description' 	=> $description,
								'type' 			=> $type,
								'status' 		=> $status,
								'latitude' 		=> $latitude,
								'longitude' 	=> $longitude,
								'id_category' 	=> $id_category,
								'id_user' 		=> $id_user,
								'create_date'   => date('Y-m-d H:i:s')
							]
						);

						if($retorno) {
							$this->set_response(
								[
									'status'  => REST_Controller::HTTP_CREATED,
									'message' => 'Resource created.'
								], REST_Controller::HTTP_CREATED
							);
						} else {
							$this->set_response(
								[
									'status'  => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
									'errorCode'=> 500,
									'message' => 'Unable to save the resource'
								], REST_Controller::HTTP_INTERNAL_SERVER_ERROR
							);
						}

					} else {
						$this->set_response([
							'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
							'errorCode'=> 422,
							'message' => 'One or more data is missing or failed validation'
						], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
					}

				} else {
					$this->set_response(
						[
							'status' => REST_Controller::HTTP_NOT_FOUND,
							'errorCode' => 404,
							'message' => 'This user doesn\'t exist'
						], REST_Controller::HTTP_NOT_FOUND
					);
				}
			} else {
				$this->set_response(
					[
						'status' => REST_Controller::HTTP_NOT_FOUND,
						'errorCode' => 404,
						'message' => 'This category doesn\'t exist'
					], REST_Controller::HTTP_NOT_FOUND
				);
			}
		}
	}

	public function posts_delete() {
		if ($this->isAuthenticated()){

			$id = (int) $this->get('id');

			if ($id) {
				$post = $this->post_model->getPost($id);

				if($post) {
					$retorno = $this->post_model->deletePost($id);

					if($retorno) {
						$this->set_response(
							[
								'status'  => REST_Controller::HTTP_OK,
								'message' => 'Resource deleted.'
							], REST_Controller::HTTP_OK
						);
					} else {
						$this->set_response(
							[
								'status'  => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
								'errorCode'=> 500,
								'message' => 'Unable to delete the resource'
							], REST_Controller::HTTP_INTERNAL_SERVER_ERROR
						);
					}
				} else {
					$this->set_response(
						[
							'status' => REST_Controller::HTTP_NOT_FOUND,
							'errorCode' => 404,
							'message' => 'This post doesn\'t exist'
						], REST_Controller::HTTP_NOT_FOUND
					);
				}
			} else {
				$this->set_response(
					[
						'status' => REST_Controller::HTTP_NOT_FOUND,
						'errorCode' => 404,
						'message' => 'This posts doesn\'t exist'
					], REST_Controller::HTTP_NOT_FOUND
				);
			}
		}
	}

	public function posts_put() {
		if ($this->isAuthenticated()){

			$id = (int) $this->get('id');

			if ($id === NULL && $id != '') {
				$message = [
					'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
					'errorCode'=> 121212,
					'message' => 'One or more data is missing, please try again or contact the administrator'
				];

				$this->response($message, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);

			} else if ($id <= 0) {
				$this->set_response(
					[
						'status'  => REST_Controller::HTTP_NOT_FOUND,
						'errorCode'=> 121212,
						'message' => 'Post could not be found'
					], REST_Controller::HTTP_NOT_FOUND
				);
			} else {
				$title    		= $this->put('title');
				$description  	= $this->put('description');
				$type    		= $this->put('type');
				$status   		= $this->put('status');
				$latitude   	= $this->put('latitude');
				$longitude   	= $this->put('longitude');
				$id_category  	= $this->put('id_category');
				$id_user   		= $this->put('id_user');
				$data 			= date('Y-m-d H:i:s');

				$putArray = [
					'title'   => $title,
					'description'  => $description,
					'type'    => $type,
					'status'   => $status,
					'latitude'   => $latitude,
					'longitude'  =>  $longitude,
					'id_category'  => $id_category,
					'id_user'   => $id_user,
					'update_date'   =>  $data       
				];

				if ($this->post_model->putPost($id,$putArray)) {
					$this->set_response(
						[
							'status'  => REST_Controller::HTTP_OK,
							'message' => 'Post updated.'
						], REST_Controller::HTTP_OK
					);
				} else {
					$this->set_response(
						[
							'status'  => REST_Controller::HTTP_NOT_FOUND,
							'errorCode'=> 121212,
							'message' => 'Problems with your update, please try again or contact the administrator'
						], REST_Controller::HTTP_NOT_FOUND
					);
				}
			}
		}
	}

	public function pictures_get() {
		$postId = $this->get('id');

		if ($postId != null){
			$pictures = $this->picture_model->getPostPictures($postId);

			if($pictures) {
				$this->set_response(
					$pictures, REST_Controller::HTTP_OK
				);
			} else {
				$this->set_response(
					[
						'status' => REST_Controller::HTTP_NOT_FOUND,
						'errorCode' => 404,
						'message' => 'Post pictures could not be found'
					], REST_Controller::HTTP_NOT_FOUND
				);
			}

		} else {
			$this->set_response(
				[
					'status' => REST_Controller::HTTP_BAD_REQUEST,
					'errorCode' => 400,
					'message' => 'One or more data is missing or failed validation'
				], REST_Controller::HTTP_BAD_REQUEST
			);
		}
	}

	private function isAuthenticated(){
		$header = $this->_head_args;

		if (!isset($header['token'])){
			$this->response([
				'status'  => REST_Controller::HTTP_FORBIDDEN,
				'errorCode'=> 403,
				'message' => 'permission denied'
			], REST_Controller::HTTP_FORBIDDEN);
		}else{
			if (!Authentication::validateToken($header['token'])){
				$this->response([
					'status'  => REST_Controller::HTTP_FORBIDDEN,
					'errorCode'=> 403,
					'message' => 'Token does not match'
				], REST_Controller::HTTP_FORBIDDEN);
			}else{
				return TRUE;
			}
		}


	}

}
