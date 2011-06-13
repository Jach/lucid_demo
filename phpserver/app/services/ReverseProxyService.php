<?php

class ReverseProxyService extends BaseAppService {

  function __construct($internal=0) {

    $this->urls = array(
      ':adminws/<all>' => 'proxy'
      );

    if(!$internal) parent::__construct();
  }

  function proxy($params) {
    $ch = curl_init();

    $server = 'http://ec2-50-17-174-36.compute-1.amazonaws.com:7999';
    curl_setopt($ch, CURLOPT_URL, $server . '/adminws/' . implode('/', $params['all']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, apache_request_headers());
    if (!empty($_POST)) {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
    } else {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
    }

    $response = curl_exec($ch);

    if (curl_errno($ch))
      echo curl_error($ch);
    else {
      curl_close($ch);
      $response = str_replace($server, 'http://demo.dynamobi.com', $response);
      echo $response;
    }
  }

}

?>

