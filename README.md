SphereCube - is a structureless data system.
==========

Each object (card) has a title, a set of metadata and a list of subordinate fields. Each field has a type that defines its visual display.

Features
========

- Card - basic structure
  - Title - title of card
  - Metadata - user information, dates
  - Fields - subordinate fields
    - Name - visual name of field
    - Type - define visual display
    - Value - value for field

Requirements
============

- PHP 7.4 or newer
- HTTP server with PHP support (eg: Apache, Nginx)
- Composer
- MySQL-like DB (MySQL v5.x or MariaDB v10.x)
- Manticore for fast full-text search ([Manticore](https://manticoresearch.com/) or [Sphinx](http://sphinxsearch.com/))

Instalation
===========

- Download or clone the repository
- Install dependence

    ```bash
    composer install
    ```
- Copy `config.php.exam` to `config.php`
- Restore DB-dump from `.dev\dump.sql`

License
=======

This software is distributed under the [MIT license](https://github.com/AlexMcArrow/SphereCube/blob/master/LICENSE).
