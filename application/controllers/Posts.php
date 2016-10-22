<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Posts extends REST_Controller {

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
	

		/**
		Query for:
		query
		searchType
		category
		sex
		skin
		age
		height:
		age:
		height:
		age:
		height:
		properties:
		rad
		lat
		lng
		**/

		$id = $this->get('id');
		$query = $this->query();

		$posts = NULL;

		if ($id === NULL) {
			if (count($query) > 0) {
				$lon = $query['lon'];
				$lat = $query['lat'];

				$offset = $query['offset'];
				$limit = $query['limit'];
			}

			if (!isset($limit) && !isset($offset)){
				$limit = 50;
				$offset = 0;
			}

			if (!isset($lat) && !isset($lon)){
				$result = $this->post_model->getAllPosts($offset, $limit);
				$countAll = $this->post_model->getCountAllPosts();
			} else {
				$result = $this->post_model->getPostsSearch($lat, $lon, 10000, $offset, $limit);
				$qtde = $this->post_model->getCountCoordPosts($lat, $lon, 10000);

				$countAll = $qtde['qtde'];
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
					'errorCode'=> 121212,
					'message' => 'No posts were found'
				], REST_Controller::HTTP_NOT_FOUND);
			}

		}else{
			$id = (int) $id;
			if ($id <= 0)
			{
				$this->set_response([
					'status'  => REST_Controller::HTTP_NOT_FOUND,
					'errorCode'=> 121212,
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
						'errorCode'=> 121212,
						'message' => 'Post could not be found'
					], REST_Controller::HTTP_NOT_FOUND);
				}                
			}
		}
	}

	public function posts_post() {

		//Validação de dados
		$title 			= $this->post('title');
		$description 	= $this->post('description');
		$type 			= $this->post('type');
		$status 		= $this->post('status');
		$latitude 		= $this->post('latitude');
		$longitude 		= $this->post('longitude');
		$id_category 	= $this->post('id_category');
		$id_user 		= $this->post('id_user');

		$category = $this->category_model->getCategory($id_category);
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
							'id_user' 		=> $id_user
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

	public function posts_delete() {
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

	public function posts_put() {

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

}
