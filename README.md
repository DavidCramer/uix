# UIX2

UIX is a small framework for creating user interfaces ( Post Types, Settings Pages, and Metaboxes ) and config structures with the least code possible. It only handles the UI. The program logic is up to you.

## Documentation

Important note is that UIX used namespacing so it is PHP 5.3+

### Installation

There are three ways to use UIX; Include, Composer & Grunt.

#### Include

Simply add it to a uix folder in the root of your plugin and include the `uix-bootstrap.php`
and include it in your main plguin file like below:
```
require_once( 'uix/uix-bootstrap.php' );
```

#### Composer

Using composer, add the following to require property of your composer.json file: `"desertsnowman/uix": "dev-master"`
then run `$ composer install`

In your main plugin file include the composer autoloader: `require_once( 'vendor/autoload.php' );`

#### Grunt

The problem with both the include and composer is versioning. The Grunt installer overcomes this by "installing" the library under your plugins namespace.
Get the [UIX-WP starter plugin]( https://github.com/Desertsnowman/uix-wp ) and copy it a folder in your plugins directory.
Edit the `package.json` file with the details of your plugin. Pay close attention to the `namespace` and the `prefix` as these are very important.

Once thats done, run `npm install` and wait. The latest version will be pulled from this repo, and the `uix2` namespace rewritten under your own plugin.
So theres no chance of having a clashing version

Then simply go to WordPress admin and activate the plugin.

## Registration

UIX can auto load UI structures from a defined UI folder. This means that you don't need ever write registraion code to make stuff happen. ( although you can if you want ).



###