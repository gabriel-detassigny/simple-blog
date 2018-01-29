# simple-blog
A simple blog built in PHP


[![Build Status](https://travis-ci.org/gabriel-detassigny/simple-blog.svg?branch=master)](https://travis-ci.org/gabriel-detassigny/simple-blog) [![Coverage Status](https://coveralls.io/repos/github/gabriel-detassigny/simple-blog/badge.svg)](https://coveralls.io/github/gabriel-detassigny/simple-blog)

This is a basic blog built in PHP, without any frameworks but instead multiple composer package linked together.

It's still a work in progress.


## Development

Follow these steps if you plan to install this project for local development purposes.

### Introduction

Make sure you have at least **PHP 7.1** installed, a MySQL server, and [composer](https://getcomposer.org/).

In the root of the project, type `composer install` to install all the dependencies.

Then, make a copy of `.env.example` into a `.env` file, and change the env parameters as you need (including the DB ones).

### HTTP Server

You can start a simple PHP server by running the following from the root of the project:
```
php -S localhost:8000 -t frontend/public/
```

(or run a local Apache / Nginx / _insert your favourite HTTP server_)

### SQL

To generate the SQL queries you need for tables creation, simply type :
```
vendor/bin/doctrine orm:schema-tool:update --dump-sql
```

And either copy / paste the SQL into your MySQL client, or add the _--force_ option to directly run it.
