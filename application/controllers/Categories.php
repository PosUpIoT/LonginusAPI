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

        $this->load->model('category_model');
    }

    public function categories_get()
    {
            $id = $this->get('id');
            if ($id === NULL)
            {
                $limit = $this->get('limit');
                $page = $this->get('page');
                $categories = $this->category_model->getAll($page, $limit);
                $this->response($categories, REST_Controller::HTTP_OK);
            }else{
                $id = (int) $id;
                if ($id <= 0)
                {
                    $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
                }
                $category = $this->category_model->getById($id);
                if ($category != null)
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
    }

    public function categories_post()
    {
        $dataReceived = $this->post('name');

        if ($dataReceived == null || $dataReceived == "")
        {
            $this->response([
                'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                'errorCode'=> 422,
                'message' => 'One or more data is missing or failed validation'
            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }else{
            if ($this->category_model->insert(array('name' => $dataReceived))) {
                    $this->set_response(['message' => 'Resource successfully created.'], REST_Controller::HTTP_CREATED);
            }else{
                $this->set_response([
                'status'  => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
                'errorCode'=> 500,
                'message' => 'Unable to save the resource, please try again later or contact the administrator'
                ], REST_Controller::HTTP_OK);
            }
        }
    }

    public function categories_delete()
    {
        $id = (int)$this->get('id');
        if ($id <= 0 )
        {
            $this->response([
                'status'  => REST_Controller::HTTP_BAD_REQUEST,
                'errorCode'=> 400,
                'message' => 'One or more data is missing, please try again or contact the administrator'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }else{
            if ($this->category_model->delete($id)) {                
                $this->set_response(['message' => 'Resource successfully deleted.'], REST_Controller::HTTP_OK);
            }else{
                $this->set_response([
                'status'  => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
                'errorCode'=> 500,
                'message' => 'Unable to save the resource, please try again later or contact the administrator'
                ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }


    }

    public function categories_put()
    {
        //PUT com x-www-form-urlencoded
        $id = (int)$this->get('id');
        if ($id <= 0 )
        {
            $this->response([
                'status'  => REST_Controller::HTTP_BAD_REQUEST,
                'errorCode'=> 422,
                'message' => 'One or more data is missing, please try again or contact the administrator'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $category = $this->category_model->getById($id);

        if (!isset($category) || $category == null) {
            $this->response([
                'status'  => REST_Controller::HTTP_NOT_FOUND,
                'errorCode'=> 404,
                'message' => 'The category requested was not found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }else{
            // Data Validation
            $dataReceived = $this->put('name');
            if ($dataReceived == null || $dataReceived == "")
            {
                $this->response([
                    'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                    'errorCode'=> 422,
                    'message' => 'One or more data is missing or failed validation'
                ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
            }else{
                $category['name'] = $dataReceived;
                if ($this->category_model->update($category)) {
                    $this->set_response([
                        'status'  => REST_Controller::HTTP_OK,
                        'errorCode'=> 200,
                        'message' => 'Resource successfully updated.'
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->set_response([
                    'status'  => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
                    'errorCode'=> 500,
                    'message' => 'Unable to save the resource, please try again later or contact the administrator'
                    ], REST_Controller::HTTP_OK);
                }
            }
        }
    }
}
