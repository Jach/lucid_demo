<?php

class HomeService extends BaseAppService {

  function __construct($internal=0) {

    $this->urls = array(
        'get:/index.php' => 'disp_home_page'
      , 'get:/home' => 'disp_home_page'
      , 'get:/index' => 'disp_home_page'
      , 'get:/ping' => 'ping'
      , 'get:/adminui' => 'disp_adminui'
      , 'post:/register_server/@url/@port' => 'register_server'
      );

    if(!$internal) parent::__construct();
  }

  function disp_home_page($params) {
    redirect('/adminui');
  }

  function disp_adminui($params) {
    // Pick a server:

    $server = '';

    $template_data = array(
        'title' => 'AdminUI'
      , 'master' => 'SQLAdmin.tpl'
      , 'server' => $server
    );
    $this->display_page($template_data);
  }

  function ping($params) {

  }

  function register_server($params) {

  }

}

?>
