<?php

// Override configuration in config.local.php

// Enable logging to the console?
$config['log_console_enable'] = true;
// Enable logging to file specified in log_file_location?
$config['log_file_enable'] = true;
$config['log_file_location'] = __DIR__ . '/var/main.log';
// Time format to use in logs. Refer to: https://www.php.net/manual/en/datetime.format.php
$config['log_time_format'] = 'Y-m-d H:i:s.u O';
/*
 * Command used to get all processes related to Dropbox
 *
 * ps
 *     ax - list all processes
 *     c - change 'command' column to only executable name, instead of full path.
 *         This is to prevent killing any programs running from inside Dropbox directory.
 *     ww - do not truncate columns. Irrelevant when running not in a terminal,
 *          but helps when copying command to terminal for debugging
 *     o - output the following columns
 *
 * grep
 *     i - case insensitive
 *     %CPU - include 'ps' header
 *     dropbox - main Dropbox processes
 *     garcon - 'garcon' processes are "Dropbox Finder Extension"
 */
$config['process_list_command'] = 'ps axcwwo pid,state,time,%cpu,%mem,command | grep -i "%CPU\|dropbox\|garcon"';
// Regex to explode 'ps' command output to separate parts
$config['process_explode_regex'] = '/\s+/';
// Should be equal to amount of columns returned by 'ps' command. Allows after
// exploding to join multiple last parts into one, to avoid exploding process
// name that contains spaces
$config['process_explode_limit'] = 6;
// If usage is above this threshold, process will be considered unresponsive
$config['cpu_usage_threshold'] = 70;
// Number of measurements to take when the first one exceeds 'cpu_usage_threshold',
// to ensure it wasn't a single spike
$config['measurement_count'] = 10;
// Interval in seconds between each measurement
$config['measurement_interval'] = 1;
// Number of seconds to sleep after killing unresponsive process before starting new one
$config['sleep_before_restart'] = 10;
// Command to restart Dropbox after killing when it's unresponsive
$config['dropbox_restart_command'] = 'open -g /Applications/Dropbox.app';
