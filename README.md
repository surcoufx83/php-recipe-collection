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

##### Database server
If the database is not located on the same computer as the web server, the
`DB_HOST` variable must be adjusted. Replace `localhost` with the host name
of the database server.
```
public const DB_HOST = 'db.example.com';
```

##### Database user and password
Specify a username and password for the database connection by replacing
`db-user-name` and `db-user-secret-password`.
```
public const DB_USER = 'myusr';
public const DB_PASSWORD = 'usrpwd!';
```

##### Database name
Specify the name of the database on the database server by replacing
`db-database-name`.
```
public const DB_DATABASE = 'cookbook';
```

#### OAuth2 configuration
[OAuth2](https://oauth.net/2/) is an open standard with which you can log on to
a website with access data from another website (e.g. social login with Facebook
or Google). For the digital Cookbook OAuth2 is currently implemented for
Nextcloud. Nextcloud is a self-hosted cloud. Other OAuth providers are currently
not supported. The management of OAuth2 clients in the Nextcloud can be found
under Settings / Administration / Security / OAuth 2.0 Clients.
The following forwarding url must be entered in the Nextcloud:
`https://<your domain>/oauth2/callback`.

If you want to offer login with a Nextcloud account, a `conf.oauth2.php` must be
provided. If the file does not exist, then the login via OAuth2 is also disabled.
Proceed with the configuration in the same way as with the database (duplicate
file, rename, edit). The following parameters are required:
- `OATH_CLIENTID`: The client ID is displayed in Nextcloud OAuth2 Administration.
- `OATH_CLIENT_SECRET`: The Secret as displayed in the Nextcloud OAuth2 Administration.
- `OATH_AUTHURL`: Replace `<Nextcloud hostname>` by your Nextcloud Url.
- `OATH_PROVIDER`: Replace `<Nextcloud hostname>` by your Nextcloud Url.
- `OATH_TOKENURL`: Replace `<Nextcloud hostname>` by your Nextcloud Url.
- `OATH_DATAURL`: Replace `<Nextcloud hostname>` by your Nextcloud Url.

#### Email configuration
The digital cookbook also sends e-mails, for example to validate an e-mail
address or when a user wants to be notified of various events.

The following parameters are required for this:
- `System:SMTP:Host`: The SMTP server host name.
- `System:SMTP:Credentials:Name`: The SMTP login user name.
- `System:SMTP:Credentials:Password`: The SMTP login user's password.
- `System:SMTP:Port`: The network port of the SMTP server.
- `System:SMTP:Security`: Encryption settings (see https://github.com/PHPMailer/PHPMailer).
- `System:SMTP:Sender:Name`: The sender name that is shown to recipients.
- `System:SMTP:Sender:Mail`: The sender mail address that is shown to recipients.

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
