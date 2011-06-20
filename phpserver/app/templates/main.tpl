<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title><?php echo $title; ?></title>
  <link rel="shortcut icon" href="/imgs/favicon.ico" />
  <link rel="stylesheet" href="/css/mainstyle.css" />
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <script type="text/javascript" src="/js/mainjs.js"></script>
</head>
<body>

<?php
if(isset($content)) {
  if(is_array($content)) {
    foreach ($content as $c) {
      echo "$c\n\n";
    }
  } else {
    echo $content;
  }
}
//$this->insert_template('content/d.tpl');
?>


</body>
</html>
