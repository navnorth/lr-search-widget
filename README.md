LR Publisher Search Widget
==========================

This code is an interface into the LR Search Index.  It provides API access to ease searching an LR ElasticSearch instance as well as passthroughs for direct ElasticSearch queries, and rendering  of a customizable remote widget

## Requirements

* PHP 5.3
* mcrypt PHP Module
* Composer `curl -s https://getcomposer.org/installer | php`
* [Redis 2.8](http://redis.io)

## Get Started
1. Point your PHP configuration at /public
2. Run composer to install required packages: `composer install`
3. Run `./setup.sh`
4. Follow installation instructions

## Pretty URL Configuration
This project includes a `.htaccess` in `public/` that should work immediately when using Apache.  If the provided `.htaccess` file does not work or you are not using Apache, please refer to the laravel 4 documentation on pretty URL configuration: http://laravel.com/docs/installation#pretty-urls

## API Documentation
Further documentation and examples for available API endpoints is available at /help

## Copyright and license
Code and documentation copyright 2014 Navigation North. Code released dual-licensed, available under either [the MIT license](LICENSE-MIT) or  [the Apache 2 license](LICENSE-APACHE).
