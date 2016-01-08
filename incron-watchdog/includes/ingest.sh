#!/bin/bash
system_ready=$(ps aux 2>/dev/null |grep islandora_batch_ingest 2>/dev/null |wc -l)
if (( $system_ready == '0' || $system_ready == '1'))
	then
	sleep 60
	/usr/local/bin/drush -c /usr/local/drush/drushrc.php vset islandora_bagit_create_on_modify '0'
	/usr/local/bin/drush -c /usr/local/drush/drushrc.php -v --user=drupaladministratorroleuser --uri=http://islandora.server.edu islandora_batch_scan_preprocess --namespace=$1 --content_models=$2 --parent=$3 --parent_relationship_pred=isMemberOfCollection --type=directory --target=$4
	sleep 10
	if [ ! -f $4/.ingest.log ]; then
		/usr/bin/touch $4/.ingest.log
	fi
	nohup /usr/local/bin/drush -c /usr/local/drush/drushrc.php -v --user=drupaladministratorroleuser --uri=http://islandora.server.edu islandora_batch_ingest >> $4/.ingest.log 2>&1 &
else
echo "Error: Batch Ingest Already Running"

fi
