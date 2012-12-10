=== xwolf Progress Bars ===
Plugin URI: http://piratenkleider.xwolf.de/plugins/
Description: Displays a textbox with progressbars into a widget or a textbox. 
Content for those bars will get by a simple csv-file on a given URL. 
Version: 1.0
Author: xwolf
Author URI: http://blog.xwolf.de
License: GPL2
Tags: politics, campaign
Requires at least: 3.3
Tested up to: 3.5

== Description ==

Displays one or more progress bars.
All progress bars get data by an external source (URL) and will be automatically 
updated.

Syntax for the data-file on the URL is a character seperated file:
Titel; Progressvalue; Maxvalue

E.g.:

    Projekt 1 Ready State; 20; 100
    Projekt 2 Ready State; 44; 100

You are not based on a maximum number:

    PC-Founding; 45,33; 2300
    Server-Founding; 2213; 34000

You can also use floating numbers in the value field.


== Installation ==

1. ZIP-Datei entpacken
2. Dateien nach `/wp-content/plugins/` hochladen
3. Das Plugin im Admin-Bereich von WordPress aktivieren