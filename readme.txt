=== xwolf Progress Bar ===
Plugin Name: xwolf Progress Bar
Plugin URI: https://github.com/xwolfde/xw-progressbar
Description: Displays a textbox with progress bars into a widget or a textbox. 
Content for those bars will get by a simple csv-file on a given URL. 
Version: 1.2
Author: xwolf
Author URI: http://blog.xwolf.de
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: statictics, progress, data, widget
Requires at least: 3.3
Tested up to: 3.5

== Description ==

Displays one or more progress bars.
All progress bars get data by an external source (URL) and will be automatically 
updated.

=== Usage ===
1. Use as widget

Simply add the progress bar widget to the sidebar you want to display it.
Within the widget you can set a title and an URL and other optional settings.


2. Use as shortcode within articles and pages

To use the progress bars add a shortcode like this:

   [progressbar url="http://some/url/data.csv"]

Additional attributes:

* color - set a color out of green, blue, red, orange
* unitstr - string to add a unit 
* rounded - make bars and box with rounded borders
* total - show total numbers
* numbers - show numbers of each bar
* numberbar - no break between bar and numbers
* html5 - use <progress>-tag instead of <div><span>..

Example:
    
[progressbar url="http://some/url/data.csv" width="400px" 
             color="blue" rounded="0" numberbar="0" unitstr="Euro"]

This will add a progress bar with data from the given url.
The bar will be displayed in a box of 400px width and blue bars.
Corners are not rounded and numbers will be displayed on an additional
line below the progress bar.



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


Note: Data from external sources will be cached once they was got by
30 minutes.  


== Screenshots ==

1. Default view of progress bars within a article or page

2. Screenshot with progress bars in orange. You can set orange, blue, green and 
red as predefined colors.

3. Bars with numbers on an additional line and a total-bar. 

4. Sample for a progress bars by activating the HTML5 <progress> tag

5. Widget settings

6. Sample progress bar (with settings of the widget in screenshot 5) on a
widget with another background color.


== Installation ==

1. Download ZIP-file
2. Upload to your blog into directory "/wp-content/plugins/"
3. Activate within the plugin-section of wordpress backend