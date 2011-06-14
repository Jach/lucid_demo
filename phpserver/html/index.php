<?php
/*
    Copyright (C) 2009-2011  Kevin "Jach" Secretan

    This project is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This project is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this project (see the "COPYING" file).  If not, see
    <http://www.gnu.org/licenses/>.
 */

mb_internal_encoding('UTF-8');

//error_reporting(E_ALL);
// header stuff
ob_start();

require_once '../app/config/config.inc.php';
require_once '../app/config/global_functions.php';

$uri = (($_SERVER["REQUEST_URI"]!="/") ? $_SERVER["REQUEST_URI"] : "/home");
$uri = mb_substr($uri, 1); // gets rid of first /
if ($uri{mb_strlen($uri)-1} == '/')
  $uri = mb_substr($uri, 0, -1); // gets rid of last /
$uri_parts = explode('/',$uri);// printa($uri_parts);
$service = $uri_parts[0];

switch($service) {

  // Public pages

  // Home (also public)
  case 'index':
  case 'home':
  default: $serviceClass = 'HomeService'; break;

}

$handler = new $serviceClass();

// footer stuff

session_write_close();
if (isset($dbc)) {
  mysqli_close($dbc);
  unset($dbc);
}

ob_end_flush();

?>
