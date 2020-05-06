# Handshake RSS Feed Plugin

**Plugin Name:** Handshake RSS Feed

**Plugin URI:** https://github.com/asuengineering/handshake-rss-feed

**Author:** Steve Ryan

**Author URI:** https://engineering.asu.edu

**Version:** 0.1

---

This is a plugin for WordPress that allows someone to embedd an RSS feed from Handshake into a WordPress post or page.

## Includes

This plugin creates a shortcode `[handshake-rss]` which can be included in any post or page to create a list of opportunities from Handshake. No options for the shortcode, just plug and play. Works within a Divi context. (And should also work within most other page builders as well.)

## Dependencies

- The plugin adds a theme options page that is currently powered by Carbon Fields. That library should be included in the theme in order for the plugin to function correctly. (All ASU Engineering WordPress products have this library already included.)
- The plugin is also dependent on having an API key from RSS 2 JSON available. The plugin takes an existing RSS feed from Handshake and converts it to JSON prior to displaying on the page.

## Setup

- TODO: Add setup notes here.

## Changelog

### Version 0.1

- Initial deployment of plugin. First stable commit to GitHub.
