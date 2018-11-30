# simple-blog
A simple blog built in PHP


[![Build Status](https://travis-ci.org/gabriel-detassigny/simple-blog.svg?branch=master)](https://travis-ci.org/gabriel-detassigny/simple-blog) [![Coverage Status](https://coveralls.io/repos/github/gabriel-detassigny/simple-blog/badge.svg)](https://coveralls.io/github/gabriel-detassigny/simple-blog)

This is a basic blog built in PHP, without any frameworks but instead multiple composer package linked together.

## Installation

Follow these steps if you plan to install this project either locally or for production.

### Introduction

Make sure you have at least **PHP 7.2** installed, a MySQL server, and [composer](https://getcomposer.org/).

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

To generate the SQL queries you need for tables creation, simply type :
```
vendor/bin/doctrine orm:schema-tool:update --dump-sql
```

And either copy / paste the SQL into your MySQL client, or add the _--force_ option to directly run it.

### Admin

Once the project is installed, head over to the _/admin_ URL in your browser to access the admin interface.
(That would be _http://localhost:8000/admin_ on dev)

It will ask you for the admin credentials you set in `.env`.

On the admin index, you'll need to update the _Blog Configuration_.
There, you can set the name of your blog, a short description of it for the home page, 
and some text about you and your blog that will display on the about page.

You'll also need at least one author created through admin.

Once this is all done, you should be ready to write blog posts!
