# simple-blog
A simple blog built in PHP

[![Build Status](https://travis-ci.com/gabriel-detassigny/simple-blog.svg?branch=master)](https://travis-ci.org/gabriel-detassigny/simple-blog) [![Coverage Status](https://coveralls.io/repos/github/gabriel-detassigny/simple-blog/badge.svg)](https://coveralls.io/github/gabriel-detassigny/simple-blog)

This is a basic blog engine built in PHP, without any frameworks but instead multiple composer packages linked together.

## Installation

This project supports [Docker](https://www.docker.com) for local development. However, it can be installed without it.

Follow these steps if you plan to install this project either locally or for production.

Go to the relevant documentation depending on which install you would prefer.

## Local installation (using Docker)

Make a copy of `.env.example` into a `.env` file, and change the env parameters if you need (the defaults should be enough to start).

Run `docker-compose up -d` from the root to start the basic LAMP stack.

Once done, run the following to install the packages, run the DB migrations and seeds:
- `docker-compose exec webserver composer install`
- `docker-compose exec webserver vendor/bin/phinx migrate`
- `docker-compose exec webserver vendor/bin/phinx seed:run`

You should now be able to access your blog at [http://localhost:8000](http://localhost:8000).

Head over to the _Admin_ section below in this doc to see how to connect to the admin and write blog posts.

## Dev or prod install without Docker

### Introduction

Make sure you have at least **PHP 7.3** installed, a MySQL server, and [composer](https://getcomposer.org/).

In the root of the project, type `composer install` to install all the dependencies.

Then, make a copy of `.env.example` into a `.env` file, and change the env parameters as you need (including the DB ones).

### HTTP Server

On dev environment, you can start a simple PHP server by running the following from the root of the project:
```
php -S localhost:8000 -t frontend/public/
```

You can of course use any other HTTP server of your choice. 
Just note that the root folder is _frontend/public_, with the _index.php_ file as an entry point.

### SQL

SQL migrations are done using [Phinx](https://phinx.org).

To generate the tables structures in your database, simply type:
```
vendor/bin/phinx migrate
```

### Fake data

If you wish to pre-seed your local dev environment with fake data, this project contains some [Phinx](https://phinx.org) seeders.

You can run them by typing:
```
vendor/bin/phinx seed:run
```

This will insert some random data into your DB tables.

### Force HTTPS on prod

Because the admin interface is using Basic HTTP authentication, it is _highly_ recommended to enforce HTTPS on production.
To do so, head over the frontend/public/.htaccess and uncomment the following lines:
```
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Admin

Once you've completed the installation, head over to the _/admin_ URL in your browser to access the admin interface.
(That would be _http://localhost:8000/admin_ on dev)

It will ask you for the admin credentials set in `.env`.

On the admin index, you'll need to update the _Blog Configuration_.
There, you can set the name of your blog, a short description of it for the home page, 
and some text about you and your blog that will display on the about page.

You'll also need at least one author created through admin.

Once this is all done, you should be ready to write blog posts!

## Assets Storage

By default, uploaded images will be stored in the filesystem.
If however you wish to load balance multiple instances, you can also set the storage to use an AWS S3 bucket.

To do so, set the STORAGE_TYPE env variable to "s3", and set the other AWS env variables with the correct values.
