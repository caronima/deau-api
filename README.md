# deAU クラウド法人情報 API

This is the development repository for deAU Cloud Corporate Information API, a WordPress plugin that gets Japanese corporate data with API of deAU Cloud Corporate Information. You can also download the plugin package installation from the [WordPress.org Plugin Directory](https://wordpress.org/plugins/deau-api/).

Contributors: caronima, takashimatsuyama  
Donate link:  
Tags: seo, scheme, japanese, corporate, about, api  
Requires at least: 4.8  
Tested up to: 6.6  
Requires PHP: 5.4.0  
Stable tag: 1.0.3  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  


Web APIから法人データを取得してWordPressテーマで利用。


## Description

このプラグインは、Webアプリ「deAU クラウド法人情報」のAPIから法人データを取得してWordPressテーマ上で利用することができます。
deAU クラウド法人情報は日本国内の法人情報を検索し閲覧や編集、共有、外部連携することができるWebアプリです。
プラグインの設定画面から法人番号を指定することでWebアプリのAPIから法人データを取得することができます。

## Installation

1. deau-api フォルダ全体を `/wp-content/plugins/` ディレクトリにアップロードします。
2. WordPress の **プラグイン** メニューから有効化してください。
3. WordPress 管理画面に **deAU API** メニューが現れるでしょう。
4. **deAU API**の設定画面から**日本の法人番号**を指定することでWebアプリのAPIから日本国内の法人データを取得することができます。

## Changelog

### 1.03
Tested on WordPress 6.6 and fixed function.php.

### 1.0.2
Tested on WordPress 6.0.

### 1.0.1
Fixed PHP 8.0 warning.

### 1.0.0
Initial release.