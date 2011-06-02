<?php

/* USAGE:
 * $send = array();
 * $send['to'] = 'to@d.com';
 * $send['from'] = 'from@d.com'; // defaults to 'admin@thisdomain.com'
 * $send['subject'] = 'Sub';
 * $send['html'] = '<a href="http://www.google.com">click me</a>'; // newlines == <br /> automagically
 *
 * $mail = new EasyMail($send);
 * $mail->send();
 *
 * You can also send multiple messages.
 *
 * $mail->send($other_sendarray);
 *
 */

class EasyMail {

  function __construct($params) {

    $domain = str_replace('www.', '', $_SERVER['SERVER_NAME']);

    $this->to = isset($params['to']) ? $params['to'] : '';
    $this->from = isset($params['from']) ? $params['from'] : 'admin@' . $domain;
    $this->subject = isset($params['subject']) ? $params['subject'] : 'Message From ' . $domain;
    $this->html = isset($params['html']) ? str_replace("\n", '<br />', $params['html']) : '';

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: {$this->from}\r\n";
    $this->headers = $headers;

  }

  function send($params=array()) {

    if (!empty($params)) {
      $this->__construct($params);
    }

    return mail($this->to, $this->subject, $this->html, $this->headers);
  }

}
?>
