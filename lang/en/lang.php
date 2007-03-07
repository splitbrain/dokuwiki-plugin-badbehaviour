<?php
/**
 * english language file
 */

// settings must be present and set appropriately for the language
$lang['encoding']   = 'utf-8';
$lang['direction']  = 'ltr';

// for admin plugins, the menu prompt to be displayed in the admin menu
// if set here, the plugin doesn't need to override the getMenuText() method
$lang['menu'] = 'Bad Behaviour Statistics';

// custom language strings for the plugin

$lang['percent'] = 'Percent';
$lang['count']   = 'Count';
$lang['reason']  = 'Reason';

$lang['blocked'] = '%d accesses were blocked in the last %d days.';

$lang['lkpresult'] = 'The request came from IP <b>%s</b> and was blocked because <b>%s</b>. The explanation shown to the user was <b>%s</b> More details may be available <a href="http://www.ioerror.us/bb2-support-key?key=%s">here</a>.';
$lang['lkplist']   = 'Below is a list of log lines matching this IP in the last %d days.';
$lang['lookup']    = 'Lookup support key';

//Setup VIM: ex: et ts=4 enc=utf-8 :
