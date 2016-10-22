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
        $this->load->model('property_model');
    }

    public function properties_get()
    {
        $cat_id = (int) $this->get('id');
        $prop_id = (int) $this->get('prop_id');
        if ($cat_id <= 0)
        {
            $this->response([
                'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                'errorCode'=> 422,
                'message' => 'Please specify the category you want the properties'
            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }else{
            if ($prop_id <= 0)
            {
                //Get all category properties
                $properties = [];
                $limit = $this->get('limit');
                $page = $this->get('page');
                $query = $this->get('query');
                $properties[] = ['category_id'=>$cat_id,'properties'=>$this->property_model->getAll($cat_id, $page, $limit, $query)];
                $this->response($properties, REST_Controller::HTTP_OK);
            }else{
                $property = $this->property_model->getById($cat_id, $prop_id);
                if ($property != null)
                {
                    $this->set_response($property, REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->set_response([
                        'status'  => REST_Controller::HTTP_NOT_FOUND,
                        'errorCode'=> 404,
                        'message' => 'Category could not be found'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
    }

    //form-data
    public function properties_post()
    {
        //Validação de dados
        $cat_id = $this->get('id');
        $name = $this->post('property_name');
        $value = $this->post('property_value');
        if ($cat_id <= 0)
        {
            $this->response([
                'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                'errorCode'=> 422,
                'message' => 'Please specify the category you want the properties'
            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }else{
            if ($name == null || $name == "")
            {
                $this->response([
                    'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                    'errorCode'=> 422,
                    'message' => 'One or more data is missing or failed validation'
                ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
            }else{
                if ($this->property_model->insert(array('id_category'=>$cat_id, 'property_name' => $name, 'property_value'=> ($value == null ? '': $value) ))) {
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
    }

    public function properties_delete()
    {

        $cat_id = $this->get('id');
        $prop_id = $this->get('prop_id');
        if ($cat_id <= 0 || $prop_id <= 0)
        {
            $this->response([
                'status'  => REST_Controller::HTTP_BAD_REQUEST,
                'errorCode'=> 400,
                'message' => 'One or more data is missing, please try again or contact the administrator'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }else{
            if ($this->property_model->delete($cat_id, $prop_id)) {                
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

    //x-www-form-urlencoded 
    public function properties_put()
    {
        //PUT com x-www-form-urlencoded
        $id = (int)$this->get('id');
        $prop_id = $this->get('prop_id');
        if ($id <= 0 || $prop_id <= 0)
        {
            $this->response([
                'status'  => REST_Controller::HTTP_BAD_REQUEST,
                'errorCode'=> 422,
                'message' => 'One or more data is missing, please try again or contact the administrator'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $property = $this->property_model->getById($id,$prop_id);

        if (!isset($property) || $property == null) {
            $this->response([
                'status'  => REST_Controller::HTTP_NOT_FOUND,
                'errorCode'=> 404,
                'message' => 'The property requested was not found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }else{
            // Data Validation
            $dataName = $this->put('property_name');
            $dataValue = $this->put('property_value');
            if ($dataName == null || $dataName == "")
            {
                $this->response([
                    'status'  => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                    'errorCode'=> 422,
                    'message' => 'One or more data is missing or failed validation'
                ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
            }else{
                $property['property_name'] = $dataName;
                $property['property_value'] = $dataValue;
                if ($this->property_model->update($property)) {
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
