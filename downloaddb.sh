#!/bin/sh

ACTION="$1"
FILENAME="$2"

# imports the gcloud sql instance into the local db
copytolocal()
{
	# exports the production sql database
	gcloud config set project romac-website
	gcloud sql instances export wp-stage gs://romac-website.appspot.com/dumps/"$FILENAME"
	gsutil cp gs://romac-website.appspot.com/dumps/"$FILENAME" .

	# removes all script pertaining to the mysql table
	sed -i.bak '1,/FLUSH PRIVILEGES/d' "$FILENAME"

	# executes script on local sql instance
	mysql -u root < "$FILENAME"
}

# exports the local db to the gcloud sql instance
copytoserver() 
{
	mysqldump -u root wordpress_db > snapshot.sql

	# insert the use schema command
	sed -i.bak '17 a\
	USE wordpress_db;\                   
	\
	' snapshot.sql

	# upload to gcs and import to db
	gsutil cp snapshot.sql gs://romac-website.appspot.com/dumps/snapshot.sql

	gcloud config set project romac-website
	gcloud sql instances import wp-stage gs://romac-website.appspot.com/dumps/snapshot.sql
}

invalidcmd()
{
	echo "Invalid argument $ACTION"
	echo "  import - copies the production database to local"
	echo "  export - copies to local database to production"
}

case "$ACTION" in
	( "import" ) copytolocal ;;
	( "export" ) copytoserver ;;
	( * ) invalidcmd ;;
esac
