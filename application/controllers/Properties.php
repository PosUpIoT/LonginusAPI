<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Properties extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['properties_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['properties_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['properties_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function properties_get()
    {
        $properties_1 = [                    
            ['id'=>4,'property_name'=>'breed'],
            ['id'=>5,'property_name'=>'color'],
            ['id'=>6,'property_name'=>'type']
        ];

        $properties_2  = [
            ['id'=>7,'property_name'=>'size'],
            ['id'=>8,'property_name'=>'hair'],
            ['id'=>9,'property_name'=>'color']
        ];


        $properties_3  = [
            ['id'=>1,'property_name'=>'plate'],
            ['id'=>2,'property_name'=>'color'],
            ['id'=>3,'property_name'=>'type']
        ];


        $cat_id = $this->get('id');
        $prop_id = $this->get('prop_id');

        if ($prop_id === NULL)
        {
            $properties = [];
            $properties[] = ['category_id'=>$cat_id,'properties'=>${ "properties_" . $cat_id }];
            //$properties[] = ['category_id'=>2,'properties'=>$properties_2];
            //$properties[] = ['category_id'=>3,'properties'=>$properties_3];
            if ($properties)
            {
                $this->response($properties, REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status'  => REST_Controller::HTTP_NOT_FOUND,
                    'errorCode'=> 121212,
                    'message' => 'No properties were found'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
        $prop_id =  (int)$prop_id;
        $cat_id =  (int)$cat_id;
        if ($prop_id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }
        $prop = NULL;
        if (!empty(${ "properties_" . $cat_id }))
        {
            foreach (${ "properties_" . $cat_id } as $key => $value)
            {
                if (isset($value['id']) && $value['id'] === $prop_id)
                {
                    $prop = $value;
                }
            }
        }
        if (!empty($prop))
        {
            $this->set_response($prop, REST_Controller::HTTP_OK);
        }
        else
        {
            $this->set_response([
                'status'  => REST_Controller::HTTP_NOT_FOUND,
                'errorCode'=> 121212,
                'message' => 'Property could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    //form-data
    public function properties_post()
    {
        //Validação de dados
        $title = $this->post('title');
        $cat_id = $this->get('id');
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
            'message' => 'Added a new property to the category '.$cat_id
        ];
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function properties_delete()
    {
        $cat_id = (int) $this->get('id');
        $prop_id = $this->get('prop_id');
        if ($cat_id === NULL && $cat_id != '' && $prop_id === NULL && $prop_id != '')
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); 
        }        
        if($prop_id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);             
        }
        $message = [
            'id' => $prop_id,
            'message' => 'Deleted the property '. $prop_id . " of category ". $cat_id
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }

    //x-www-form-urlencoded 
    public function properties_put()
    {
        $cat_id = (int) $this->get('id');
        $prop_id = $this->get('prop_id');
        if ($cat_id === NULL && $cat_id != '' && $prop_id === NULL && $prop_id != '')
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); 
        }
        if($prop_id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);             
        }

        //Validação de dados
        $property_name = $this->put('property_name');
        if ($property_name == null || $property_name == "")
        {
            $message = [
                'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                'errorCode'=> 121212,
                'property_name' => $property_name,
                'message' => 'One or more data is missing, please try again or contact the administrator'
            ];
            $this->response($message, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
        $message = [
            'id' => $prop_id,
            'message' => 'Updated the property!'
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }

}
