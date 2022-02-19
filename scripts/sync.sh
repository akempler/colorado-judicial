#!/bin/bash

# Purpose: Create a database backup from any Pantheon-based environment & import it.
#
# Usage example
#
# Create bash alias: alias terminus-db-sync="sh ~/Sites/tools/terminus-db-sync.sh"
# terminus-db-sync mysite.test

if [ -z "$1" ]
  then
    echo "ERROR: No terminus <site>.<env> provided . Exiting..."
    exit 1
fi

SITE=$1
terminus backup:create $SITE --element=db
# Will download the database dump to a relative location, for import
terminus backup:get $SITE --element=db --to=$SITE-backup.sql.gz
# Import
#gzip -d $SITE-backup.sql.gz
# Assumes this is run within the codebase
#drush sql-drop -y && drush sqlc < $SITE-backup.sql
# Delete archive
lando db-import $SITE-backup.sql.gz
rm $SITE-backup.sql
