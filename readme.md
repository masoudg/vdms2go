# Voucher Distribution Microservice 

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

## About VDMS
A PHP micro-framework called “Lumen” is used for implementation of this Voucher Distribution Microservice (VDMS).

## How to run?

To run vdms, follow these steps:
1. Set the environment variables (specifically db related variables) in .env file at the root directory of the project. 
2. Run composer install (to install the project dependencies).
3. Run php artisan migrate (to create the tables on db).
4. Run php artisan db:seed (to insert some fake records into tables).

At the end, run php -S localhost:8000 -t public command to start the development server.
