=== Slick Menu ===

Tags: menu, push menu, sidebar, mega menu, menu widgets
Requires at least: 3.9
Tested up to: 4.9.6
Stable tag: 1.0.9.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Advanced Vertical Menu with multi-level functionality that allows endless nesting of navigation elements. Automatically integrates with your existing Wordpress menus

== Description ==

Slick Menu is more than just a menu plugin. It can be used to create unlimited Multi Level Vertical Menus or Sidebars with rich content and multiple style options and animation effects.

Every menu level is customizable featuring background colors, images, videos, overlays. Insert rich content by simply adding shortcodes and widgets directly within WordPress Nav Menu editor. 

== Installation ==

Installing "Slick Menu" can be done by following these steps:

1. Download the plugin via "CodeCanyon.net" 
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= V.1.0.9.8 - 22.06.2018 =
- Added option to set logged in user avatar as logo

= V.1.0.9.7 - 16.06.2018 =
- Added Google Font API Key setting within global settings

= V.1.0.9.6 - 19.05.2018 =
- Fix issue with scroll to section
- Fixed issue causing php warning message: "count(): Parameter must be an array"

= V.1.0.9.5 - 30.03.2018 =
- Added animation option to footer section
- New Apple Menu Demo
- Load modernizr using wp_enqueue_script instead of ajax
- Do not close menu before loading a new page when clicking a menu item
- Fixed issue with svg icon color not being applied in some cases
- Fix issue with site crashing when using Polylang plugin (However, slick menu still cannot be translated using Polylang. Only supports WPML for now. )

= V.1.0.9.4 - 26.06.2017 =
- Fixed issue with the "Activate Current Menu Item on Menu Open" option
- Fixed minor CSS issues

= V.1.0.9.3 - 21.05.2017 =
- Added global option to load Dynamic CSS Internally instead of using an external link.
- Google fonts are now enqueued normally instead of using css @import

= V.1.0.9.2 - 13.05.2017 =
- Fix issues with menus loaded on HTTPS websites
- Removed menu item Label Truncate option for now, unstable.

= V.1.0.9.1 - 09.05.2017 =
- Added menu item Label Visibility option (Show on hover / Hide on hover)
- Added menu item Label Truncate option (Truncate to 1 to 5 lines)
- Fix dynamic css caching bug
- Minor CSS Fixes

= V.1.0.9 - 09.05.2017 =
- Added background pattern & overlay options to level headers and footers
- New Extension Slick Menu - Dynamic Posts
- Fix menu caching bug when using Nav Menu Roles
- Minor CSS Fixes
- Support PHP 5.4

= V.1.0.8.2 - 25.04.2017 =
- Fixed issue with license activation caused by minor code change in earlier version

= V.1.0.8.1 - 24.04.2017 =
- Minor customizer bug fixes.
- Fixed mobile breakpoint for always visible menu, hide by default

= V.1.0.8 - 22.04.2017 =
- Added option to select a page to be used as content for the Level Description.
- Added 3 new Menu Animations (Fade In / Scale In / Scale Out)
- Level width can now be set using %, px, vw
- Level menu item heights can now be set using %, px, vh
- Added Level Footer Minimum Height option
- Faster Ajax Menu Build Request
- Faster menu settings / customizer loading
- Faster dynamic css loading
- Restructure Extensions Code
- Update Modernizr Library
- Fixed issue with local license being reset by it self.
- Fixed compatibility issue with Nav Menu Roles
- Lazyload Modernizr fixes issue with some themes
- Minor CSS Fixes

= V.1.0.7 - 05.03.2017 =
- Added more transforms options for menu items. (Initial State / Visible State / Hover State)
- Added Perspective / Perspective Origin / Transition Delay & 3D Rotate option to menu items transforms section.
- Added Box Shadow options for menu items
- Added option to show Back link icon only
- Added option to replace menu item with an image instead.
- Auto hide top sticky back link on scroll
- Enable content filters on mobile
- Added option to override item title
- Fixed issue with menu item links opening in the same window while their target is set to open in a new window.
- Fix issue with menu to menu triggers no being triggered properly
- Minor CSS fixes

= V.1.0.6 - 04.02.2017 =
- Added option to scroll level automatically to current level item
- Added option to show Level Scrollbars in Level General Settings
- Added option to automatically open current menu item level once menu is open
- Added Hamburger Trigger Label option. Label can be positioned above, below, before or after the hamburger trigger
- Added Hamburger Trigger Visibility option. Show on All, Mobile Only or Desktop Only
- Fix cache issue with WPML
- Fixed javascript bug causing a conflict with the Beaver Page Builder
- Animate.css - Prefixed all animation classes to avoid conflicts with other plugins / themes

= V.1.0.5.1 - 21.12.2016 =
- Added new JS API function SlickMenu.openSubLevel(menu_id, menu_item_id, callback) to programatically open a menu sub level. More Info
- License System now allows the same purchase code to be valid within a multisite setup.
  1 License: unlimited domains, subdomains, folders as long as as it is under a multisite.
- License System now allows you to revoke the license locally.

= V.1.0.5 - 12.12.2016 =
- Added a 12 column grid system allowing you to insert menu items in multiple columns and create a grid of menu items. See new demo (Full Screen Gallery)
- Added the option to add images Above, Below or Behind a menu item. See new demo (Full Screen Gallery)
- Added the option to set filters to menu item images
- Added the option to set a different menu item height for each item
- Added the option to have a sticky / over the content header / footer
- Added global option to Fade In site content once Slick Menus are fully loaded
- Added better support for Jupiter Theme
- Added better support for BeTheme Theme
- Added better support for Berg Theme
- Minor CSS Fixes
- License Validation Fix
- Fixed customizer issues on WordPress 4.7
- Fixed menu item icons on regular menus that are not activated as Slick Menu

= V.1.0.4 - 26.10.2016 =
- Level description now supports shortcodes
- Added option to set a url to the logo. Scroll to anchor also supported.
- Added a Flush Cache option. Note: Saving menu settings also flushes the cache.
- Added better support for Avada Theme
- Added better support for Kinetika Theme
- Added better support for Tower Theme
- Replaced Menu Level Title h2 tags with a regular div tags to avoid affecting SEO
- Force override existing theme trigger behaviour when adding a custom trigger selector to a Slick Menu
- Updated Modernizr Library
- Minor CSS Fixes
- Fix issue with full width revolution slider when a menu is active.
- Customizer: Fixed social networks color picker not working when adding a new network

= V.1.0.3.4 - 29.09.2016 =
- Updated translation files
- Added mobile fallback for the Site Wrapper Videos
- Added Content Filter option for individual menu levels
- Added Transparent and Half Transparent Content Filters
- Added Mobile Centered Level Option that forces everything to be centered on mobile breakpoint
- Added W3 Total Cache and WP Super Cache support to automatically flush the cache whenever menu settings are changed.
- New Import / Export extension released. More Info
- Minor CSS Fixes

= V.1.0.3.3 - 21.09.2016 =
- Fixed minor bug with the plugin updater

= V.1.0.3.2 - 19.09.2016 =
- Added better support for Bridge Theme

= V.1.0.3.1 - 17.09.2016 =
- Added better support for Yolo Naveda Theme

= V.1.0.3 - 16.09.2016 =
- Added option to select a different menu animation on mobile (only supported ones)
- Added PHP 5.3 compatibility
- Improved mobile menus performance
- Merge all Google Font imports into 1 for a faster page load.
- Fixed menu scroll on Android devices

= V.1.0.2 - 09.09.2016 =
- Fixed intermittent bug with back button on Firefox
- Added better support for The 7 Theme

= V.1.0.1 - 07.09.2016 =
- Fixed javascript event bug on Firefox

= V.1.0.0 - 06.09.2016 =
- Initial Version

