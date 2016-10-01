<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Pictures extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['pictures_delete']['limit'] = 50; // 50 requests per hour per user/key
        $this->methods['pictures_put']['limit'] = 50; // 50 requests per hour per user/key
        $this->methods['pictures_post']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function pictures_get()
    {
            $message = [
                'status'  => REST_Controller::HTTP_NOT_FOUND,
                'errorCode'=> 121212,
                'message' => 'Method not implemented in the API'
            ];
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
    }

    public function pictures_post()
    {
        $file = $this->post('file');
        $post_id = $this->post('post_id');
        if ($file === NULL || $post_id === NULL)
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
            'file'=> $file,
            'message' => 'Added a picture to the post!'
        ];
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function pictures_delete()
    {
        $id = $this->get('id');

        if ($id === NULL && $id != '')
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); 
        }
        if ((int) $id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND); 
        }
        $message = [
            'id' => $id,
            'message' => 'Deleted the picture'
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }

    public function pictures_put()
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
        $file = $this->put('file');
        if ($file == null || $file == "")
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
            'message' => 'Updated the picture'
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }
}
