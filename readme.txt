=== xwolf Progress Bars ===
Plugin URI: https://github.com/xwolfde/xw-progressbar
Description: Displays a textbox with progressbars into a widget or a textbox. 
Content for those bars will get by a simple csv-file on a given URL. 
Version: 1.0.1
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

=== Usage ===
1. Use as widget
   Simply add the progress bar widget to the sidebar you want to display it.
   Within the widget you can set a title and an URL.

2. Use as shortcode within articles and pages
   Add a shortcode like this:

   [progressbar url="http://some/url/data.csv"]

   Additional attributes:

    * color - set a color out of green, blue, red, orange
    * unitstr - string to add a unit 
    * rounded - make bars and box with rounded borders
    * total - show total numbers
    * numbers - show numbers of each bar
    * html5 - use <progress>-tag instead of <div><span>..
    


=== Syntax for data sources ===
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

1. Download ZIP-file
2. Upload to your blog into directory "/wp-content/plugins/"
3. Activate within the plugin-section of wordpress backend