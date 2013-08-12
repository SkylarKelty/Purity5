Purity5 [![Build Status](https://travis-ci.org/SkylarKelty/Purity5.png?branch=master)](https://travis-ci.org/SkylarKelty/Purity5)
=======

Purity5 is a HTML5 query library, with a similar interface to jQuery.

To use, simply include Purity5 and use like so:
```php
$html = Purity5::parse($markup);
$title = $html("title");
$table_headings = $html("body table tr th");
$first_table_headings = $html("body table tr th:first-child");
$active_links = $html("body a[class=active]");
$active_current_links = $html("body a[class=active+current]");
```

### Supported Selectors
* a > b
* a + b
* *
* .a
* #a
* a.b
* a#b
* .a.b
* a:first-child
* a:last-child
* a:nth-child(num|even|odd)
* a[attr='val']