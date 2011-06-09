<?php

/*
* Configuration file does the following things:
* - Has site settings in one location.
* - Stores URLs and URIs as constants.
* - Sets include paths for inc files and class files.
* - Loads global functions.
* - Sets how errors will be handled.
* - Establishes a connection to the database.
*     NOTE: You must create dbinfo.php for DB config values. See below.
* - Determines how sessions should be handled, and starts the session.
*     NOTE: Sessions implementation uses database, to create table do:
       CREATE TABLE sessions (
         id CHAR(32) NOT NULL,
         data TEXT,
         last_accessed TIMESTAMP NOT NULL,
         PRIMARY KEY (id)
       );
*
* THIS FILE SHOULD BE PLACED SOMEWHERE SAFE.
*/

#***************************#
#******SETTINGS*************#

// You must edit the following settings on site-by-site basis.

// Errors are emailed here.
$contact_email = 'ksecretan@dynamobi.com';

// Determine whether we're working on a local server or on the real server:
if (mb_stristr($_SERVER['HTTP_HOST'], 'local') ||
 (mb_substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168')) {
  $local = TRUE;
} else {
  $local = FALSE;
}

// Determine location of files and the URL of the site:
// Allow for development on different servers.
if ($local) {

  $host = 'localhost';
  $user = 'u1';
  $pass = 'pw';
  $database = 'db';

  // Always debug when running locally:
  $debug = TRUE;

  // Define the constants:
  define('BASE_URI', '/var/www/projectdir/html/');
  define('BASE_URL', 'http://localhost/');
  define('APP_URI', '/var/www/projectdir/app/');

} else {

  define('BASE_URI', '/home/dynamobi/lucid_demo/phpserver/html/');
  define('BASE_URL', 'http://demo.dynamobi.com/');
  define('APP_URI', '/home/dynamobi/lucid_demo/phpserver/app/');

  // STORE DB connectivity info variables as above, here:
  require_once APP_URI . 'config/dbinfo.php';
  // Also store the variable $authpass for simple service protection.

}

define('APP_TITLE', "LucidDB Live Demo");

// Set the location to class files and other inc files for easy includes.
$autoload_dirs = array(
  APP_URI . 'classes/',
  APP_URI . 'services/'
);
$inc_dirs = implode(PATH_SEPARATOR, $autoload_dirs);

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $inc_dirs);
function __autoload($class_name) {
  global $autoload_dirs;
  foreach ($autoload_dirs as $dir) {
    $path = $dir . $class_name;
    if (mb_strtolower(mb_substr($path, -4) != '.php')) {
      $path .= '.php';
    }
    if (file_exists($path)) {
      require_once $path;
    }
  }
}

/* Very important setting...
* The $debug variable is used to set error management.
*
* To debug the entire site, do:
$debug = TRUE;
* before this next conditional.
*/

$debug = TRUE;
if (!isset($debug)) { // Assume debugging is off.
  $debug = FALSE;
}

date_default_timezone_set("US/Pacific");

#****END SETTINGS*****#
#*********************#

#*********************#
#***ERROR MANAGEMENT**#

// A design note: your code should be throwing and catching exceptions
// as they are much nicer than old fashioned errors.

// Create the error handler.
function my_error_handler ($e_number, $e_message, $e_file, $e_line, $e_vars) {

  global $debug, $contact_email;

  // Build the error message.
  $message = "An error occured in script '$e_file' on line $e_line: \n<br />$e_message\n<br />";

  // Add the date and time.
  $message .= "Date/Time: " . date('n-j-Y H:i:s') . "\n<br />";

  // Append $e_vars to the $message.
  $message .= "<pre>" . print_r ($e_vars, 1) . "</pre>\n<br />";

  if ($debug) { // Show the error.
    echo '<p class="error">' . $message . '</p>';
  } else {
    // Log the error:
    error_log ($message, 1, $contact_email); // Send email.

    // Only print an error message if the error isn't a notice or strict.
    if ( ($e_number != E_NOTICE) && ($e_number < 2048)) {
      echo '<p class="error">A serious system error occured. We apologize for the inconvenience.</p>';
    }
  } // End of $debug IF
} // End of my_error_hander() function.

// Use my error handler:
set_error_handler ('my_error_handler');

#****END ERROR MANAGEMENT****#
#****************************#

#****************************#
#*****DATABASE STUFF*********#

// Normally you'd put this in DB under an initDB() function that
// returns the $dbc var, or a link to the abstraction layer.

// Connect to the database:
$dbc = @mysqli_connect($host, $user, $pass, $database) OR
trigger_error("Could not connect to the database!\n<br />MySQL Error: " . mysqli_connect_error());
if (!$dbc) { // try again?
  sleep(10);
  $dbc = @mysqli_connect($host, $user, $pass, $database) OR
    trigger_error("Could not connect to the database again after 10 secs!\n<br />MySQL Error: " . mysqli_connect_error());
}
mysqli_set_charset($dbc, 'UTF-8');

// Create a function for escaping the data.
// You must do this on all input in order to avoid SQL Injection
// (Unless you use prepared statements.)
// I gotta give Larry Ullman credit for this. It's very nice.

function escape_data($data, $b64_ed=0) {

  // Need the connection:
  global $dbc;

  // Address Magic Quotes.
  if (ini_get('magic_quotes_gpc') && !$b64_ed) {
    $data = stripslashes($data);
  }
  // Trim and escape:
  return mysqli_real_escape_string($dbc, trim($data));

} // End of escape_data() function.

#****END DATABASE STUFF*****#
#***************************#

#***************************#
#****SESSION STUFF**********#

// Define the open_session() function:
// This function takes no arguments.
// This function will return true, as the database connection is already established.
function open_session() {
  return true;
}

// Define the close_session() function:
// This function takes no arguments.
// This function will return true, because the footer.inc.php closes the session for me.
function close_session() {
  return true;
}

// Define the read_session() function:
// This function takes one argument: the session ID.
// This function retrieves the session data.
function read_session($sid) {
  global $dbc; // Fetch the database connection
  // Query the database.
  $q = sprintf('SELECT data FROM sessions WHERE id="%s"', escape_data($sid));
  $r = mysqli_query($dbc, $q);

  // Retrieve the results
  if (mysqli_num_rows($r) == 1) {
    list ($data) = mysqli_fetch_array($r, MYSQLI_NUM);
    return $data;
  } else { // Return an empty string.
    return '';
  }
} // End of read_sessions()

// Define the write_session() function:
// This function takes two arguments:
// the session ID and the session data.
function write_session($sid, $data) {
  global $dbc;
  
  // Store in the database
  $q = sprintf('REPLACE INTO sessions (id,data) VALUES ("%s", "%s")', escape_data($sid),
       escape_data($data));
  $r = mysqli_query($dbc, $q);
  return mysqli_affected_rows($dbc);
}

// Define the destroy_session() function:
// This function takes one argument: the session ID.
function destroy_session($sid) {
  global $dbc;
  // Delete from the database.
  $q = sprintf('DELETE FROM sessions WHERE id="%s"', escape_data($sid));
  $r = mysqli_query($dbc, $q);

  // Clear the $_SESSION array:
  $_SESSION = array();

  return mysqli_affected_rows($dbc);
}

// Define the clean_sessions() function.
// This function takes one argument: a value in seconds
function clean_session($expire) {
  global $dbc;

  // Delete the old sessions:
  $q = sprintf('DELETE FROM sessions WHERE DATE_ADD(last_accessed, INTERVAL %d SECOND) < NOW()',
       (int) $expire);
  $r = mysqli_query($dbc, $q);
  return mysqli_affected_rows($dbc);
}

// Declare the functions to use:
session_set_save_handler('open_session', 'close_session', 'read_session', 'write_session',
                         'destroy_session', 'clean_session');

// Make whatever other changes to the session settings here.

// Start the session:
session_start();

#******END OF SESSION STUFF*****#
#*******************************#

?>
