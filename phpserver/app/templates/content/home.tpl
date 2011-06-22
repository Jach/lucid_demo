<?php
if (count($servers) == 0) {
  $this->launch_aws();
?>

<h1>The demo server appears to be resting.</h1>

<h4>Please wait while we relaunch, feel free to refresh the page.</h4>

<script type="text/javascript">
  window.setInterval(window.location.reload, 10000);
</script>

<?php
} else {
?>
<table border="1">
<tr><td>ID</td><td>Status</td></tr>

<?php
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
?>

</table>
<?php
}
?>
