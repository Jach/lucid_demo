<table border="1">
<tr><td>ID</td><td>Status</td></tr>

<?php
foreach ($servers as $server) {
  echo "\n<tr><td>Server #{$server['id']}</td></tr>";
  if ($server['occupied']) {
    echo 'Unavailable';
  } else {
    echo '<a href="/reserve/' . $server['id'] . '">Reserve a session</a>';
  }
  echo '</td></tr>';
}
?>

</table>
