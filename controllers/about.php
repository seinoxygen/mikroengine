<?php

class About extends Controller {

    function __construct() {
        parent::__construct();
    }

    function index(){
        $this->load->view('about', array('content' => 'The main text.'));
    }
    
}