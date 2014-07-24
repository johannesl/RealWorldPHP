# easiermysql.php

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
