# Render Faster

Tags: theme, speed, optimization  
Contributors: tarosky, Takahashi_Fumiki  
Tested up to: 5.7  
Requires at least: 5.5  
Requires PHP: 5.6  
Stable Tag: nightly  
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Render the page faster. Enhance your site's load page for Core Web Vital.

## Deprecation Notice

**This plugin is no longer maintained and will be closed on WordPress.org.**

When Render Faster was first released (2021), it provided features that were missing from WordPress core. Since then, WordPress has absorbed most of those optimizations natively, which means this plugin is no longer necessary on modern WordPress sites (6.3+).

If you are currently using Render Faster, please deactivate and remove it, and use the replacements described below.

### Migration Guide

| Feature of Render Faster | Replacement | Available since |
|---|---|---|
| `loading="lazy"` on `<img>` | WordPress core (automatic) | WP 5.5 |
| `loading="lazy"` on `<iframe>` | WordPress core (automatic) | WP 5.7 |
| `fetchpriority="high"` / eager loading for the LCP image | WordPress core (automatic) | WP 6.3 |
| `defer` / `async` on scripts | Script Loading Strategies API — pass `strategy` to `wp_enqueue_script()` / `wp_register_script()` | WP 6.3 |
| Loading only the block styles used on the page | `should_load_separate_core_block_assets` filter, or `wp_enqueue_block_style()` | WP 5.8 |
| Removing `jquery-migrate` | A small custom snippet, or a dedicated plugin such as _Disable jQuery Migrate_ | — |
| Preloading stylesheets (`rel="preload"`) and the `loadCSS` polyfill | Not recommended on modern browsers. Consider Critical CSS tooling or a full-featured performance plugin (WP Rocket, NitroPack, FlyingPress, LiteSpeed Cache, Jetpack Boost, etc.) | — |
| Lazy loading Twitter / Instagram embed helper scripts | Handle inside your theme, or use a full-featured performance plugin listed above | — |

## Description

This plugin optimize page rendering of WordPress theme.

### Features

- Add `loading="lazy"` or `loading="eager"` with your images.
- Add `defer` or `async` attributes to your scripts.
- Add `rel="preload"` to your stylesheets. Polyfill for old browsers is also available.
- Remove default script helper of embeds(twitter, instagram) and load one after user interaction.

All of above are selectable and you can customize with white list.

### Case 1. Image Loading

If you wish header logo(`.custom-logo`) and main post thumbnail(`.post-feature-image`) should be load faster because they are in first view.

Just put `custom-logo,post-feature-image` at **High Priority** section in your setting screen.

### Case 2. Stop Defer

Defering JavaScripts sometimes breaks your site.
For example, if a script requires just in time operation with inline script tag, it will fail.

```
&lt;script id="some-script-js" src="somescript.js" defer&gt;&lt;/script&gt;
&lt;script&gt;
new SomeScript();
&lt;/script&gt;
```

To avoid this, Add `some-script` handle name in **Deny Defer** section in your setting screen.

Generally speaking, many JavaScripts loaded in your WordPress are issued by WordPress Core, plugins, themes, your custom code, and so on.

To optimize JavaScript loading, try and error approaches works fine.

### Case 3. Critical Stylesheet

<code>rel="preload"</code> attributes makes your stylesheets loaded asynchrounsely, but FOUC(Flush of Unstyled Content) happens.

To avoid this, include critical CSS to **Deny List** in your setting screen. Critical CSS are generally your theme's main stylesheet.

## Installation

### From Plugin Repository

Click install and activate it.

### From Github

See [releases](https://github.com/tarosky/render-faster/releases).

## FAQ

### Where can I get supported?

Please create new ticket on support forum.

### How can I contribute?

Create a new [issue](https://github.com/tarosky/render-faster/issues) or send [pull requests](https://github.com/tarosky/render-faster/pulls).

## Changelog

### 1.2.0

* Support separate loading of block styles. Available on WP 5.8 and later.

### 1.1.0

* Support embed optimization.

### 1.0.0

* First release.
