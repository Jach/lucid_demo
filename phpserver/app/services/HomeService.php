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
      , ':/register_server/@authpass/@url/@port' => 'register_server'
      );

    if(!$internal) parent::__construct();
  }

  function disp_home_page($params) {
    redirect('/adminui');
  }

  function disp_adminui($params) {
    global $dbc;
    $data = array();
    if (isset($_SESSION['server_id'])) {
      // try to give them old server if they were the last to use it
    } else {
      // Pick a server:
      $q = 'SELECT id, url, port FROM servers WHERE occupied=0 LIMIT 1';
      $r = mysqli_query($dbc, $q);
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
    }

    $server = $data['url'] . ':' . $data['port'];

    $template_data = array(
        'title' => 'AdminUI'
      , 'master' => 'SQLAdmin.tpl'
      , 'server' => $server
      , 'password' => ''
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
    $q = 'INSERT INTO servers (url, port) VALUES (?, ?)';
    $stmt = mysqli_prepare($dbc, $q);
    mysqli_stmt_bind_param($stmt, 'ss', $url, $port);
    $url = 'http://' . $params['url'];
    $port = $params['port'];
    $r = mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) == 1) {
      return ajax_response('Success');
    } else {
      return ajax_response('Failure', TRUE);
    }
  }

}

?>
