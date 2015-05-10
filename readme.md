# Kirby Webmentions Plugin

This Kirby plugin provides an easy way to add [webmentions](http://indiewebcamp.com/Webmention) to your site. [Download the plugin](https://github.com/bastianallgeier/kirby-webmentions/archive/master.zip) from Github and put it into /site/plugins. It will automatically be loaded by Kirby. 

To enable webmentions in a template, you can use the webmentions() helper:

```php
<?php echo webmentions() ?>
```

Additionally you have to specify your new webmention endpoint in the header of your site:

```
<link rel="webmention" href="<?php echo url('webmention') ?>">
```

Your site is now able to receive webmentions. 

## Author

Bastian Allgeier
<bastian@getkirby.com>