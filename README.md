# minimalnfc.php

Fast and limited Unicode normalizer written in pure PHP.

This converts strings containing ÅÄÖ where AAO has been separated from the ring and umlaut (so called decomposed unicode) into strings with a single character per letter (called precomposed unicode, or Normalization Form C).

For example, the letter:

61 CC 8A // LATIN CAPITAL LETTER A + COMBINING RING ABOVE (U+0041 + U+030A)

Becomes:

C3 85 // LATIN CAPITAL LETTER A WITH RING ABOVE (U+00C5)

Use minimalnfc.php/utf8_normalize() instead of Normalizer::normalize when you do not need full Unicode normalization and prefer something tiny with less dependancies.

The function operates directly on UTF-8 and replaces decomposed unicode characters found in regular latin text (basically ISO8859-1) with their precomposed counterpart using Normalization Form C.


# easiermysql.php

This script allows you to insert and update MySQL content using regular PHP arrays. Prefix your array keys with @ when you need to set the value with MySQL functions. You can either use this for plain query formatting, or preferably for all database access with the following API at the end of the file:

Connecting to a MySQL server
```php
$mysql_username = 'username';
$mysql_password = 'password';
$mysql_database = 'database';

mConnect();
```

Simple inserts
```php
$insert = array(
  'articleid' => 1234,
  'author' => 567,
  '@created', 'now()',
  'text' => 'Great advice! This will be a perfect match for my Raspberry Pi!'
);
mInsert('comments',$insert);
```

Easy updates
```php
$update = array(
  'id' => '1234',
  '@likes' => 'likes+1'
);
mUpdate('articles','id',$update);
```

Safe queries
```php
$articles = mSelectRows(
  'SELECT * FROM articles '.
  'WHERE category=@ AND author=@ ORDER BY created DESC',
  array($_GET['category'],$_GET['author'])
);
```
