# islandora_ingest_dragndrop

## Introduction

Designed for ease of use by Archivists, who are in authoritative control; a methodology for creating a drag and drop batch ingest workflow powered by a local Linux-based NAS system integrated with an Islandora ingest server. 



### NAS Requirements

* Linux-based NAS system or equivalent
* SSH server
* rsync
* ssh-keygen
* cron


### Islandora Server Requirements

* Linux-based system or equivalent
* SSH server
* rsync
* PHP CLI
* nohup
* [incron](https://packages.debian.org/search?keywords=incron)
* [islandora_batch](https://github.com/Islandora/islandora_batch)



## NAS Configuration

Goal: rsync'ing from NAS to Islandora Server

UNC Charlotte Libraries utilizes a [Seagate NAS Pro](http://www.seagate.com/products/network-attached-storage/business-storage/seagate-nas-pro/), which meets all technical requirements out of the box.   Seagate NAS Pro provides an SSH service, which is best accessed using an administrative account.  Once SSH'ed in, admin can `sudo su` and escalate to root.

System does not have to be a commercial NAS, any Linux-based server with file sharing abilities will suffice.  Any Linux-based server can meet the technical requirements.

* Create a "Ready" share, which will be used by archivist's for objects that are "ready for ingest"
* Create subdirectory structure on "Ready" share, which reflects repository collection structure and related object policies
	* Example: (repository contains a "sandbox" collection supporting basic images, large images, and PDFs)
```
/share/Ready/sandbox/jpeg_png_gif
/share/Ready/sandbox/tiff_jp2
/share/Ready/sandbox/pdf
```

* Create "archivist" account on NAS
* Setup [SSH Public Key Authentication](https://www.google.com/search?q=SSH+Public+Key+Authentication)
	* `su archivist` account and generate keys using ssh-keygen
		* Tips:
			* Create hidden directory outside of mountable share path(s), for storing the "archivist" keys
			* `chmod 600` private key (id_dsa)
	* Create complementary account on Islandora server-side and implement SSH Public Key Authentication
* `Crontab -e`
	* Example: (upload from NAS to Islandora server at the top of every hour of the business day, 9:00 AM - 6:00 PM, Monday-Friday)
	```
    0	9,10,11,12,13,14,15,16,17,18	*	*	1,2,3,4,5	/usr/bin/rsync -e 'ssh -i /path/to/.archivist_ssh/id_dsa' --exclude=".*" --exclude ".*/" --filter='P .ingest.log' -zavr --inplace --delete /shares/Ready/ account@islandora.server.edu:/path/to/inbound/nas/directory/loadingdock/seagate-nas
    ```
	
## Islandora Server Configuration

Goal: Watch inbound directories for "ready for ingest" objects.  If "ready for ingest" are seen, ingest using islandora_batch after upload from NAS completes.

* Install server-side "incron-watchdog" scripts
* Edit "ingest.php" script, defining inbound directories and ingest parameters, including namespace, content model, and parent collection where objects should be ingested
* Edit "ingest.sh" script, defining drush paths and islandora_batch parameters
* `incrontab -e`
```
/path/to/inbound/nas/directory/loadingdock/seagate-nas/sandbox/jpeg_png_gif IN_CLOSE_WRITE /usr/bin/php /path/to/incron-watchdog/watchdog.php directory="$@" filename="$#"
/path/to/inbound/nas/directory/loadingdock/seagate-nas/sandbox/tiff_jp2 IN_CLOSE_WRITE /usr/bin/php /path/to/incron-watchdog/watchdog.php directory="$@" filename="$#"
/path/to/inbound/nas/directory/loadingdock/seagate-nas/sandbox/pdf IN_CLOSE_WRITE /usr/bin/php /path/to/incron-watchdog/watchdog.php directory="$@" filename="$#"
```



## General Operation

* Archivist's drag and drop readied objects and matching metadata into the NAS' "Ready" share folder structure   
* Per islandora_batch, "ready for ingest" is defined as "object1.tif" and "object1.xml" being present (object + metadata)
* When Archivist is truly ready for ingest, in the specific readied subdirectory, Archivist creates a plain text file named "zready.txt"
* When crontab on NAS executes, files will be transferred to Islandora server, which is programmed to ingest files when "zready.txt" is seen.


## Notes
* File is named "zready.txt", intentionally beginning with the letter "z", so when rsync'ed "zready.txt" should transfer last due to alphabetical order.
* Archivist's can populate NAS subdirectories and coordinate ingests, and place "zready.txt" into subdirectory when truly ready
* To add more objects to a given collection, NAS subdirectory should be cleared and loaded with fresh objects.  A new "zready.txt" file should be created OR the existing "zready.txt" file can be edited, a slight modification made, and saving.  NAS-based rsync crontab is configured not to overwrite "zready.txt" file already present on server-side unless it is different.
* ingest.sh is programmed to disable islandora_bagit_create_on_modify, because a fresh bagit bag will be generated after each derivative datastream is generated and added to an ingested object.   We re-enable islandora_bagit_create_on_modify during nightly server maintenance, which runs after ingests are observed to be fully complete.
* ingest.sh runs ingests using `nohup command &` for true process backgrounding; ingest will run for as long as required or until server resources are absolutely depleted (right-sizing and tuning server infrastructure will not be covered here :-)
* ingest.sh creates ingest.log files in each subdirectory to be ingested, for your review


## Customization

* Every bit of this methodology is ripe for customization
* Example: A way to create the NAS subdirectory structure using automation is highly desired, such automation could be powered by a repository structure query

## Troubleshooting/Issues

* Two readied NAS collections can collide and nothing will ingest properly.   Archivist's should coordinate collection ingestion operations offline to avoid two readied collections colliding.   The collision issue is primarily caused by the author's programming, which can be improved upon...  For example, the programming could be changed to execute only "islandora_batch_scan_preprocess" operations when objects arrive on the server, and wait until all "islandora_batch_scan_preprocess" are finished before running "islandora_batch_ingest".


## Maintainers

Current maintainers:

* [Brad Spry](https://github.com/bradspry)


## License

[GPLv3](http://www.gnu.org/licenses/gpl-3.0.txt)
