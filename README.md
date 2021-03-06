
[![E-Access Bulletin][eab-logo-1]][eab]

[![Build status: Travis-CI][travis-icon]][travis-ci]
[![js-semistandard-style][semi-icon]][semi]
[![WordPress code standard][wp-icon]][wp]

# wp-eab-plugins #

This is a collection of WordPress plugins to facilitate editing and
publishing the E-Access Bulletin, via email and the Web.

> "_E-Access Bulletin is a free, monthly, text-only email newsletter on
> technology access by those with disabilities and older people._"

* [E-Access Bulletin Live][eab live]
* [E-Access Bulletin archive][eab]
* [Subscribe to the E-Access Bulletin newsletter][sub]

### Post type

WordPress custom [post type][]:  `eab_bulletin`

### Archive shortcode

WordPress [shortcode][]:  `[eab_archive]`

### Markdown filter

WordPress [filter][] to convert HTML to plain text/ Markdown:  `the_content_markdown`

## GAAD widget plugin

Typical usage ~ `wp-content/themes/../header.php`:

```php
<div id="container">

<?php do_action( 'gaad_widget' ) ?>
```

## PHPList shortcode

WordPress [shortcode][]:  `[phplist id=1 ] Intro text... [/phplist]`

## Install .. Test

```sh
composer install
composer npm-install
composer test
```

---

* [GitHub: nfreear/wp-eab-plugins][gh]
* [GitHub: nfreear/headstar-web][hed] ~ legacy Bulletins.


---
WordPress plugins: Copyright © Nick Freear, 06-November-2017.

_(E-Access Bulletin: © 2017 Headstar Ltd.)_


[gh]: https://github.com/nfreear/wp-eab-plugins
[travis-ci]: https://travis-ci.org/nfreear/wp-eab-plugins "Build status – Travis-CI"
[travis-icon]: https://travis-ci.org/nfreear/wp-eab-plugins.svg?branch=master
[semi]: https://github.com/Flet/semistandard "Javascript coding style — 'semistandard'"
[semi-icon]: https://img.shields.io/badge/code_style-semistandard-brightgreen.svg?_style=flat-square
[wp]: https://packagist.org/packages/wp-coding-standards/wpcs
[wp-gh]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
[wp-icon]: https://img.shields.io/badge/code_style-WordPress-blue.svg
[wp-pl-ico]: https://img.shields.io/badge/WordPress-plugin-blue.svg
[eab-logo-1]: http://www.headstar.com/images/EAB-logo-small-trans.png
[hed]: https://github.com/nfreear/headstar-web "Legacy archive, and Perl code-base."
[sub]: http://headstar.com/eablive/?page_id=80
[EAB]: http://headstar.com/eab/archive.html "E-Access Bulletin archive (EAB)"
[EAB Live]: http://headstar.com/eablive/ "E-Access Bulletin Live (EAB)"

[post type]: https://codex.wordpress.org/Post_Types "WordPress post types."
[shortcode]: https://codex.wordpress.org/shortcode "WordPress shortcodes."
[filter]: https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content "WordPress 'the_content' filter."

[End]: //.
