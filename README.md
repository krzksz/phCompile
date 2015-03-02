# ngPhRender
[![Build Status](https://travis-ci.org/krzksz/ngPhRender.svg?branch=master)](https://travis-ci.org/krzksz/ngPhRender)

Library for server-side precompiling AngularJS templates in PHP.

ngPhRender tries to mimic AngularJS behaviour for easier usage, you can notice that specially when using `Scope` object
which allows you to use both PHP's `foo['bar']` and JS' `foo.bar` notation to access it's data.

ngPhRender is still in alpha state, bugs and api changes may occur.

### Use cases
* Serving complete website on first load for better SEO and social crawlers.
* Reusing AngualarJS templates outside client application.
* Static HTML page generation.
* Faster website rendering when using big data chunks.
* Making your website preserve minimal functionality without JavaScript enabled.

### Supported
* {{ }} expressions,
* ng-repeat(special properties like $index, $first etc. are WIP),
* ng-hide,
* ng-show,
* ng-class,
* ng-bind.

### Roadmap
* Filters, currently they are expected to break expression that uses them,
* Custom directives,
* Expressions priorities,
* Expressions cache,
* Render halting,
* Suggestions?

## Setting up 
```php
$phRender = new \PhRender\PhRender(); // Create PhRender object
$template = new \PhRender\Template\Template($phRender); // Create template

$template->loadHtml('template.html'); // Load from file
$template->setHtml('<span></span>'); // Or just from string
```

## Rendering
The main goal is to achive as closest similarity to AngularJS as possible. It is also worth noting, that ngPhRender always adds either class or attribute where it changed template structure so you can revert everything back easily on the client-side.
```php
$phRender = new \PhRender\PhRender();
$template = new \PhRender\Template\Template($phRender);
$template->setHtml('<span></span>');

$template->getScope()->setData(array('foo'=>'bar')); // Feed template scope with data
echo $template->render(); // Render template HTML
```

#### {{ }} expressions
Expressions work just like in AngularJS, any value or code inside {{}} is replaced with it's evaluation. Remember that function calls inside expressions are forbidden and using them will cause `InvalidExpressionException` to be thrown because of `eval` usage and security reasons.
```php
$phRender = new \PhRender\PhRender();
$template = new \PhRender\Template\Template($phRender);
$template->setHtml('{{foo}}');

$template->getScope()->setData(array('foo'=>'bar'));
echo $template->render(); // Outputs <span ng-phrender="foo">bar</span>
```
```php
$phRender = new \PhRender\PhRender();
$template = new \PhRender\Template\Template($phRender);
$template->setHtml('{{2+2*2}}');
echo $template->render(); // Outputs <span ng-phrender="2+2*2">6</span>
```
As you propably noticed, expression value has been covered with `span` element with `ng-phrender` attribute. This mechanism lets you revert template back on the client-site when AngularJS gets ready.
#### ng-repeat
ng-repeat attribute also works similar to AngularJS and supports both `foo in bar` and `(foo, bar) in baz` methods. The only difference is that ngPhRender leaves first copy of the element unrendered with `ng-hide` class, so you can use it when AngularJS kicks in on the client side.

```php
$phRender = new \PhRender\PhRender();
$template = new \PhRender\Template\Template($phRender);

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

echo $template->render();
```
Will output:
```html
<span ng-repeat="foo in bar" class="ng-hide">
	{{foo.baz}}
</span>
<span class="ng-phrender">
	<span ng-phrender="foo.baz">zaz</span>
</span>
```
#### ng-bind
ng-bind is one of the attributes that doesn't introduce any additional attributes or classes. This is because it doesn't interrupt AngularJS in any way.

```php
$phRender = new \PhRender\PhRender();
$template = new \PhRender\Template\Template($phRender);

$template->setHtml('<span ng-bind="foo"></span>');
$template->getScope()->setData(array('foo'=>'bar'));

echo $template->render(); // Outputs <span ng-bind="foo">bar</span>
```
#### ng-hide, ng-show
Those visibility classes are also supported in a AngularJS way, `ng-hide` class is added to certain element when conditions are met. They also don't add any extra classes or attributes to template.

```php
$phRender = new \PhRender\PhRender();
$template = new \PhRender\Template\Template($phRender);

$template->setHtml('<span ng-hide="foo"></span>');
$template->getScope()->setData(array('foo'=>true));

echo $template->render(); // Outputs <span ng-hide="foo" class="ng-hide">bar</span>
```
## Configuration

Main configuration is stored inside `Scope` object inside `PhRender` class.
PhRender sets some configuration by default:
```php
array(
	'render' => array(
        /**
         * Class used to tag server side rendered elements.
         */
        'class' => 'ng-phrender',
        /**
         * Attribute used to tag server side compiled expressions.
         */
        'attr' => 'ng-phrender'
  	)
)```
You can of course access and overwrite this configuration however you want:
```php
$phRender->setConfig(
    array(
        'render' => array('class' => 'custom-class')
    )
);
echo $phRender->getConfig('render.class'); // Outputs "custom-class"
echo $phRender->getConfig('render["class"]'); // Also outputs "custom-class"
echo $pgRender->getConfig('render.attr'); // Outputs "ng-phrender"
echo $pgRender->getConfig('render["attr"]'); // Also outputs "ng-phrender"
```

## Renderers
Renderers are objects responsible for parsing provided `DOMElement` using given `Scope` data. ngPhRender comes with some default `Renderer` objects that correspond with standard AngularJS templating attributes. Those objects must extend `Renderer` object to be accepted by `PhRender`.


## Thanks
I would like to thank [@danchoi](https://github.com/danchoi) for creating his [AngularJS template parser in Haskell](https://github.com/danchoi/ngrender) which gave me inspiration and ideas for this project.
