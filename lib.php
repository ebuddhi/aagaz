<?php

$localhost = 'localhost';
$user = 'createwe_app';
$database = 'createwe_app';
$password="NGWECMxo{([l";

//setup db connection
$link = mysqli_connect("$localhost","$user","$password");
mysqli_select_db($link, "$database");
define('BASE_ADMIN_PATH', 'http://'.$_SERVER['SERVER_ADDR'].'/newspaper/administrator/');


//executes a given sql query with the params and returns an array as result
function query() {
  global $link;
  $debug = false;

  //get the sql query
  $args = func_get_args();
  $sql = array_shift($args);

  //secure the input
  for ($i=0;$i<count($args);$i++) {
    $args[$i] = urldecode($args[$i]);
    $args[$i] = mysqli_real_escape_string($link, $args[$i]);
  }

  //build the final query
  $sql = vsprintf($sql, $args);

  if ($debug) print $sql;

  //execute and fetch the results
  $result = mysqli_query($link, $sql);
  if (mysqli_errno($link)==0 && $result) {
    $rows = array();
    if ($result!==true)
    while ($d = mysqli_fetch_object($result)) {
      //array_push($rows,$d);
      $rows[] = $d;
    }
    //return json
    return $rows;

  } else {

    //error
    return array('error'=>'Database error');
  }
}

function query_count() {
  global $link;
  $debug = false;

  //get the sql query
  $args = func_get_args();
  $sql = array_shift($args);

  //secure the input
  for ($i=0;$i<count($args);$i++) {
    $args[$i] = urldecode($args[$i]);
    $args[$i] = mysqli_real_escape_string($link, $args[$i]);
  }

  //build the final query
  $sql = vsprintf($sql, $args);

  if ($debug) print $sql;

  //execute and fetch the results
  $result = mysqli_query($link, $sql);
  if (mysqli_errno($link)==0 && $result) {
    $rows = array();
    if ($result!==true)
    $rowcount=mysqli_num_rows($result);
    //return json
    return $rowcount;

  } else {

    //error
    return array('error'=>'Database error');
  }
}

function query_insert() {
  global $link;
  $debug = false;

  //get the sql query
  $args = func_get_args();
  $sql = array_shift($args);

  //secure the input
  for ($i=0;$i<count($args);$i++) {
    $args[$i] = urldecode($args[$i]);
    $args[$i] = mysqli_real_escape_string($link, $args[$i]);
  }

  //build the final query
  $sql = vsprintf($sql, $args);

  if ($debug) print $sql;

  //execute and fetch the results

  $result = mysqli_query($link, $sql);
  //echo mysqli_errno($link);
  if (mysqli_errno($link)==0 && $result) {

    $rows = array();

    if ($result!==true)
    while ($d = mysqli_fetch_assoc($result)) {
      array_push($rows,$d);
    }

    //return json
    return array('result'=>$rows);

  } else {

    //error
    return array('error'=>'Database error');
  }
}

function update() {
  global $link;
  $debug = false;

  //get the sql query
  $args = func_get_args();
  $sql = array_shift($args);

  //secure the input
  for ($i=0;$i<count($args);$i++) {
    $args[$i] = urldecode($args[$i]);
    $args[$i] = mysqli_real_escape_string($link, $args[$i]);
  }

  //build the final query
  $sql = vsprintf($sql, $args);

  if ($debug) print $sql;

  //execute and fetch the results

  $result = mysqli_query($link, $sql);
  //echo mysqli_errno($link);
  if (mysqli_errno($link)==0 && $result) {

    return array('result'=>$result);
  }
  else {
    //error
    return array('result'=>0, 'error'=>'Database error');
  }
}
//loads up the source image, resizes it and saves with -thumb in the file name
function thumb($srcFile, $sideInPx) {

  $image = imagecreatefromjpeg($srcFile);
  $width = imagesx($image);
  $height = imagesy($image);

  $thumb = imagecreatetruecolor($sideInPx, $sideInPx);

  imagecopyresized($thumb,$image,0,0,0,0,$sideInPx,$sideInPx,$width,$height);

  imagejpeg($thumb, str_replace(".jpg","-thumb.jpg",$srcFile), 85);

  imagedestroy($thumb);
  imagedestroy($image);
}

?>
