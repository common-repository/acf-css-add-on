=== ACF CSS ADD-ON ===
Contributors: yatnam, vanisreedivakar, akhilkjuliyan
Donate link: https://yatnam.com/
Tags: acfca, css, advanced, custom, field, fields, class, acf, classes
Requires at least: 5.0 or higher
Tested up to: 5.2.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin can be used as an add-on to the Advanced Custom Fields Plugin.
Helps you add a class to any of the ACF Custom Fields, including repeater, flexible content. 
The brush icon corresponds to the ACF CSS ADD-ON feature. You can click on this to add classes for the field.
Add multiple classes to the fields by seperating them with spaces. 
Add class to options pages also.


== Installation ==

From your WordPress dashboard

1. **Visit** Plugins > Add New
2. **Search** for "ACF CSS Add-on"
3. **Activate** ACF CSS Add-on from your Plugins page. Make sure you have installed and activated Advanced Custom Fields Plugin or Advanced Custom Fields Pro Plugin.
4. **Click** on any post that has ACF custom fields and start adding classes.

OR

1. Upload `acfca.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. **Click** on any post that has ACF custom fields and start adding classes.


== Frequently Asked Questions ==


== Screenshots ==

1. ACF CSS Add-on layout.
2. The brush icon is the ACF CSS ADD-ON feature.
3. Use ACFCA classes in options page.
4. Use ACFCA classes in options page sub fields.
5. Use ACFCA Classes to any page or post.
6. Use ACFCA Classes to any page or post sub fields.

This screen shot description corresponds to screenshot-1.png. Note that you can always add one or more classes seperated with space and hit save. This screen shot description corresponds to screenshot-2.png. The brush icon corresponds to the ACF CSS ADD-ON feature. You can click on this to add classes for the field. Other screenshots corresponds to its usage in pages, posts and options page.



== Changelog ==


== Arbitrary section ==
Use get_field_classes('fieldname') for simple fields eg. text, url, password, textarea
Use get_sub_field_classes('fieldname') for sub-fields eg. repeater, flexible content
Use get_field_classes('fieldname', 'option') for simple fields eg. text, url, password, textarea in options page
Use get_sub_field_classes('fieldname', 'option') for sub-fields eg. repeater, flexible content in options page
To add multiple classes, seperate the classes with spaces.  