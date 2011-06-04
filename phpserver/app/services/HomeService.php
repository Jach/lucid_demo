<?php

class HomeService extends BaseAppService {

  function __construct($internal=0) {

    $this->urls = array(
        ':/index.php' => 'disp_home_page'
      , ':/home' => 'disp_home_page'
      , ':/index' => 'disp_home_page'
      , ':/noservers' => 'disp_no_servers'
      , 'get:/ping' => 'ping'
      , 'get:/adminui' => 'disp_adminui'
      , 'post:/register_server/@authpass/@url/@port' => 'register_server'
      );

    if(!$internal) parent::__construct();
  }

  function disp_home_page($params) {
    redirect('/adminui');
  }

  function disp_adminui($params) {
    // Pick a server:
    global $dbc;
    $q = 'SELECT id, url, port FROM servers WHERE NOT occupied LIMIT 1';
    $r = mysqli_query($dbc, $q);
    $data = array();
    if (mysqli_num_rows($r) == 1)
      $data = mysqli_fetch_assoc($r);
    else
      redirect('/noservers');
    $q = 'UPDATE servers SET occupied=TRUE, acquire_time=NOW(), session_id=\'' .
      escape_data(session_id()) . '\' WHERE id=' . $data['id'];
    $r = mysqli_query($dbc, $q);
    if (mysqli_affected_rows($dbc) == 1)
      $_SESSION['server_id'] = (int)$data['id'];
    else
      redirect('/noservers');

    $server = $data['url'] + ':' + $data['port'];

    $template_data = array(
        'title' => 'AdminUI'
      , 'master' => 'SQLAdmin.tpl'
      , 'server' => $server
    );
    $this->display_page($template_data);
  }

  function disp_no_servers($params) {
    $template_data = array(
        'title' => 'No available servers!'
      , 'master' => 'main.tpl'
      , 'content' => 'content/no_servers.tpl'
    );
    $this->display_page($template_data);
  }

  function ping($params) {
    global $dbc;
    if (!isset($_SESSION['server_id']))
      redirect('/');
    $q = 'UPDATE servers SET last_used=NOW() WHERE id=' .
      (int)$_SESSION['server_id'] . ' AND session_id=\'' .
      escape_data(session_id()) . '\'';
    $r = mysqli_query($dbc, $q);
    if (mysqli_affected_rows($dbc) == 1) {
      return ajax_response('Success');
    } else {
      $_SESSION['server_id'] = -1;
      return ajax_response('Failure', TRUE);
    }
  }

  function register_server($params) {
    global $authpass; // expected from dbinfo.php
    global $dbc;
    if (!isset($params['authpass'], $params['url'], $params['port']) ||
        $params['authpass'] != $authpass)
      redirect('/');
    $url = escape_data($params['url']);
    $port = escape_data($params['port']);
    $q = "INSERT INTO servers (url, port) VALUES ('$url', '$port')";
    $r = mysqli_query($dbc, $q);
    if (mysqli_affected_rows($dbc) == 1) {
      return ajax_response('Success');
    } else {
      return ajax_response('Failure', TRUE);
    }
  }

}

?>
