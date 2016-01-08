<?
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$directory = $_GET['directory'];
$directory = str_replace('"', "", $directory);
$filename  = $_GET['filename'];

if (preg_match("/zready.txt/", $filename)) {
include "/path/to/incron-watchdog/includes/ingest.php";
} 

#$headers = 'From: Incron Watchdog <email@address.edu>' . "\r\n";
#$to = "email@address.edu";
#$subject = "New File, Bark Bark";
#$message = "New File: $directory $filename";
#mail($to, $subject, $message, $headers);
?>
