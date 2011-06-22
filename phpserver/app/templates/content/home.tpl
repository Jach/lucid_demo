<table border="1">
<tr><td>ID</td><td>Status</td></tr>

<?php
if (count($servers) > 1) {
  $i = 1;
  foreach ($servers as $server) {
    echo "\n<tr><td>Server #$i</td><td>";
    if ($server['occupied']) {
      echo 'Unavailable';
    } else {
      echo '<a href="/reserve/' . $server['id'] . '">Reserve a session</a>';
    }
    echo '</td></tr>';
    $i++;
  }
} else {
  $this->insert_template('content/sidebar/tag_cloud.tpl');
}
?>

</table>
