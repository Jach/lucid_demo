<?php

function printa($var, $return=0) {
  $out = '<pre>' . htmlentities(print_r($var, 1), ENT_COMPAT, 'UTF-8') . "</pre>\n<br />\n";
  if ($return)
    return $out;
  else
    echo $out;
}

function secho($output) {
  // Safe Echo
  echo htmlentities($output, ENT_COMPAT, 'UTF-8');
}

function redirect($loc) {
  $url = BASE_URL . mb_substr($loc, 1); // chop off extra /

  session_write_close();
  if (isset($dbc)) {
    mysqli_close($dbc);
    unset($dbc);
  }

  header("Location: $url");
  exit();
}

function ajax_response($msg, $error=0, $kwargs=array()) {
  if (is_array($msg)) {
    $msg = json_encode($msg);
  }
  $response = array('msg' => $msg, 'error' => $error);
  if(!empty($kwargs))
    $response = array_merge($response, $kwargs);
  return $response;
}

function table_last_modified($table) {
  // Warning: this appears to be a fairly expensive call.
  global $dbc;
  global $database;
  $q = "SELECT UNIX_TIMESTAMP(UPDATE_TIME) t FROM INFORMATION_SCHEMA.Tables
    WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = '$table'";
  $r = mysqli_query($dbc, $q);
  if (mysqli_num_rows($r) == 1) {
    $row = mysqli_fetch_assoc($r);
    return $row['t'];
  } else {
    return FALSE;
  }
}

function table_last_row_time($table, $where_clause = '', $date_field = 'post_date', $sort_field = 'id') {
  global $dbc;
  $q = "SELECT UNIX_TIMESTAMP($date_field) t FROM $table $where_clause ORDER BY $sort_field DESC LIMIT 1";
  $r = mysqli_query($dbc, $q);
  if (mysqli_num_rows($r) == 1) {
    $row = mysqli_fetch_assoc($r);
    return $row['t'];
  } else {
    return FALSE;
  }
}

// For higher-order Scheme-like stuff:
function first($lst) {
  return empty($lst) ? NULL : $lst[0];
}
function bf($lst) {
  if (!empty($lst)) array_shift($lst);
  return $lst;
}
function cons($first, $rest) {
  array_unshift($rest, $first);
  return $rest;
}
function map($fn, $lst) {
  return empty($lst) ?
    array() :
    cons($fn(first($lst)),
      map($fn, bf($lst)));
}

?>
