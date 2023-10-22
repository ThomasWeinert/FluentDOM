@echo off
php -d error_reporting=22527 "%~dp0phpdocumentor.phar" %*
