[![Build Status](https://travis-ci.org/surcoufx83/php-recipe-collection.svg?branch=master)](https://travis-ci.org/surcoufx83/php-recipe-collection)
[![CodeFactor](https://www.codefactor.io/repository/github/surcoufx83/php-recipe-collection/badge)](https://www.codefactor.io/repository/github/surcoufx83/php-recipe-collection)

# php-recipe-collection
This project aims to develop a self-managed digital cookbook.
It is based on PHP with a MySQL database and HTML with JavaScript for the frontend.

## Getting started
To use this digital cookbook, the entire repository must be downloaded and made
available on the web server. After adjusting the database configuration, the
database will be initialised with the first call of the start page. A user can
be created via the command line to manage the application.

### Get source
The source code is downloaded to the root directory of the web server via `git clone`:
```
git clone https://github.com/surcoufx83/php-recipe-collection.git ./
```
This command loads all source code for the digital cookbook from the GitHub server
and writes it to the current directory. It uses the current major release, not
the latest development release.

### Install dependencies
Use [Composer](https://getcomposer.org/) to install necessary dependencies:
```
composer install
```

### Edit configuration file
The configuration files are located in the `/config` folder and have the
file extension `.template`. To set up the system configuration:
1. duplicate the `.template`-files.
1. name the duplicated files like the original but without the `.template` extension.
1. edit the files thus duplicated and enter the required values.

#### cbconfig.yml
Open the cbconfig.yml and check all settings. Many of them are only used
to configure the website, but some must also contain data like database
access data.

##### Database credentials
Make sure that you enter valid connection and access data to the database server. The current version of this software only supports MySQL (or Maria DB) databases.
Specify the information in the `cbconfig.yml` in lines 63 to 67:
1. line 63: Replace `db.example.com` with a hostname or IP address where the database server can be reached.
2. line 65: Replace `db-user-name` with the user name for the database connection.
3. line 66: Replace `db-user-secret-password!` with the password associated with the user name.
4. line 67: Replace `db-database-name` with the database name.

#### OAuth2 configuration
[OAuth2](https://oauth.net/2/) is an open standard with which you can log on to
a website with access data from another website (e.g. social login with Facebook
or Google). For the digital Cookbook OAuth2 is currently implemented for
Nextcloud. Nextcloud is a self-hosted cloud. Other OAuth providers are currently
not supported. The management of OAuth2 clients in the Nextcloud can be found
under Settings / Administration / Security / OAuth 2.0 Clients.
The following forwarding url must be entered in the Nextcloud instance:
`https://<your domain>/oauth2/callback`.

In `cbconfig.yml` the following settings must be set for an OAuth2 connection:
1. lines 70, 73, 74, 75, 76: Replace `cloud.example.com` with the hostname of your nextcloud instance.
2. line 71: Replace `really-secret-client-id` with the client id which is displayed in your Nextcloud instance.
3. line 72: Replace `my-client-secret` with the client secret which is displayed in your Nextcloud instance.

#### Email configuration
The digital cookbook also sends e-mails, for example to validate an e-mail
address or when a user wants to be notified of various events.

These settings are also made in `cbconfig.yml`:
1. line 78: Replace `smtp.example.com` with the hostname or IP address of the SMTP server.
2. lines 80 and 81: Replace `smtp-user-name` and `smtp-user-secret-password!` with valid access data for sending mail.
3. line 82: Correct the port if necessary
4. lines 85 and 86: Enter sender information that will be displayed to the recipient.

### Create missing folders
Check in the directory `public/pictures` if the two directories `avatars` and `cbimages` exist. If not, please create them manually.

### Webserver
Configure your web server to use the subdirectory public as `documentroot` and forward all requests to the index.php (except static files like css, js, map, jpg, etc.)

#### Apache example
In the httpd.conf file from the Apache webserver setup the following `DocumentRoot` and `Directory` (replace `c:/dev/xampp/htdocs` with a valid path to your directory):
```
DocumentRoot "C:/dev/xampp/htdocs/public"
<Directory "C:/dev/xampp/htdocs/public">
  Options Indexes FollowSymLinks Includes ExecCGI
  AllowOverride All
  Require all granted
  RewriteEngine on
  RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} -d
  RewriteCond %{REQUEST_URI} !/$
  RewriteRule ^(.+)$ $1/
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.+)$ index.php?q=$1 [L,QSA]
</Directory>
```

#### nginx example
Create a site for nginx for example with the following rules:
```
server {
  listen 443 http2;
  listen [::]:443 ssl http2;
  server_name cookbook.example.com;

  index index.php;
  root /var/www/public;
  client_max_body_size 64M;

  server_tokens off;
  #  add_header Content-Security-Policy "default-src 'self' cookbook.example.com; style-src *";

  ssl on;
  ssl_certificate /etc/letsencrypt/live/cookbook.example.com/fullchain.pem; # managed by Certbot
  ssl_certificate_key /etc/letsencrypt/live/cookbook.example.com/privkey.pem; # managed by Certbot
  ssl_protocols TLSv1.2 TLSv1.3;
  ssl_prefer_server_ciphers off;
  ssl_dhparam /etc/nginx/dhparam.pem;
  ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA$  ssl_ecdh_curve secp384r1; # Requires nginx >= 1.1.0
  ssl_session_timeout  10m;
  ssl_session_tickets off; # Requires nginx >= 1.5.9
  ssl_stapling on; # Requires nginx >= 1.3.7
  ssl_stapling_verify on; # Requires nginx => 1.3.7

  ###############################################################
  # exclude /favicon.ico from logs
  location = /favicon.ico {
    log_not_found off;
    access_log off;
  }

  ##############################################################
  # Disable logging for robots.txt
  location = /robots.txt {
    deny all;
    log_not_found off;
    access_log off;
  }

  ##############################################################
  # Deny all attempts to access hidden files such as
  # .htaccess, .htpasswd, .DS_Store (Mac).
  location ~ /\. {
    deny all;
    access_log off;
    log_not_found off;
  }

  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  ##############################################################
  #
  location ~ [^/]\.php(/|$) {
    fastcgi_split_path_info ^(.+?\.php)(/.*)$;
    include /etc/nginx/fastcgi_params;
    fastcgi_param HTTP_PROXY "";
    fastcgi_param   SCRIPT_FILENAME  $document_root/index.php;
    fastcgi_pass unix:/run/php/php7.2-fpm-mellhen.sock;
    fastcgi_read_timeout 300;
    fastcgi_buffers 16 16k;
    fastcgi_buffer_size 32k;
  }

  ###############################################################
  # serve static files directly
  location ~* ^.+.(css|eot|gif|ico|jpeg|jpg|js|map|otf|png|svg|ttf|woff|woff2|wav|webmanifest)$ {
    access_log off;
    expires    30d;
  }

}
```

### Create your first user
If you are using a Nextcloud integration via OAuth2, just load the cookbook's
homepage and select `Sign up with Nextcloud account`.

Otherwise you have to create a first user from the console to log in. To do
this, execute the following command in the root directory of the web server
(from where the installation was started):
```
php cli.php user:create
```
Follow the console instructions and provide username, first and last name and
an email address. If an activation link is to be sent, the user chooses his own
password during activation. Otherwise you must also specify a password. The user
is then created in the database. If everything has succeeded, this is confirmed
in the console.

To make this user an administrator, execute the following command:
```
php cli.php user:grantadmin <username>
```
