# Section-Specific Colored Tabs for WordPress

## Demonstration of Oject-Oriented Beans/UIKit Implementation

Sometimes it's a nice idea to use colors to represent different sections of a large site. It is certainly possible in WordPress, but most themes don't offer this capability easily.

This is a scaled-down demo of how I accomplished this for a couple of different clients using the [Beans/UIKit framework for WordPress](https://getbeans.io).

This is a child theme for Beans, so you need to have Beans installed as a prerequisite. [Clone it here](https://github.com/Getbeans/Beans).

### Definitely Read This Bit

It would be a good idea to run this on a fresh WordPress install without any pages, as the child theme will initially create a set of 5 pages and corresponding menu items.

### How It Works

A list of pages is defined (eg. your site's top-level pages) whose corresponding menu tabs and section headers will be colored.  CSS classes are established based on these page names using slugs or some other means.  In the demo an array is used for this.

Beans/UIKit uses LESS, which makes it easy to loop through the list of page slugs or ids and perform all of the related styling at once.

### In Real Life

Here is [one of the sites](https://www.cvcorps.org) where this approach has been put to use.
