<?
if (preg_match("/sandbox\/jpeg_png_gif/", $directory)) {
$namespace = "test";
$content_model = "islandora:sp_basic_image";
$parent = "test:1";
rename("$directory/zready.txt", "$directory/.zready.txt");
$command = "/usr/local/bin/drush -c /usr/local/drush/drushrc.php -v --user=drupaladministratorroleuser --uri=https://islandora.server.edu islandora_batch_scan_preprocess --namespace=$namespace --content_models=$content_model --parent=$parent --parent_relationship_pred=isMemberOfCollection --type=directory --target=$directory";
exec ("/path/to/incron-watchdog/includes/ingest.sh $namespace $content_model $parent $directory");
rename("$directory/.zready.txt", "$directory/zready.txt");
} 
else if (preg_match("/sandbox\/tiff_jp2/", $directory)) {
$namespace = "test";
$content_model = "islandora:sp_large_image_cmodel";
$parent = "test:1";
rename("$directory/zready.txt", "$directory/.zready.txt");
$command = "/usr/local/bin/drush -c /usr/local/drush/drushrc.php -v --user=drupaladministratorroleuser --uri=https://islandora.server.edu islandora_batch_scan_preprocess --namespace=$namespace --content_models=$content_model --parent=$parent --parent_relationship_pred=isMemberOfCollection --type=directory --target=$directory";
exec ("/path/to/incron-watchdog/includes/ingest.sh $namespace $content_model $parent $directory");
rename("$directory/.zready.txt", "$directory/zready.txt");
} 
else if (preg_match("/sandbox\/pdf/", $directory)) {
$namespace = "test";
$content_model = "islandora:sp_pdf";
$parent = "test:1";
rename("$directory/zready.txt", "$directory/.zready.txt");
$command = "/usr/local/bin/drush -c /usr/local/drush/drushrc.php -v --user=drupaladministratorroleuser --uri=https://islandora.server.edu islandora_batch_scan_preprocess --namespace=$namespace --content_models=$content_model --parent=$parent --parent_relationship_pred=isMemberOfCollection --type=directory --target=$directory";
exec ("/path/to/incron-watchdog/includes/ingest.sh $namespace $content_model $parent $directory");
rename("$directory/.zready.txt", "$directory/zready.txt");
} 
$headers = 'From: Incron Watchdog <email@address.edu>' . "\r\n";
$to = 'archivist-group@address.edu';
$subject = "Archivist Alert: Seagate NAS Directory Synced, Ready, and Ingesting Now";
$message = "Now Ingesting Directory: $directory\n\nCommand: $command\n";
mail($to, $subject, $message, $headers);
?>
