# Weeding-Helper

**Weeding Helper** is web-based software to help libraries make 
weeding/de-accession decision based on item usage and external evaluations of 
individual items. Designed for libraries using III (Innovative Interfaces) 
catalogs.

## Demo

See a limited [demo instance of Weeding Helper](http://www6.wittenberg.edu/lib/iii/weed/demo/); the demo does not allowed file uploads, but is otherwise fully functional. 

## Requirements

It runs in a LAMP environment: Linux, Apache, MySQL, PHP; it also uses a good 
bit of JavaScript. It relies on Linux's cron process for some jobs. It relies 
on several pre-existing open-source tools, all of which are included in this 
installation. 

## Installation

Full installation instructions are available in the documentation.php file. 

## Usage

Weeding Helper takes input in the form of tab-delimited text file outputs from
the User Defined Export feature of III's Millennium or Sierra interface's 
Create List module. The export format is defined in documentation.php. Once 
uploaded to Weeding Helper, the system breaks each bibliographic record into 
separate item records so that each volume may be examined separately. A cron 
job will check the holdings against a shared III INN-Reach catalog (if 
desired). Weeding Helper will allow users to make notes and changes to 
selected fields.

Items may be viewed, filtered, sorted and printed. Some data visualization is 
also included. 

## Authors

Weeding Helper was developed by Ken Irwin, kirwin@wittenberg.edu

## License

Weeding Helper is licensed under a 
[Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License](http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en_US)

Weeding Helper makes use of various other open-source components.
These components are used in compliance with their individual licenses.
Those licenses are listed in the documentation.php file.
[View that license information in the demo site](www6.wittenberg.edu/weed/documentation.php#open_source)