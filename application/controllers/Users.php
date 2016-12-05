<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Authentication.php';

class Users extends REST_Controller {

    function __construct()
    {
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

        $this->load->model("user_model");
    }

    public function users_get()
    {

        /*$users = [
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com', 'role' => 1, 'facebook'=>'https://www.facebook.com/thizaom','google'=>'https://plus.google.com/u/0/115634086300123564141','twitter'=>'http://www.twitter.com/thizaom','phone'=>'(41)99913738','create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05' ],
            ['id' => 2, 'name' => 'Jim', 'email' => 'jim@example.com', 'role' => 1, 'facebook'=>'https://www.facebook.com/thizaom','google'=>'https://plus.google.com/u/0/115634086300123564141','twitter'=>'http://www.twitter.com/thizaom','phone'=>'(41)99913738','create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05'  ],
            ['id' => 3, 'name' => 'Jane', 'email' => 'jane@example.com',  'role' => 1, 'facebook'=>'https://www.facebook.com/thizaom','google'=>'https://plus.google.com/u/0/115634086300123564141','twitter'=>'http://www.twitter.com/thizaom','phone'=>'(41)99913738','create_date'=>'2016-09-18 09:58:05','update_date'=>'2016-09-18 09:58:05' ],
        ];*/

        $id = $this->get('id');
        $query = $this->query();

        if ($id === NULL) {
            if (count($query) > 0) {
                if (isset($query['offset']) && isset($query['limit'])){
                    $offset = $query['offset'];
                    $limit = $query['limit'];
                }
                if(isset($query['email'])){
                    $email = $query['email'];
                }
                if(isset($query['password'])){
                    $password = $query['password'];
                }           
            }
            if (!isset($limit)){
                $limit = 50;
            }

            if (!isset($offset)){
                $offset = 0;
            }            

            if (!isset($email) && !isset($password)){
                $result = $this->user_model->getUsers($offset, $limit);
                $countAll = $this->user_model->getCountUsers();                
            }else{
                $result = $this->user_model->getUserEmailPassword($email, $password);

                if ($result){
                    $countAll = 1;
                }
            }


            $users = [
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

            if ($result)
            {
                $this->response($users, REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status'  => REST_Controller::HTTP_NOT_FOUND,
                    'errorCode'=> 404,
                    'message' => 'No users were found'
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
                $user = $this->user_model->getUser($id);

                if (!empty($user))
                {
                    $this->set_response($user, REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->set_response([
                        'status'  => REST_Controller::HTTP_NOT_FOUND,
                        'errorCode'=> 404,
                        'message' => 'User could not be found'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }                
            }
        }
    }

    public function users_post()
    {


        $name = $this->post('name');
        $email = $this->post('email');
        $facebook = $this->post('facebook');
        $password = $this->post('password');
        $phone = $this->post('phone');
        $social_network_id = $this->post('social_network_id');
        $social_network_access_token = $this->post('social_network_access_token');


        if (!isset($social_network_id) && !isset($social_network_access_token)){
            if(isset($name) && isset($email) && isset($password) && isset($phone)) {

                // verificar se o usuário com o email já está cadastrado no sistema
                if (!$this->user_model->getUserEmail($email)){
                    $data = array(
                        'role'=>1,
                        'name'=> $name,
                        'email' => $email,
                        'password' => md5($password), // run this via your password hashing function
                        'phone' => $phone,
                        'api_token' => Authentication::createToken($email, $password, $this->config->item('salt')),
                        'create_date' => date('Y-m-d H:i:s')
                    );

                    if ($this->user_model->insert_user($data)){

                        $user = $this->user_model->getTokenUser($email, $password);

                        $this->set_response(
                            [
                                'status'  => REST_Controller::HTTP_CREATED,
                                'message' => 'User created.',
                                'token' => $user['api_token']
                            ], REST_Controller::HTTP_CREATED
                        );
                    }

                }else{
                    $this->set_response(
                        [
                            'status' => REST_Controller::HTTP_BAD_REQUEST,
                            'errorCode' => 400,
                            'message' => 'Email or user already present in the system'
                        ], REST_Controller::HTTP_BAD_REQUEST
                    );   
                }

            }else{
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





    public function register()
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nome', 'required');
        $this->form_validation->set_rules('password', 'Senha', 'required|min_length[6]');
        $this->form_validation->set_rules('password_conf', 'Confirmação de Senha', 'required|matches[password]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('header/header');
            $this->load->view('login/signup');
            $this->load->view('footer/footer');
        }
        else
        { 
                $data = array(
                    'role'=>1,
                    'name'=> $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'password' => md5($this->input->post('password')), // run this via your password hashing function
                    'phone' => '',
                    'provider'=>'internal',
                    'create_date' => date('Y-m-d H:i:s')
                );
     
                $retorno = $this->user_model->insert_user($data);
                if($retorno)
                {
                    $this->user_model->login($this->input->post('email'),$this->input->post('password'));
                    redirect('/home');
                    //$this->session->set_flashdata('message', 'Usuário registrado com sucesso!');
                    //redirect('/home/login');
                }else{
                    $this->session->set_flashdata('email', $this->input->post('email'));
                    $this->session->set_flashdata('name', $this->input->post('name'));
                    redirect($this->agent->referrer());                 
                }
        }
    }
}
