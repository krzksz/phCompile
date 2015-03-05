# ngPhCompile
[![Build Status](https://travis-ci.org/krzksz/ngPhCompile.svg?branch=master)](https://travis-ci.org/krzksz/ngPhCompile)

Library for server-side precompiling AngularJS templates in PHP.

ngPhCompile tries to mimic AngularJS behaviour for easier usage, you can notice that specially when using `Scope` object
which allows you to use both PHP's `foo['bar']` and JS' `foo.bar` notation to access it's data.

ngPhCompile is still in alpha state, bugs and api changes may occur.

### Use cases
* Serving complete website on first load for better SEO and social crawlers.
* Reusing AngualarJS templates outside client application.
* Static HTML page generation.
* Faster website rendering when using big data chunks.
* Making your website preserve minimal functionality without JavaScript enabled.

### Supported
* {{ }} expressions,
* ng-repeat(without "track by"),
* ng-hide,
* ng-show,
* ng-class,
* ng-bind,
* Compile halting.

### Roadmap
* Docs and wiki update,
* Performance benchmark,
* Filters, currently they are expected to break expression that uses them,
* Custom directives,
* Expressions priorities,
* Expressions cache,
* Suggestions?

## Setting up 
```php
$phCompile = new \PhCompile\PhCompile(); // Create PhCompile object
$template = new \PhCompile\Template\Template($phCompile); // Create template

$template->loadHtml('template.html'); // Load from file
$template->setHtml('<span></span>'); // Or just from string
```

## Compiling
The main goal is to achive as closest similarity to AngularJS as possible. It is also worth noting, that ngPhCompile always adds either class or attribute where it changed template structure so you can revert everything back easily on the client-side.
```php
$phCompile = new \PhCompile\PhCompile();
$template = new \PhCompile\Template\Template($phCompile);
$template->setHtml('<span></span>');

$template->getScope()->setData(array('foo'=>'bar')); // Feed template scope with data
echo $template->compile(); // Compile template HTML
```

#### {{ }} expressions
Expressions work just like in AngularJS, any value or code inside {{}} is replaced with it's evaluation. Remember that function calls inside expressions are forbidden and using them will cause `InvalidExpressionException` to be thrown because of `eval` usage and security reasons.
```php
$phCompile = new \PhCompile\PhCompile();
$template = new \PhCompile\Template\Template($phCompile);
$template->setHtml('{{foo}}');

$template->getScope()->setData(array('foo'=>'bar'));
echo $template->compile(); // Outputs <span ng-phcompile="foo">bar</span>
```
```php
$phCompile = new \PhCompile\PhCompile();
$template = new \PhCompile\Template\Template($phCompile);
$template->setHtml('{{2+2*2}}');
echo $template->compile(); // Outputs <span ng-phcompile="2+2*2">6</span>
```
As you propably noticed, expression value has been covered with `span` element with `ng-phcompile` attribute. This mechanism lets you revert template back on the client-site when AngularJS gets ready.
#### ng-repeat
ng-repeat attribute also works similar to AngularJS and supports both `foo in bar` and `(foo, bar) in baz` methods. The only difference is that ngPhCompile leaves first copy of the element uncompiled with `ng-hide` class, so you can use it when AngularJS kicks in on the client side.

```php
$phCompile = new \PhCompile\PhCompile();
$template = new \PhCompile\Template\Template($phCompile);

$template->setHtml('<span ng-repeat="foo in bar">{{foo.baz}}</span>');
$template->getScope()->setData(
	array(
    	'bar'   =>  array(
        	array(
            	'baz'   =>  'zaz'
            )
        )
	)
);

echo $template->compile();
```
Will output:
```html
<span ng-repeat="foo in bar" class="ng-hide">
	{{foo.baz}}
</span>
<span class="ng-phcompile">
	<span ng-phcompile="foo.baz">zaz</span>
</span>
```
#### ng-bind
ng-bind is one of the attributes that doesn't introduce any additional attributes or classes. This is because it doesn't interrupt AngularJS in any way.

```php
$phCompile = new \PhCompile\PhCompile();
$template = new \PhCompile\Template\Template($phCompile);

$template->setHtml('<span ng-bind="foo"></span>');
$template->getScope()->setData(array('foo'=>'bar'));

echo $template->compile(); // Outputs <span ng-bind="foo">bar</span>
```
#### ng-hide, ng-show
Those visibility classes are also supported in a AngularJS way, `ng-hide` class is added to certain element when conditions are met. They also don't add any extra classes or attributes to template.

```php
$phCompile = new \PhCompile\PhCompile();
$template = new \PhCompile\Template\Template($phCompile);

$template->setHtml('<span ng-hide="foo"></span>');
$template->getScope()->setData(array('foo'=>true));

echo $template->compile(); // Outputs <span ng-hide="foo" class="ng-hide">bar</span>
```
## Configuration

Main configuration is stored inside `Scope` object inside `PhCompile` class.
PhCompile sets some configuration by default:
```php
array(
	'compile' => array(
        /**
         * Class used to tag server side compileed elements.
         */
        'class' => 'ng-phcompile',
        /**
         * Attribute used to tag server side compiled expressions.
         */
        'attr' => 'ng-phcompile'
  	)
)```
You can of course access and overwrite this configuration however you want:
```php
$phCompile->setConfig(
    array(
        'compile' => array('class' => 'custom-class')
    )
);
echo $phCompile->getConfig('compile.class'); // Outputs "custom-class"
echo $phCompile->getConfig('compile["class"]'); // Also outputs "custom-class"
echo $phCompile->getConfig('compile.attr'); // Outputs "ng-phcompile"
echo $phCompile->getConfig('compile["attr"]'); // Also outputs "ng-phcompile"
```

## Directives
Directive are objects responsible for compiling provided `DOMElement` using given `Scope` data. ngPhCompile comes with some default `Directive` objects that correspond with standard AngularJS directives. Those objects must extend `Directive` object to be accepted by `PhCompile`.


## Thanks
I would like to thank [@danchoi](https://github.com/danchoi) for creating his [AngularJS template parser in Haskell](https://github.com/danchoi/ngrender) which gave me inspiration and ideas for this project.
