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

## Description

This plugin optimize page rendering of WordPress theme.

### Features

- Add `loading="lazy"` or `loading="eager"` with your images.
- Add `defer` or `async` attributes to your scripts.
- Add `rel="preload"` to your stylesheets. Polyfill for old browsers is also available.

All of above are selectable and you can customize with white list.

### Case 1. Image Loading

If you wish header logo(`.custom-logo`) and main post thumbnail(`.post-feature-image`) should be load faster because they are in first view.

Just put `custom-logo,post-feature-image` at **High Priority** section in your setting screen.

### Case 2. Stop Defer

Defering JavaScripts sometimes breaks your site.
For example, if a script requires just in time operation with inline script tag, it will fail.

```
<script id="some-script-js" src="somescript.js" defer></script>
<script>
new SomeScript();
</script>
```

To avoid this, Add `some-script` handle name in **Deny Defer** section in your setting screen.

Generally speaking, many JavaScripts loaded in your WordPress are issued by WordPress Core, plugins, themes, your custom code, and so on.

To optimize JavaScript loading, try and error approaches works fine.

### Case 3. Critical Stylesheet

`rel="preload` attributes makes your stylesheets loaded asynchrounsely, but FOUC(Flush of Unstyled Content) happens.

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

### 1.0.0

* First release.
