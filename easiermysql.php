<?php
/*

  easiermysql.php - Helper functions to make MySQL access smoother.

  Basically this script allows you to insert and update MySQL content
  using regular PHP arrays. Prefix your array keys with @ when you
  need to set the value with MySQL functions. You can either use this
  for plain query formatting, or for all database access with the API
  at the end of the file.

  Written by Johannes Lundberg (johannes.lundberg at gmail)
  Released to the public domain.

  Last update 2011-08-14.

*/


/*=============================================================
  Build an INSERT query for you. No more manual INSERTs.
  Also makes your code more safe with mysql_real_escape_string.

  Example:

  $data = array(
    'username' => $username,
    'password' => $password,
    '@created' => 'now()'
  );
  $query = genericinsert ("user", $data);
===============================================================*/

function genericinsert ($table, $data) {
  $names = '';
  $values = '';
  foreach ($data as $key => $value) {
    if (substr($key,0,1) == '@') {
      $names  .= substr($key,1).',';
      $values .= $value.',';
    } else {
      $names  .= $key.',';
      $values .= '"'.mysql_real_escape_string($value).'",';
    }
  }
  // INSERT INTO table (a,b,c) values (1,2,3)
  $query = 'INSERT INTO `'.mysql_real_escape_string($table).'` '.
           '('.substr($names ,0,strlen($names )-1).') values '.
           '('.substr($values,0,strlen($values)-1).')';
  return $query;
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
  $query = genericupdate ("user", "id", $data);
===============================================================*/

function genericupdate ($table, $primary, $data) {

  /* Support single and combined primary keys. */
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
      $set .= $key.'="'.mysql_real_escape_string($value).'",';
  }
  // UPDATE table SET a=1, b=2, c=3
  $query = 'UPDATE '.$table.' SET '.substr($set,0,strlen($set)-1).
           ' WHERE '.substr($where,0,strlen($where)-5);
  return $query;
}


/*=============================================================
  Return an array of all rows in a a MySQL result 
*/
function res2array($res) {
  if(!$res) return array();
  while($row=mysql_fetch_assoc($res)){
    $array[] = $row;
  }
  return $array;
}


/*=============================================================
  Return a keyed array of all rows in a a MySQL result 
*/
function res2keyarray($res,$key) {
  if(!$res) return array();
  while($row=mysql_fetch_assoc($res)){
    $array[$row[$key]] = $row;
  }
  return $array;
}



/*
  Simple yet fully real-world usable MySQL API below.
  
  If you need different behavior or extra features,
  change the code to your needs.
*/

function mConnect() {
  mysql_connect(
    $GLOBALS['mysql_hostname'],
    $GLOBALS['mysql_username'],
    $GLOBALS['mysql_password']
  );
  
  mysql_select_db ($GLOBALS['mysql_database']);
  mysql_set_charset('utf8');
}

function mClose() {
  mysql_close();
}

function mInsert ($table, $data) {
  $q = genericinsert($table, $data);
  mysql_query($q);
  return mysql_insert_id();
}

function mUpdate ($table, $primary, $data) {
  $q = genericupdate($table, $primary, $data);
  $res = mysql_query($q);
  return mysql_affected_rows();
}

/*
  Use this to easy and safely select rows from the database.
  Put @ for all variables you need in the query string, and then pass a list
  of all variables as the second argument. These values will be integrated
  into the query string using mysql_real_escape_string.
  
  mSelectRows( 'SELECT * FROM users WHERE name = @', array('John Doe') );
*/
function mSelectRows ($q, $escapelist = null) {
  $res = mysql_query( mEscape($q,$escapelist) );
  if (!$res || mysql_num_rows($res) <= 0) return null;
  
  return res2array($res);
}

/*
  Select a single row
*/
function mSelectOne ($q, $escapelist = null) {
  $res = mysql_query( mEscape($q,$escapelist) );
  if (!$res || mysql_num_rows($res) <= 0) return null;
  
  return mysql_fetch_assoc($res);
}

/*
  Generic query with @-escaping support.
*/
function mQuery ($q, $escapelist = null) {
  return mysql_query( mEscape($q,$escapelist) );
}

/*
  Delete with @-escaping and showing how many rows that was deleted.
*/
function mDelete ($q, $escapelist = null) {
  $res = mysql_query( mEscape($q,$escapelist) );
  return mysql_affected_rows();
}

function mEscape ($q, $escapelist = null) {
  while ($i = strpos($q,'@')) {
    $parts[] = substr($q,0,$i);
    $q = substr($q,$i+1);
    $parts[] = '"'. mysql_real_escape_string( array_shift($escapelist) ) .'"';
  }
  $parts[] = $q;
    
  return implode('',$parts);
}


?>
