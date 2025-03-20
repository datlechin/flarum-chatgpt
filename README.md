# ChatGPT: AI-powered Auto-Reply Extension for Flarum
增加自定义第三方api的支持, 安装原版后,用本仓库的文件进行覆盖操作
插件目录vender\datlechin\flarum-chatgpt
![](https://private-user-images.githubusercontent.com/90635150/425006174-aa99612f-2f1c-4532-85f5-9b26a0ac729f.png?jwt=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3NDI0NzgzNjgsIm5iZiI6MTc0MjQ3ODA2OCwicGF0aCI6Ii85MDYzNTE1MC80MjUwMDYxNzQtYWE5OTYxMmYtMmYxYy00NTMyLTg1ZjUtOWIyNmEwYWM3MjlmLnBuZz9YLUFtei1BbGdvcml0aG09QVdTNC1ITUFDLVNIQTI1NiZYLUFtei1DcmVkZW50aWFsPUFLSUFWQ09EWUxTQTUzUFFLNFpBJTJGMjAyNTAzMjAlMkZ1cy1lYXN0LTElMkZzMyUyRmF3czRfcmVxdWVzdCZYLUFtei1EYXRlPTIwMjUwMzIwVDEzNDEwOFomWC1BbXotRXhwaXJlcz0zMDAmWC1BbXotU2lnbmF0dXJlPTQ3MDNkYjFmZDA0MmFjYmRjODhlYjBkNzc2NTMzYTBiM2FlNDRjMWJkZjA5NDFhY2U0NTliNDg1MjM1ZGFlYjAmWC1BbXotU2lnbmVkSGVhZGVycz1ob3N0In0.LzrMPvGptg8rP_S6tHLGwKuGysIwyhNx2Ur70JFya9k)
![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/datlechin/flarum-chatgpt.svg)](https://packagist.org/packages/datlechin/flarum-chatgpt) [![Total Downloads](https://img.shields.io/packagist/dt/datlechin/flarum-chatgpt.svg)](https://packagist.org/packages/datlechin/flarum-chatgpt)

A [Flarum](http://flarum.org) extension.

The ChatGPT extension for Flarum includes an auto-reply discussion feature, customizable max tokens, and permission controls who can use this feature.

The auto-answer feature uses the text-davinci-003 model to generate quick and accurate responses to users' questions.

![](https://user-images.githubusercontent.com/56961917/224526200-4aee65bf-59df-4892-b23d-aab644238101.gif)

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

Currently only supports the `text-davinci-003` model, having the option to choose from other models in the future is an exciting prospect.

## Roadmap

- [ ] Add more models
- [ ] Select AI user used for auto-reply

## Links

- [Packagist](https://packagist.org/packages/datlechin/flarum-chatgpt)
- [GitHub](https://github.com/datlechin/flarum-chatgpt)
- [Discuss](https://discuss.flarum.org/d/32535)
