#vhfp3.com
Ruben J. Leon

Dec 1, 2015

### Migrations
### Database Sync - From Production to Development
##### RSYNC

### Map Structures
### Servers
### Apache Confs
### Setup Scripts

### Deployment
### Production Scripts
##### optimize tables
##### Delete By API User
##### Server Logs
##### Application Logs
##### Cron Jobs

## Working with Database Tables
*  .local.ini override - connect
* Adding a Table to the framework
* Adding Columns to existing tables

#### Map Uploader from dev
#### Map Uploader Custom








#General Installation Notes
CentOS 6.5, PHP 5.3, MySQL 5.0, Apache 2

###Apache 2
*Enable mod rewrite by uncommenting out the following line in the __httpd.conf__:*
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```


AllowOverride should be set to "All" in the __httpd.conf__ or the __.htaccess__ file
```apache
<Directory />
  Options FollowSymLinks
  AllowOverride All
</Directory>
```


###PHP
**Required Modules:**
* php.x86_64
* php-cli.x86_64
* php-common.x86_64
* php-mbstring.x86_64
* php-mysql.x86_64
* php-pdo.x86_64
* php-xml.x86_64


*Show currently installed PHP modules*
```
yum list installed | grep -i php
```

*Install all required or missing modules*
```
yum -y install php php-cli php-common php-mbstring php-mbstring php-mysql php-pdo php-xml
```

#####php.ini Directives
```
date.timezone = ‘America/Los_Angeles’
short_open_tag = On
```


###MySQL
*connect as a privileged user*
```sql
mysql -u root -p -h 127.0.0.1
-- OR
mysql -u root -p -h localhost
  
  -- show all schemas
  show databases;
  
  --Create Database Schema
  CREATE DATABASE vhfp3
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_unicode_ci;
    
  -- create user and grant access to the new schema
  grant all on schema.* to user@hostname identified by 'somePassword';
  grant all on vhfp3.* to vhfp3@localhost identified by 'passwordInClearText';
  
  --use the new schema
  use vhfp3;
  
  -- read in sql script from current directory (specify path if otherwise)
  source vhfp3.sql;
  
  -- display all tables
  show tables;
  select count(*) from Maps;
  
  -- quit mysql
  exit;
  
  -- if problems arise, delete the db schema and start over
  drop database vhfp3;
```


###CSR Installation
1. Create the media, Temporary and Sessions folder
2. Clone the project from github or download the zip file
3. Add soft links to the media, Temporary and sessions folders
4. Configure the ".local.ini" file with the database credentials
5. Set Owner, Group & permissions on files & directories


######1. Create Directories
*The CSR requires 3 directories to be installed outside of the Document Root.  It's best to create these directories 1 level above the Document Root of the CSR*
* media
* Temporary/_cache
* Temporary/_log
* Temporary/_temp
* sessions

*Create the above Directories anywhere outside of Document Root*
```sh
mkdir /var/www/vhosts/vhfp3.com/media
mkdir -p /var/www/vhosts/vhfp3.com/Temporary/_cache
mkdir -p /var/www/vhosts/vhfp3.com/Temporary/_log
mkdir -p /var/www/vhosts/vhfp3.com/Temporary/_temp
mkdir /var/www/vhosts/vhfp3.com/sessions
```

*Recursively assign owner=__apache__, assign group=__apache__ to these dirs*
```sh
chown -R apache:apache media/ Temporary/ sessions/
```

*set permissions and add the sticky bit*
```sh
chmod 775 media/ Temporary/ sessions/
chmod g+s media/ Temporary/ sessions/
```


######2. Clone the project
*Clone this project from github into a directory called __dev.vhfp3.com__*
```
git clone git@github.com:yalfonso/vhfp3.com.git vhfp3.com
```

*OR Clone this project from github into the current directory*
```
git clone git@github.com:yalfonso/vhfp3.com.git .
```


######3. Add Soft Links
*Add soft links to the media, Temporary and sessions folder inside the __development__ folder*
```sh
cd dev.vhfp3.com/development
ln -s /var/www/vhosts/vhfp3.com/Temporary
ln -s /var/www/vhosts/vhfp3.com/media
ln -s /var/www/vhosts/vhfp3.com/sessions
```


######4. Configure the database file
*Configure the CSR to talk to the database*
*Copy lines 49 thru 52 from config.ini to create a new hidden file called __.local.ini__*
*Edit the .local.ini file to include the appropriate database credentials*
```sh
sed -n -e "49,52p" config.ini > .local.ini
#Edit the .local.ini file.  Final contents will be something like:
[db0]
dsn    = "mysql:dbname=vhfp3;host=localhost or IPaddress"
dbuser = "vhfp3"
dbpass = "SomePassword"
```


######5. Assign owner, group and set permissions
*Recursively assign owner=__vhfp3__, assign group=__apache__ to CSR*
```sh
chown -R vhfp3:apache dev.vhfp3.com
```

*set permissions and add the sticky bit*
```sh
chmod 775 dev.vhfp3.com
chmod g+s dev.vhfp3.com

# _engine folder requires 777 permissions
chmod 777 dev.vhfp3.com/development/Application/_engine
```

