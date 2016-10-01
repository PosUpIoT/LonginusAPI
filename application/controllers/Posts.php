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

        $this->load->model("post_model");
    }

    public function posts_get()
    { 

        

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

        if ($id === NULL)
        {
            

            if (count($query) > 0){
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
            }else{
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

    public function posts_post()
    {

        //Validação de dados
        $title = $this->post('title');
        if ($title === NULL)
        {
            $message = [
                'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                'errorCode'=> 121212,
                'message' => 'One or more data is missing, please try again or contact the administrator'
            ];
            $this->response($message, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }

        $message = [
            'id' => 100,
            'title'=> $title,
            'message' => 'Added a post'
        ];
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function posts_delete()
    {
        $id = (int) $this->get('id');
        if ($id === NULL && $id != '')
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); 
        }        
        if($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);             
        }
        $message = [
            'id' => $id,
            'message' => 'Deleted the post'
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }

    public function posts_put()
    {
        $id = (int) $this->get('id');
        if ($id === NULL && $id != '')
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); 
        }
        if($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);             
        }

        //Validação de dados
        $title = $this->put('title');
        if ($title == null || $title == "")
        {
            $message = [
                'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                'errorCode'=> 121212,
                'title' => $title,
                'message' => 'One or more data is missing, please try again or contact the administrator'
            ];
            $this->response($message, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
        $message = [
            'id' => $id,
            'message' => 'Updated the post'
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }

}
