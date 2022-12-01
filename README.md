# Simple calendar
This is a very simple read only calendar to publish public dates in a ics format.

## Setup

1. Upload [kamiKatze](https://github.com/kkapsner/kamiKatze/) to the web server if not already present.
2. Upload all the files to the web server.
3. Create a `config.ini` according to `config.ini.sample`
4. Fill`data.txt` according to `data.txt.sample` with the events in the calendar

### Remark
The folder "www" has to be accessible from the internet. The folder "calendar" should not be accessible.

## Requirements

### PHP 8
Development and testing was done in PHP 8.1.12

### kamiKatze
The PHP library [kamiKatze](https://github.com/kkapsner/kamiKatze/) is used.

The path to kamiKatze has to be stored in `$_SERVER["KAMIKATZE_LOCATION"]`. The easiest way to do this is define the following in the .htaccess:
```
SetEnv KAMIKATZE_LOCATION "/absolute/path/to/kamiKatze"
```