<?php
  include_once('lib.php');
  include_once("JSON.php");
  $jobj = new Services_JSON();

  extract($_REQUEST);

  function errorJson($msg){
    print json_encode(array('success' => 0, 'error'=>$msg));
    exit();
  }
  function okJson($msg){
    print json_encode(array('status'=>$msg));
    exit();
  }
  if(!isset($action)){
    $action = '';
  }

switch($action){
  case 'get_users_status':
    get_users_status();
  break;

  case 'get_users_info':
    get_users_info();
  break;

  case 'users':
    $token = 'users';
    get_all($token);
  break;

  case 'add_user':
    add_user();
    break;
  case 'update_user':
    update_user();
    break;
  case 'add_group':
    add_group();
    break;


  #default:
  //$token = 'users';
  //Default Test Function
  #get_all('groups');
  #break;
}

function get_all($option = null) {
  global $jobj;
  switch($option){
    case 'users':
      $tbl_name="`users` u";
      $where = '';
      $sqlusers = "SELECT u.`id`, u.`name`  FROM $tbl_name ".$where;
      $result = query($sqlusers);
        if (count($result)>0) {
          $response['data'] = $result;
          $response['success'] = 1;
          echo $jobj->encode($response);
        }
        else{
        errorJson('Authorization failed');
        }
      break;
    case 'groups':
      $sqlusers = "SELECT * FROM groups ";
      $result = query($sqlusers);
        if (count($result)>0) {
          $response['data'] = $result;
          $response['success'] = 1;
          echo $jobj->encode($response);
        }
        else{
        errorJson('Authorization failed');
        }
      break;
  }
}

function add_user(){
  global  $_REQUEST, $jobj;

  $jdata = json_decode($_REQUEST['json']);
  $device_id = $jdata->device_id;
  $name = $jdata->name;
  $check_existing_id = check_existing_id($device_id);
  if ($check_existing_id ) {
    $insert = "INSERT INTO `users` (`id`, `name`, `device_id`, `avatar`) VALUES (NULL, '$name', '$device_id', 'missing.png');";
    $result = query_insert($insert);
    if (!isset($result['error'])) {
      //registration is susccessfull
      $result = array(
        'success'=> 1,
        'data' => array(
          'msg'=> 'Registration susccessfull'
        )
      );
      echo $jobj->encode($result);
    }
    else {
      //for some database reason the registration is unsuccessfull
      errorJson('Registration failed');
    }
  }
  else {
    errorJson('Registration failed');
  }
}

function get_users_info() {
  global  $_REQUEST,$site_url, $jobj;

  $jdata = json_decode($_REQUEST['json']);
  $device_id = $jdata->device_id;

  $result['user']= get_users_by_id($device_id);

  if (count($result['user'])>0) {
    $response['data'] = $result;
    $response['success'] = 1;
    echo $jobj->encode($response);
  }
  else{
    echo $jobj->encode(array('status' => 0, 'error' => 'usersname not exist'));
  }
}



/* -------- users Related Functions ------------------- */

// return 1 if user not exists
function check_existing_id($device_id){
  $sql="SELECT * FROM users WHERE device_id='$device_id'";
  $result=query($sql);
  if (isset($result[0]->id)) {
    return 0;
  }
  else {
    return 1;
  }
}

// return user id
function get_user_id($device_id){
  $sql="SELECT id FROM users WHERE device_id='$device_id'";
  $result=query($sql);
  if (isset($result[0]->id)) {
    return $result[0]->id;
  }
}

// return user
function get_users_by_id($device_id){
  global  $_REQUEST, $site_url, $jobj;
  $sql="SELECT * FROM users WHERE device_id='$device_id'";
  $result=query($sql);
  if (count($result)>0) {
    $result['groups']= get_users_groups_by_id($device_id);
    return $result;
  }
}

// return group count
function get_users_group_count_by_id($device_id){
  global  $_REQUEST,$site_url, $jobj;
  $uid = get_user_id($device_id);
  $sql="SELECT * FROM `groups` WHERE `user_id` = '$uid'";
  $result=query_count($sql);
  return $result;
}



// return groups created by user
function get_users_groups_by_id($device_id){
  global  $_REQUEST,$site_url, $jobj;
  $uid = get_user_id($device_id);
  $sql="SELECT * FROM `groups` WHERE `user_id` = '$uid'";
  $result=query($sql);
  if (count($result)>0) {
    $result['count'] = count($result);
    return $result;
  }
}

// Update user record
function update_user(){
  global  $_REQUEST, $jobj;

  $jdata = json_decode($_REQUEST['json']);
  $device_id = $jdata->device_id;
  $name = $jdata->name;
  $new_users = check_existing_id($device_id);
    if(!$new_users){
       $update = "UPDATE `users` SET
        `name` = '".$name."'
        WHERE `device_id` = '".$device_id."' ";
    $result = query_insert($update);
    if (!isset($result['error'])) {
      //registration is susccessfull
      $result = array(
        'success'=> 1,
        'data' => array(
          'msg'=> 'Record updated susccessfull'
        )
      );
      echo $jobj->encode($result);
    }
    else {
      //for some database reason the registration is unsuccessfull
      errorJson('Record updation failed');
    }
  }
  else {
    errorJson('Record updation failed');
  }
}

###############################################
################# Group section ###############
###############################################

function add_group(){
  global  $_REQUEST, $jobj;

  $jdata = json_decode($_REQUEST['json']);
  $device_id = $jdata->device_id;
  $name = $jdata->name;
  $check_existing_id = check_existing_id($device_id);
  if ($check_existing_id == 0 ) {
    $user_id = get_user_id($device_id);

    $insert = "INSERT INTO `groups` (`id`, `user_id`, `name`) VALUES (NULL, '$user_id', '$name');";
    $result = query_insert($insert);
    if (!isset($result['error'])) {
      //registration is susccessfull
      $result = array(
        'success'=> 1,
        'data' => array(
          'msg'=> 'Group added susccessfully'
        )
      );
      echo $jobj->encode($result);
    }
    else {
      //for some database reason the registration is unsuccessfull
      errorJson('Group creation failed');
    }
  }
  else {
    errorJson('Group creation failed');
  }
}

function get_all_user_groups() {
  global  $_REQUEST,$site_url, $jobj;

  $jdata = json_decode($_REQUEST['json']);
  $device_id = $jdata->device_id;

  $result['user']= get_users_by_id($device_id);

  if (count($result['user'])>0) {
    $response['data'] = $result;
    $response['success'] = 1;
    echo $jobj->encode($response);
  }
  else{
    echo $jobj->encode(array('status' => 0, 'error' => 'usersname not exist'));
  }
}
