# ChatGPT: AI-powered Auto-Reply Extension for Flarum

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/nodeloc/flarum-chatgpt.svg)](https://packagist.org/packages/nodeloc/flarum-chatgpt) [![Total Downloads](https://img.shields.io/packagist/dt/nodeloc/flarum-chatgpt.svg)](https://packagist.org/packages/datlechin/flarum-chatgpt)

A [Flarum](http://flarum.org) extension.

This extension is fork from https://github.com/datlechin/flarum-chatgpt . I just add some features.

The ChatGPT extension for Flarum includes an auto-reply discussion feature, customizable max tokens, and permission controls who can use this feature.

The auto-answer feature uses the text-davinci-003 model to generate quick and accurate responses to users' questions.

## Installation

This extension requierd **Flarum 1.7** and **PHP 8.1**.

Install with composer:

```sh
composer require datlechin/flarum-chatgpt:"*"
```

## Updating

```sh
composer update datlechin/flarum-chatgpt:"*"
php flarum migrate
php flarum cache:clear
```

## Roadmap

- [√] Add enable config in tag
- [×] Use title and post content as prompt
- [×] Add config for more keys for polling
- [×] Add queue work
- 
## Links

- [Packagist](https://packagist.org/packages/nodeloc/flarum-chatgpt)
- [GitHub](https://github.com/nodeloc/flarum-chatgpt)
- [Discuss](https://discuss.flarum.org/d/32535)
