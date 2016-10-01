<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Categories extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['categories_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['categories_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['categories_delete']['limit'] = 50; // 50 requests per hour per user/key
        $this->methods['categories_put']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function categories_get()
    {
        $categories = [
                ['id' => 1, 'name' => 'animals', 'create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05'],
                ['id' => 2, 'name' => 'peoples', 'create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05'],
                ['id' => '3', 'name' => 'vehicles', 'create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05']
                ];       

        $id = $this->get('id');
        if ($id === NULL)
        {
            if ($categories)
            {
                $this->response($categories, REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status'  => REST_Controller::HTTP_NOT_FOUND,
                    'errorCode'=> 121212,
                    'message' => 'No category was found'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
        $id = (int) $id;
        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }
        $category = NULL;

        if (!empty($categories))
        {
            foreach ($categories as $key => $value)
            {
                if (isset($value['id']) && $value['id'] === $id)
                {
                    $category = $value;
                }
            }
        }

        if (!empty($category))
        {
            $this->set_response($category, REST_Controller::HTTP_OK);
        }
        else
        {
            $this->set_response([
                'status'  => REST_Controller::HTTP_NOT_FOUND,
                'errorCode'=> 121212,
                'message' => 'Category could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function categories_post()
    {
        $title = $this->post('name');
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
            'message' => 'Added a category'
        ];
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function categories_delete()
    {
        $id = (int) $this->get('id');
        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); 
        }
        $message = [
            'id' => $id,
            'message' => 'Deleted the category'
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }

    public function categories_put()
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
        $title = $this->put('name');
        if ($title == null || $title == "")
        {
            $message = [
                'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                'errorCode'=> 121212,
                'message' => 'One or more data is missing, please try again or contact the administrator'
            ];
            $this->response($message, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }

        $message = [
            'id' => $id,
            'message' => 'Updated the category'
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }
}
