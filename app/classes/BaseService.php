<?php

/* Core "framework" code.
 * All Serviecs should inherit from this class.
 *
 * For App-Specific functions, use BaseAppService which inherits
 * from this. In general, your Service classes should really
 * inherit from the BaseAppService.
 */

class BaseService {

  function display_page($template_data) {
    // Required data:
    // title -- page title
    // master -- master source template, e.g. main.tpl
    //
    // Sample other data:
    // content -- page content

    if (!isset($template_data['title']))
      $template_data['title'] = APP_TITLE;
    if (!isset($template_data['master']))
      $template_data['master'] = 'main.tpl';
    if (!isset($template_data['content']))
      $template_data['content'] = NULL;

    extract($template_data);

    if ($content && !is_array($content)) {
      $file = APP_URI . "templates/$content";
      if (mb_substr($content, -4) == '.tpl' && file_exists($file))
        $content = $this->get_include_contents($file, $template_data);
    }

    $master_file = APP_URI . "templates/$master";
    if (file_exists($master_file))
      require_once $master_file;

  }

  function get_include_contents($file, $vars=array()) {
    // precondition: caller checks to see if file exists
    extract($vars);
    ob_start();
    include $file;
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }

  function insert_template($template, $vars = array()) {
    extract($vars);
    $file = APP_URI . "templates/$template";
    if (file_exists($file))
      require_once $file;
  }

  function apply_wrapper($wrapper_data) {
    $file = APP_URI . "templates/{$wrapper_data['wrapper_file']}";
    if (file_exists($file))
      require $file;

    return $wrapper_data['content'];
  }

  function not_found($msg='') {
    header("HTTP/1.0 404 Not Found");

    $content = '<div class="maincontent"> ';
    $content .= (empty($msg)) ? '<h3>The page you requested could not be found.</h3>' : $msg;
    $content .= ' </div>';
    $template_data = array('title' => 'Not Found!',
      'content' => $content,
      'master' => 'main.tpl'
    );
    $this->display_page($template_data);
  }

  function map_url_to_func() {
    $uri = (($_SERVER["REQUEST_URI"]!="/") ? $_SERVER["REQUEST_URI"] : "/home");
    $uri = mb_substr($uri, 1); // gets rid of first /
    if ($uri{mb_strlen($uri)-1} == '/')
      $uri = mb_substr($uri, 0, -1); // gets rid of last /
    $uri_parts = explode('/',$uri);

    $params = array();

    $call = '';
    foreach($this->urls as $url => $function) {
      list($types, $url) = explode(':', $url);

      if ($url{0} == '/')
        $url = mb_substr($url, 1); // chop the first / if there
      $url_parts = explode('/', $url);
      if (count($url_parts) != count($uri_parts)) {
        continue; // not this url
      } elseif ($url == implode('/', $uri_parts)) { // exact, found it
        $call = $function;
        if (mb_stripos($types, 'get') !== FALSE)
          $params = array_merge($params, $_GET);
        if (mb_stripos($types, 'post') !== FALSE)
          $params = array_merge($params, $_POST);
        break;
      } else {
        $vars = array();
        foreach($url_parts as $key => $section) {
          if ($section == $uri_parts[$key]) { // good so far
            continue;
          } elseif ($section{0} == '@') { // it's a var?
            $var = mb_substr($section, 1); // chop off @
            $vars = array_merge($vars, array($var => $uri_parts[$key]));
            $call = $function;
          } else { // doesn't match at all
            $call = '';
            break;
          }
        }

        if (!empty($call)) { // found it
          $params = array_merge($params, $vars);
          if (mb_stripos($types, 'get') !== FALSE)
            foreach($_GET as $k => $v)
              if ($v)
                $params[$k] = $v;
          if (mb_stripos($types, 'post') !== FALSE)
            $params = array_merge($params, $_POST);
          break;
        }
      }
    }

    return array('call' => $call, 'params' => $params);
  }

  function __construct($func_data = array()) {
    // This and the mapping function is essentially my micro-framework heart.
    // See HomeService or one of the other services for how it's used.
    // Requirements at the bottom are json and output buffering,
    // but are only really necessary if you want nice JavaScript interfacing.

    if (empty($func_data)) {
      $func_data = $this->map_url_to_func();
      // semi expensive operation, no need to calculate it twice if
      // we already had to for helping the caching system.
    }
    extract($func_data);
    if (method_exists($this, $call)) {
      $result = $this->$call($params);
      // Comment the rest of this if you don't have json or OB
      $contents = ob_get_contents();
      if (empty($contents))
        echo json_encode($result);
    } else {
      $this->not_found();
    }

  }

  function render_response($func_data = array()) {
    // Aliased in case you have instantiated something internally but want to render
    // it in the end. (e.g. some implementation of a cacher.)
    self::__construct($func_data);
  }

}

?>
