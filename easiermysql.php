<?php
/*

  easiermysql.php - Helper functions to make MySQL / MariaDB access smoother.

  These functions enable you to insert and update MySQL content using regular
  PHP arrays. Prefix your array key with @ if you want to set raw values,
  such as when using MySQL functions.

  Updated to use mysqli and work with PHP7.

  Written by Johannes Ridderstedt (johannesl@46elks.com)
  Released to the public domain.

  Last update 2017-07-16.

*/


/*=============================================================
  Build an INSERT query & run it. No more manual INSERTs.
  Also makes your code more safe with mysqli_escape_string.

  Example:

  $insert = array(
    'username' => $username,
    'password' => $password,
    '@created' => 'now()'
  );
  $userid = mInsert ("user", $insert);
===============================================================*/

function mInsert ($table, $data) {
  global $mysql_link;
  $names = '';
  $values = '';
  foreach ($data as $key => $value) {
    if (substr($key,0,1) == '@') {
      $names  .= substr($key,1).',';
      $values .= $value.',';
    } else {
      $names  .= $key.',';
      $values .= '"'.mysqli_escape_string($mysql_link,$value).'",';
    }
  }
  // INSERT INTO table (a,b,c) values (1,2,3)
  $query = 'INSERT INTO `'.mysqli_escape_string($mysql_link,$table).'` '.
           '('.substr($names ,0,strlen($names )-1).') values '.
           '('.substr($values,0,strlen($values)-1).')';
  mysqli_query($mysql_link,$query);
  return mysqli_insert_id($mysql_link);
}


/*=============================================================
  Build an UPDATE query for you.

  Example:

  $data = array(
    'id'       => $_POST[id],
    'username' => $_SESSION[username],
    'password' => $_POST[newpassword],
    '@updated' => 'now()'
  );
  $query = mUpdate ("user", "id", $data);
===============================================================*/

function mUpdate ($table, $primary, $data) {
  global $mysql_link;

  /* Support both single and combined primary keys. */
  if(gettype($primary)=='string'){
    $primary=array($primary);
  }
  $where = '';
  foreach ($primary as $k) {
    $where .= $k.'="'.$data[$k].'" AND ';
    unset($data[$k]);
  }
  $set = '';
  foreach ($data as $key => $value) {
    foreach ($primary as $k) {
      if($key != $k) continue; }
    if (substr($key,0,1) == '@')
      $set .= substr($key,1).'='.$value.',';
    else
      $set .= $key.'="'.mysqli_escape_string($mysql_link,$value).'",';
  }
  // UPDATE table SET a=1, b=2, c=3
  $query = 'UPDATE '.$table.' SET '.substr($set,0,strlen($set)-1).
           ' WHERE '.substr($where,0,strlen($where)-5);
  $res = mysqli_query($mysql_link,$query);
  return mysqli_affected_rows();
}


/*=============================================================
  Return an array of all rows in a MySQL result.
*/
function res2array($res) {
  if(!$res) return array();
  while($row=mysqli_fetch_assoc($res)){
    $array[] = $row;
  }
  return $array;
}


/*=============================================================
  Return a keyed array of all rows in a MySQL result.
*/
function res2keyarray($res,$key) {
  if(!$res) return array();
  while($row=mysqli_fetch_assoc($res)){
    $array[$row[$key]] = $row;
  }
  return $array;
}

function mConnect() {
  global $mysql_link;

  if (isset($GLOBALS['mysql_port']))
    $mysql_link = mysqli_connect(
      $GLOBALS['mysql_hostname'],
      $GLOBALS['mysql_username'],
      $GLOBALS['mysql_password'],
      $GLOBALS['mysql_database'],
      $GLOBALS['mysql_port']
    );
  else
    $mysql_link = mysqli_connect(
      $GLOBALS['mysql_hostname'],
      $GLOBALS['mysql_username'],
      $GLOBALS['mysql_password'],
      $GLOBALS['mysql_database']
    );
  
  mysqli_set_charset($mysql_link,'utf8');
}

function mClose() {
  global $mysql_link;
  mysqli_close($mysql_link);
}

/*
  Use this to easy and safely select rows from the database.
  Put @ for all variables you need in the query string, and then pass a list
  of all variables as the second argument. These values will be integrated
  into the query string using mysqli_escape_string.
  
  mSelectRows( 'SELECT * FROM users WHERE name = @', array('John Doe') );
*/
function mSelectRows ($q, $escapelist = null) {
  global $mysql_link;
  $res = mysqli_query( $mysql_link, mEscape($q,$escapelist) );
  if (!$res || $res->num_rows <= 0) return null;

  return res2array($res);
}

/*
  Select a single row.
*/
function mSelectOne ($q, $escapelist = null) {
  global $mysql_link;
  $res = mysqli_query( $mysql_link, mEscape($q,$escapelist) );
  if (!$res || $res->num_rows <= 0) return null;

  return mysqli_fetch_assoc($res);
}

/*
  Generic query with @-escaping support.
*/
function mQuery ($q, $escapelist = null) {
  global $mysql_link;
  return mysqli_query( $mysql_link, mEscape($q,$escapelist) );
}

/*
  Delete with @-escaping and showing how many rows that were deleted.
*/
function mDelete ($q, $escapelist = null) {
  global $mysql_link;
  $res = mysqli_query( mEscape($mysql_link,$q,$escapelist) );
  return mysqli_affected_rows();
}

function mEscape ($q, $escapelist = null) {
  global $mysql_link;
  while ($i = strpos($q,'@')) {
    $parts[] = substr($q,0,$i);
    $q = substr($q,$i+1);
    $parts[] = '"'. mysqli_escape_string( $mysql_link, array_shift($escapelist) ) .'"';
  }
  $parts[] = $q;
    
  return implode('',$parts);
}

/*
  A simple yet fully real-world usable and battle tested MySQL API. If you
  need different behavior or extra features, change the code to your needs.
*/

?>
