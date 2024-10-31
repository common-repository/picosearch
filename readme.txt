=== WordPress Search Plugin - PicoSearch ===
Contributors: picocodes
Tags: search, wordpress search, autocomplete, query completion,search statistics, search logs
Requires at least: 4.0.0
Tested up to: 5.0
Stable tag: 1.0.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Shows your users relevant search results, highlights search terms, suggests search terms and logs search queries among other features to your in-built search engine
== Description ==

PicoSearch dramatically improves your blog's search results by displaying the most relevant results to your users. 
This WordPress plugin highlights search terms so that your visitors can easily view which part of the post matched the search.

In addition; you can enable query suggestions so that your visitors can get relevant results while they are still searching in the searchbox.

= Ranks the search results by relevance =
Our Search Plugin uses either BM25F or tf-idf to calculate the relevance of each search result. The search results are then ranked according to how relevant they are.
You can choose to favor certain post types or categories.

= Highlights search terms =
PicoSearch highlights search terms on the search results page so that your visitors can quickly see which part of the result matched the search term.

= Suggests search terms =
When your users start typing into the search box, PicoSearch will suggest search terms via ajax just the way Google does.
This speeds up your visitors search experience.

= Logs search queries =

PicoSearch logs all search terms so that you can view them later and get an idea of what your users are searching.
You can then use that information to create new blog content or republish old content.

= Gives you total control =

Easily exclude content from certain categories, tags, post types or by certain authors from the search results.

= Understands most languages =

Unlike the default WordPress search engine, PicoSearch stems words from most languages. This means that a search for "playing" will also find documents that contain the terms "play", "players" etc.

= Other features =

1. Adjust the weighting of each individual post type. 
2. Search through the output of shortcodes.
3. Export and import settings for easy migrations.
4. Fast support.

== Installation ==

= Minimum Requirements =

* PHP version 5.2.4 or greater (PHP 7.0 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* WordPress 4.0+

= Automatic installation =

To do an automatic install of PicoSearch, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “PicoSearch” and click Search Plugins. Once you’ve found our search plugin,  you can install it by simply clicking “Install Now”.

= Manual installation =

[Instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions ==

= Will this plugin work with my theme? =

Yes; PicoSearch will work with any theme, but may require some styling to make it match nicely.

= How can I add a new search field? =

Contact your theme provider on how to add a search box into your website. PicoSearch will automatically detect the new search box.

= PicoSearch is awesome! Can I contribute? =

Yes you can! Join in on our [GitHub repository](http://github.com/picocodes/picosearch/) :)

== Changelog ==

= 1.0.0 - 2017-04-10 =
* WordPress Search Plugin (PicoSearch) is born.

= 1.0.3 - 2018/6/28 =
* Fix hardcoded database table names as pointed out by @CherryAustin.

= 1.0.5 - 2018/7/2 =
* Fixed an incorrect call to do_action() at the bottom of the main search function