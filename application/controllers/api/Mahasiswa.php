<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Mahasiswa extends CI_Controller
{
  use REST_Controller {
  REST_Controller::__construct as private __resTraitConstruct;
  }

  function __construct()
  {
    // Construct the parent class
    parent::__construct();
    $this->__resTraitConstruct();

    // Configure limits on our controller methods
    // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
    $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
    $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
    $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

    // load model mahasiswa
    $this->load->model('Mahasiswa_model', 'model');
  }

  public function index_get()
  {
    $id = $this->get('id');
    if ($id === NULL) {
      // get all data mahasiswa
      $mahasiswa = $this->model->getMahasiswa();

      if (!empty($mahasiswa)) {
        $this->response([
          'data' => $mahasiswa,
          'status' => true,
          'total' => count($mahasiswa)
        ], 200); // OK (200) being the HTTP response code
      }
    } else {
      // get specific data mahasiswa based on id
      $mahasiswa = $this->model->getMahasiswaAt($id);

      if (!empty($mahasiswa)) {
        $this->response([
          'status' => true,
          'data' => $mahasiswa,
        ], 200); // OK (200) being the HTTP response code
      } else {
        $this->response([
          'status' => false,
          'message' => 'Id not found',
        ], 404);
      }
    }
  }

  public function index_post()
  {
    $data = [
      'nrp' => $this->post('nrp'),
      'nama' => $this->post('nama'),
      'email' => $this->post('email'),
      'jurusan' => $this->post('jurusan')
    ];

    if ($this->model->createMahasiswa($data)) {
      $this->response([
        'status' => true,
        'message' => 'New mahasiswa has been created!',
      ], 201);
    } else {
      $this->response([
        'status' => false,
        'message' => 'Failed to create new mahasiswa',
      ], 400);
    }
  }

  public function index_put()
  {
    $id = $this->put('id');
    $data = [
      'nrp' => $this->put('nrp'),
      'nama' => $this->put('nama'),
      'email' => $this->put('email'),
      'jurusan' => $this->put('jurusan')
    ];

    if ($this->model->updateMahasiswa($data, $id)) {
      $this->response([
        'status' => true,
        'message' => 'Mahasiswa has been updated!',
      ], 200);
    } else {
      $this->response([
        'status' => false,
        'message' => 'Failed to edit mahasiswa',
      ], 400);
    }
  }

  public function index_delete()
  {
    // tampung id yang dikirimkan lewat method delete
    $id = $this->delete('id');

    if ($id === NULL) {
      $this->response([
        'status' => false,
        'message' => 'provide an id',
        'id' => $id
      ], 400);
    } else {
      // if delete success
      if ($this->model->deleteMahasiswa($id)) {
        $this->response([
          'status' => true,
          'message' => 'deleted'
        ], 200);
      } else {
        // if id not found
        $this->response([
          'status' => false,
          'message' => 'Id not found'
        ], 400);
      }
    }
  }
}
