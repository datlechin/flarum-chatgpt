# ChatGPT

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/datlechin/flarum-chatgpt.svg)](https://packagist.org/packages/datlechin/flarum-chatgpt) [![Total Downloads](https://img.shields.io/packagist/dt/datlechin/flarum-chatgpt.svg)](https://packagist.org/packages/datlechin/flarum-chatgpt)

A [Flarum](http://flarum.org) extension.

The ChatGPT extension for Flarum includes an auto-reply discussion feature, customizable max tokens, and permission controls who can use this feature.

The auto-answer feature uses the text-davinci-003 model to generate quick and accurate responses to users' questions.

## Installation

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

Currently only supports the `text-davinci-003` model, having the option to choose from other models in the future is an exciting prospect.

## Roadmap

- [ ] Add more models
- [ ] Select AI user used for auto-reply

## Links

- [Packagist](https://packagist.org/packages/datlechin/flarum-chatgpt)
- [GitHub](https://github.com/datlechin/flarum-chatgpt)
- [Discuss](https://discuss.flarum.org/d/)
