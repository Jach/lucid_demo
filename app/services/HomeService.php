<?php

class HomeService extends BaseAppService {

  function __construct($internal=0) {

    $this->urls = array(
        'get:/index.php' => 'display_home_page'
      , 'get:/home' => 'display_home_page'
      , 'get:/index' => 'display_home_page'

      );

    if(!$internal) parent::__construct();
  }

  function display_home_page($params) {
    global $dbc;
    $page = isset($params['page']) ? (int) $params['page'] : 1;
    $start = ($page-1) * $this->POST_DISPLAY_LIMIT;
    $end = $this->POST_DISPLAY_LIMIT;
    $posts = $this->get_latest_posts($start, $end);

    $wrapper_data = array(
        'wrapper_file' => 'content/wrappers/home_page.wrapper.tpl'
      , 'page' => $page
      , 'posts' => $posts
      , 'this' => $this
    );
    $content = $this->apply_wrapper($wrapper_data);

    $template_data = array(
      'title' => "The Jach's Website"
      , 'master' => 'main.tpl'
      , 'content' => $content
    );
    $this->display_page($template_data);

  }

  function display_about_page($params) {
    $template_data = array(
        'title' => 'About Jach'
      , 'content' => 'content/about.tpl'
      , 'master' => 'main.tpl'
    );
    $this->display_page($template_data);
  }

}

?>
