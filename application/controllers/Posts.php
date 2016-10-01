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
    }

    public function posts_get()
    {
        $posts = [
                ['id' => 1, 'title' => 'teste', 'description' => 'Teste de feed de post', 'type' => '1', 'status' => '1', 'latitude' => '-25.445641', 'longitude' => '-49.360237', 'category' => ['id' => 1, 'name' => 'animals'], 'user' => ['id' => 2, 'name' => 'João', 'email' => 'joao@outlook.com'], 'pictures'=>['https://s3-us-west-2.amazonaws.com/longinus/samplejpg','https://s3-us-west-2.amazonaws.com/longinus/sample1jpg','https://s3-us-west-2.amazonaws.com/longinus/sample2jpg'], 'create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05'],
                ['id' => 2, 'title' => 'teste2', 'description' => 'Teste de feed de post 2', 'type' => '1', 'status' => '1', 'latitude' => '-25.445641', 'longitude' => '-49.360237', 'category' => ['id' => 2, 'name' => 'peoples'], 'user' => ['id' => 1, 'name' => 'Rafael', 'email' => 'rafael@outlook.com'],'pictures'=>['https://s3-us-west-2.amazonaws.com/longinus/samplejpg','https://s3-us-west-2.amazonaws.com/longinus/sample1jpg','https://s3-us-west-2.amazonaws.com/longinus/sample2jpg'], 'create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05'],
                ['id' => 3, 'title' => 'teste3', 'description' => 'Teste de feed de post 3', 'type' => '1', 'status' => '1', 'latitude' => '-25.445641', 'longitude' => '-49.360237', 'category' => ['id' => 3, 'name' => 'vehicles'], 'user' => ['id' => 2, 'name' => 'Blabla', 'email' => 'blabla@outlook.com'],'pictures'=>['https://s3-us-west-2.amazonaws.com/longinus/samplejpg','https://s3-us-west-2.amazonaws.com/longinus/sample1jpg','https://s3-us-west-2.amazonaws.com/longinus/sample2jpg'], 'create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05']
                ];       

        $id = $this->get('id');

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

        if ($id === NULL)
        {
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
        }

        $id = (int) $id;
        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }
        $post = NULL;
        if (!empty($posts))
        {
            foreach ($posts as $key => $value)
            {
                if (isset($value['id']) && $value['id'] === $id)
                {
                    $post = $value;
                }
            }
        }
        if (!empty($post))
        {
            $this->set_response($post, REST_Controller::HTTP_OK);
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
