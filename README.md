# phCompile
[![Build Status](https://travis-ci.org/krzksz/phCompile.svg?branch=master)](https://travis-ci.org/krzksz/phCompile)

Library for server-side precompiling AngularJS templates in PHP.

The goal of using phCompile is to let you compile your HTML templates using your existing JSON REST api.

Server side compiling works in a way that lets you easily revert back all of the changes made by phCompile on the client side by either adding speciall class or attribute to the element.

phCompile is still in alpha state, bugs and api changes may occur.

### Use cases
* Serving complete website on first load for better SEO and social crawlers.
* Reusing AngualarJS templates outside client application.
* Static HTML page generation.
* Faster website rendering when using big data chunks.
* Making your website preserve minimal functionality without JavaScript enabled.

### Supported AngularJS features
* {{ }} expressions;
* ng-repeat(without "track by");
* ng-hide, ng-show;
* ng-class, ng-class-even, ng-class-odd;
* ng-bind, ng-bind-template;
* ng-href;
* ng-value;
* ng-src, ng-srcset.

### Roadmap
* Remaining textual AngularJS directives,
* Filters(currently they are expected to break expression that uses them),
* Expressions cache,
* Suggestions?

### Quick Example
```php
$phCompile = new \PhCompile\PhCompile(); // Create PhCompile object
$template = new \PhCompile\Template\Template($phCompile); // Create template
$template->setScope(new Scope(array('foo'=>'bar'))); // Set scope data.

$template->loadHtml('template.html'); // Load from file
$template->setHtml('<span ng-bind="foo"></span>'); // Or just from string
echo $template->compile(); // "<span ng-bind="foo">bar</span>"
```

### [Documentation](https://github.com/krzksz/ngPhCompile/wiki)

### Contribute
You are welcome to contribute your own improvements and ideas. If you have any issues and feature ideas please post them at [project's issues page](https://github.com/krzksz/phCompile/issues).

### Contact
If you would like you can contact me or follow via Twitter [@krzksz](https://twitter.com/krzksz).

### Thanks
I would like to thank [@danchoi](https://github.com/danchoi) for creating his [AngularJS template parser in Haskell](https://github.com/danchoi/ngrender) which gave me inspiration and ideas for this project.
