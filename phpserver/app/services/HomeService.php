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
      , ':/reserve/@id' => 'reserve_server'
      , ':/register_server/@authpass/@url/@port/@sapass' => 'register_server'
      , 'get,post:/invalidate_sessions' => 'invalidate_sessions'
      );

    if(!$internal) parent::__construct();
  }

  function disp_home_page($params) {
    $list = $this->get_servers_list();
    $template_data = array(
        'title' => 'LucidDB AdminUI Demo'
      , 'master' => 'main.tpl'
      , 'content' => 'content/home.tpl'
      , 'servers' => $list
      , 'this' => $this
    );
    $this->display_page($template_data);
  }

  function get_servers_list() {
    global $dbc;
    $q = 'SELECT id, occupied FROM servers';
    $r = mysqli_query($dbc, $q);
    $servers = array();
    while ($row = mysqli_fetch_assoc($r)) {
      $servers[] = $row;
    }
    return $servers;
  }

  function reserve_server($params) {
    global $dbc;
    $id = (int)$params['id'];
    $q = 'UPDATE servers SET occupied=1, acquire_time=NOW(), session_id=\'' .
      escape_data(session_id()) . '\' WHERE occupied=0 and id=' . $id;
    $r = mysqli_query($dbc, $q);
    if (mysqli_affected_rows($dbc) == 1) {
      $_SESSION['server_id'] = $id;
      redirect('/adminui');
    } else {
      redirect('/');
    }
  }

  function disp_adminui($params) {
    if (!isset($_SESSION, $_SESSION['server_id'])) {
      redirect('/');
    }
    global $dbc;
    $q = 'SELECT url, port, sapass FROM servers WHERE id=' .
      (int)$_SESSION['server_id'];
    $r = mysqli_query($dbc, $q);
    $data = array();
    if (mysqli_num_rows($r) == 1)
      $data = mysqli_fetch_assoc($r);
    else
      redirect('/');

    $server = $data['url'] . ':' . $data['port'] . '/adminws/ns0.wsdl';

    $template_data = array(
        'title' => 'AdminUI'
      , 'master' => 'SQLAdmin.tpl'
      , 'server' => $server
      , 'password' => $data['sapass']
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
    if (!isset($params['authpass'], $params['url'], $params['port'], $params['sapass']) ||
        $params['authpass'] != $authpass)
      redirect('/');
    $q = 'INSERT INTO servers (url, port, sapass) VALUES (?, ?, ?)';
    $stmt = mysqli_prepare($dbc, $q);

    mysqli_stmt_bind_param($stmt, 'sss', $url, $port, $sapass);
    $url = 'http://' . $params['url'];
    $port = $params['port'];
    $sapass = $params['sapass'];

    $r = mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) == 1) {
      return ajax_response('Success');
    } else {
      return ajax_response('Failure', TRUE);
    }
  }

  function invalidate_sessions($params) {
    global $dbc;
    // Force any sessions off that have been around for 2 hours
    // or haven't pinged for 4 mins (gives them a couple chances to
    // ping late)
    $q = 'SELECT id, url, port FROM servers WHERE occupied=1 AND ' .
      '(DATE_ADD(acquire_time, INTERVAL 2 HOUR) < NOW() OR ' .
      'DATE_ADD(last_used, INTERVAL 4 MINUTE) < NOW())';
    $r = mysqli_query($dbc, $q);
    $rows = array();
    $ids = array();
    while ($row = mysqli_fetch_assoc($r)) {
      $rows[] = $row;
      $ids[] = (int)$row['id'];
    }
    $q = 'UPDATE servers SET occupied=0 WHERE id IN (' . implode(',', $ids) . ')';
    $r = mysqli_query($dbc, $q);
    // TODO: tell server to reset its data.


    // TODO:
    // Shut down the instance if no servers have been accessed in a while.

    // TODO:
    // Ping servers and remove from the list if they're not up.

    return ajax_response('Okay');
  }

}

?>
