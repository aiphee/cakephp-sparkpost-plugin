# CakePHP SparkPost Plugin
A CakePHP plugin for sending mail via SparkPost's REST API

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://api.travis-ci.org/syntaxera/cakephp-sparkpost-plugin.png)](https://travis-ci.org/syntaxera/cakephp-sparkpost-plugin)
[![Coverage Status](https://coveralls.io/repos/github/syntaxera/cakephp-sparkpost-plugin/badge.svg?branch=master)](https://coveralls.io/github/syntaxera/cakephp-sparkpost-plugin)
[![Total Downloads](https://img.shields.io/packagist/dt/syntaxera/cakephp-sparkpost-plugin.svg)](https://packagist.org/packages/syntaxera/cakephp-sparkpost-plugin)
[![Latest Stable Version](https://img.shields.io/packagist/v/syntaxera/cakephp-sparkpost-plugin.svg?label=stable)](https://packagist.org/packages/syntaxera/cakephp-sparkpost-plugin)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/syntaxera/cakephp-sparkpost-plugin.svg?label=unstable)](https://packagist.org/packages/syntaxera/cakephp-sparkpost-plugin)

# Introduction
## What is SparkPost?
[SparkPost](https://www.sparkpost.com) is a web-based email delivery service that allows web application developers to
send transactional email via a flexible and reliable REST API. You may have heard of other email delivery services such
as [Mandrill](https://mandrillapp.com) and [SendGrid](https://sendgrid.com).

Unfortunately these services are either not free or were once free but are no longer. Luckily, sending emails via
SparkPost is free if you send less than 100,000 emails per month (which most fledgling webapps do).

Syntax Era developed this plugin because we use CakePHP in most of our apps,and because we noticed that there weren't
any CakePHP plugins for SparkPost. We hope that this plugin proves to be useful to the CakePHP community at large but
especially those migrating from Mandrill or another paid email service who still aren't ready/able to pay for the tiny
amount of transactional emails they send.

#### Transactional email...?
Transactional email is email that is sent to notify users of something, or sometimes to prompt users to complete an
action. For example, password reset emails, account verification emails, and "you just got a message" emails are
all transactional.

For more information, see [What is transactional email?](https://blog.mailchimp.com/what-is-transactional-email).

## What is CakePHP?
CakePHP is a rapid-development PHP web application framework that uses the MVC (Models/Views/Controllers) architecture.
It was first released in August 2005 and is now on version 3.0 and is more powerful than ever! One of CakePHP's great
features is that it supports extending its own functionality via the use of plugins, of which this is one.

For more information about CakePHP, visit their website at [CakePHP.org](http://cakephp.org).

# Installation
## Command-Line Installation
Using Composer is the recommended way of installing this plugin because it allows you to easily download the plugin and
its dependencies and keep them up-to-date and make use of Composer's awesome class auto-loader.

To install the plugin using Composer:

1. Open a terminal and `cd` to your CakePHP application's root directory.
2. Run `composer require syntaxera/cakephp-sparkpost-plugin` to download the plugin and its dependencies.
3. If you'd like, open `composer.json` for editing and modify the versioning schema for the plugin to your liking.

### Finding a HTTP client

We are not coupled to any specific HTTP client. You need to install one yourself. Read the 
[HTTPlug documentation](http://docs.php-http.org/en/latest/httplug/users.html) and choose a client 
from [this list](https://packagist.org/providers/php-http/client-implementation). 

To be on the safe side you need Guzzle and MessageFactories that creates a PSR-7 request. 

```bash
$ composer require php-http/guzzle6-adapter php-http/message
```

## Manual Installation
You can also install the plugin manually by clicking the "Download ZIP" button above and unzipping it into your CakePHP
application's `plugins/` directory, **however if you do this you will have to perform updates manually as well.**

If you're familiar with git you can also download the repository directly to your hard drive using `git clone`. This
allows you to stay on the bleeding-edge of the plugin's development, but be warned that it's called "bleeding-edge" for
a reason.

# Setup
To add the plugin to your application, activate the plugin in your application's bootstrap file:

```php
Plugin::load('SparkPost');
```

Then add the following to your application's config file:

```php
'SparkPost' => [
    'Api' => [
        'key' => 'YOUR_SPARKPOST_API_KEY'
    ]
]
```

# Usage
Because the plugin is implemented as a Transport, you can use it from just about any layer of your CakePHP application.

## From a Controller

```php
// Configure email transport
Email::configTransport('sparkpost', [
    'className' => 'SparkPost.SparkPost',
    'apiKey' => Configure::read('SparkPost.Api.key')
]);

// Create email message
$email = new Email();
$email->transport('sparkpost');

$email->from(['FROM_ADDRESS' => 'FROM_NAME']);
$email->to(['TO_ADDRESS' => 'TO_NAME']);
$email->subject('SUBJECT');
$email->send('BODY');
```

## From the Cake CLI
Exactly the same process as sending from a controller, however you should additionally specify your hostname with
`$email->domain('www.example.com');` because CLI environments do not have hostnames.

# Testing
You can test the plugin using CakePHP's built-in support for PHPUnit, however this assumes two things:

 1. You have PHPUnit globally installed and the `phpunit` command available from the command-line
 2. You didn't delete the `phpunit.xml.dist` that is created with new CakePHP 3.x applications

To test the plugin, first add its test suites to the application's PHPUnit config file:

```xml
<?xml version="1.0" encoding="UTF-8"?>

<phpunit ...>
    ...
    <testsuites>
        <testsuite name="App Test Suite">
            <directory>./tests/TestCase</directory>
        </testsuite>

        <!-- Add these lines -->
        <testsuite name="SparkPost Plugin Test Suite">
            <directory>./vendor/syntaxera/cakephp-sparkpost-plugin/tests/TestCase</directory>
        </testsuite>
    </testsuites>
    ...
</phpunit>
```

Next, `cd` to your application's root directory and run `phpunit`.

If that doesn't work, run `composer dump-autoload` and try again.

# Contributing
## Bug Reports
If you find a bug in the plugin, or know of a missing feature you'd like to see added, feel free to create an issue on
the [Issues page](https://github.com/syntaxera/cakephp-sparkpost-plugin/issues). **When reporting a bug, be as
descriptive as possible. Include what you were doing when the bug occurred, what might have caused it to occur, and how
to reproduce it if possible. Screenshots are nice, too.**

As a matter of courtesy, please use the search tool to verify that an issue has not already been reported before
creating a new issue. Feel free to add your comments/+1's to an open bug/feature request, but duplicate issues take time
to prune and crowd out new, important issues.

## Pull Requests
Found a bug you think you can fix? By all means go ahead! Clone the affected branch, fix the bug, then create a pull
request and we'll review and accept it as quickly as we can. However, before submitting a pull request, please be sure
that your code follows our [Code Style Guidelines](http://syntaxera.io/pages/codestyle). PRs that don't follow the
style guidelines will be rejected! (politely, of course)

#### A Word on Forks...
At Syntax Era we believe that certain software should be free, and this plugin is definitely in that category. That's
why we've made this repository public, and why we allow contributions from the community. If you want to take this
project in a different direction, thenust fork the repo and develop away! **Because we have no control over the source
code in a fork, however, we cannot provide support for that software.** Use it at your own risk.

# Legal
## License
Licensed under the MIT License (MIT). See `LICENSE.md` for details.

## Copyright
Copyright (c) 2016 Syntax Era Development Studio. All rights reserved.
