# tempan #

*tempan* is a template engine which uses annotated HTML to render data provided by a view model.

## Purpose ##

This project was inspired by Iain Dooleys post on [workingsoftware.com]. In his post, Iain argues that "As soon as I'm looking at more than one programming or markup language in the same file, I'm looking at spaghetti code." He refers to the mix-up of a templating language and HTML with most templating engines. He suggests that instead one should use HTML itself to bear the meta-data needed to manipulate the HTML on DOM level. He calls this *Template Animation*, hence the name of this project.

The main advantage is the template resulting to be completely independent of the rendering engine. Instead of creating a template with all kind of curly placeholders, the markup looks like this

	<div property="person">
		Name: <span property="name">John Wayne</span>
		Homepage: <a property="url" href="http://johnwayne.com">
			<span property="caption">johnwayne.com</span>
		</a>
	</div>
	
The `property` attributes refer to the field names of the view model below. This attribute is also used in the *Resource Description Framework in attributes* or [RDFa], hence the template can be easily extended to become a RDFa document.

	{
		"Person": {
			"name": "John Wayne",
			"url" : {
				"href": "http://johnwayne.com",
				"caption": "johnwayne.com"
			}
		}
	}

[workingsoftware.com]: http://www.workingsoftware.com.au/page/Your_templating_engine_sucks_and_everything_you_have_ever_written_is_spaghetti_code_yes_you
[RDFa]: http://rdfa.info/
[RDFa/Play]: http://rdfa.info/play/

## Main Features ##

The feature set includes conditionals, repetition and dynamic values. This makes the *tempan* both compact and powerful.

*Note*: Although JSON syntax is used to describe the view models, they are actually PHP objects or arrays.

### Replace content with static values ###

The text content and attributes of an element are replaced with the value of the view model matching the attributes name in the `property` attribute.

	{ 
		"one": "Hello", 
		"two": { 
			"value": "World",
			"title": "Everyone"
		}
	}
	
leads to
	
	<span property="one">Hello</span>
	<span property="two" title="Everyone"><span property="value">World</span></span>

### Navigate complex data ###

Nested data structures can be traversed as well. To access the inner data of the following view model

	{
		"recipient": "World"
		"outer": {
			"inner": {
				"message": "Hello"
			}
		}
	}
	
the template would be

	<div property="outer">
		<span property="inner">
			<span property="message">Hello</span>
			<span property="recipient">World</span>
		</span>
	</div>

The scope of the properties is not limited to the current sub-model but inherited by the containing models. In this example, `recipient` is inherited by the models grand-father. However, the scope of attributes is limited to the current model.

### Replace content with dynamic values ###

The referenced data may be the return value of a method or closure. Only zero-arguments methods are possible. Closures receive the current element and instance on `Animator` as their arguments. This way, variables can be generated on-demand and element modified dynamically.

	{
		"number": {
			"value": 2,
			"isMany": function (element, animator) { return this.value != 1 },
			"shorten": function (element, animator) { return element->getContent()->substr(0, 4); }
		}
	}

can be used with

    <span property="number">
		<span property="value">2</span> car<span property="isMany">s</span>
		<span property="shorten">Some long string</span>
	</span>

### Remove elements ###

If the value is `false`, `null`, an empty array, the corresponding element will be removed. If the value is `true`, the element won't be modified but its children. An undefined propery is ignored.

### Repeated elements ###

If the value of a field is a list, the element will be repeated for each item of the list.

	{
		"pets": {
			"isMany": true,
			"count": 2,
			"pet": [
				{ "name": "Cat" },
				{ "name": "Dog" }
			]
		}
	}
	
Siblings of the element in the template will be removed before repeating the element. Thus the following rendered result can be used as its template as well.
	
	<div rel="pets">
		<p>		
			I have <span property="count">2</span> pet<span property="isMany">s</span>
		</p>
		<ul>
			<li rel="pet">
				<span property="name">Cat</span>
			</li>
			<li rel="pet">
				<span property="name">Dog</span>
			</li>
		</ul>
	</div>

## Installation ##

There are three options. If you already have [Composer], you can use

	php composer.phar create-project watoki/tempan

to check out *tempan* as a stand-alone project (you'll need git and php as well). To run the test suite use
	
	cd tempan
	phpunit
	
If you don't have Composer yet, or want to install a different branch you can use

    git clone https://github.com/watoki/tempan.git
    cd tempan
    php install.php

To use it in your own project, add the following lines to your `composer.json`.

    "require" : {
        "watoki/tempan" : "*"
    },
    "minimum-stability": "dev"
	
[Composer]: http://getcomposer.org/

## Basic Usage ##

For a complete description of all features and usage examples, check out the test cases in the [spec] folder. You can find an example using all the basic features together in [ComplexTest.php].

[spec]: https://github.com/watoki/tempan/tree/master/spec/watoki/tempan
[ComplexTest.php]: https://github.com/watoki/tempan/tree/master/spec/watoki/tempan/ComplexTest.php
		
