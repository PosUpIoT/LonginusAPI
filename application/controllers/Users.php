<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Users extends REST_Controller {

    function __construct()
    {
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function users_get()
    {
        $users = [
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com', 'role' => 1, 'facebook'=>'https://www.facebook.com/thizaom','google'=>'https://plus.google.com/u/0/115634086300123564141','twitter'=>'http://www.twitter.com/thizaom','phone'=>'(41)99913738','create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05' ],
            ['id' => 2, 'name' => 'Jim', 'email' => 'jim@example.com', 'role' => 1, 'facebook'=>'https://www.facebook.com/thizaom','google'=>'https://plus.google.com/u/0/115634086300123564141','twitter'=>'http://www.twitter.com/thizaom','phone'=>'(41)99913738','create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05'  ],
            ['id' => 3, 'name' => 'Jane', 'email' => 'jane@example.com',  'role' => 1, 'facebook'=>'https://www.facebook.com/thizaom','google'=>'https://plus.google.com/u/0/115634086300123564141','twitter'=>'http://www.twitter.com/thizaom','phone'=>'(41)99913738','create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05' ],
        ];

        $id = $this->get('id');
        if ($id === NULL)
        {
            if ($users)
            {
                $this->response($users, REST_Controller::HTTP_OK);
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No users were found'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
        $id = (int) $id;
        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }
        $user = NULL;
        if (!empty($users))
        {
            foreach ($users as $key => $value)
            {
                if (isset($value['id']) && $value['id'] === $id)
                {
                    $user = $value;
                }
            }
        }
        if (!empty($user))
        {
            $this->set_response($user, REST_Controller::HTTP_OK);
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'User could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function users_post()
    {
        $message = [
            'id' => 100,
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'message' => 'Added a user!'
        ];

        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function users_delete()
    {
        $id = (int) $this->get('id');
        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT);
    }

}
