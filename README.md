SphereCube - is a structureless data system.
==========

Each object (card) has a title, a set of metadata and a list of subordinate fields. Each field has a type that defines its visual display.

Features
========

- Card - basic structure
  - Title - title of card
  - Metadata - user information, dates, etc
  - Fields - subordinate fields
    - Name - visual name of field
    - Type - define visual display
    - Value - value for field

Requirements
============

- PHP 8.1 or newer* with extensions:
  - PDO
  - PDO_Postgre
  - Memcached
- HTTP server with PHP support (eg: Apache, Nginx)
- Composer
- PostgreSQL DB

Instalation
===========

- Download or clone the repository
- Install dependence

    ```bash
    composer install
    ```
- Copy `config.php.exam` to `config.php` and configure for your server
- Restore DB-dump from `.dev\dump.sql` into DB

License
=======

This software is distributed under the [MIT license](https://github.com/AlexMcArrow/SphereCube/blob/master/LICENSE).
