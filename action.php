<?php
/**
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
define('BB2_CWD', dirname(__FILE__));

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_badbehaviour extends DokuWiki_Action_Plugin {

    /**
     * return some info
     */
    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    /**
     * register the eventhandlers and initialize some options
     */
    function register(&$controller){

        $controller->register_hook('DOKUWIKI_STARTED',
                                   'BEFORE',
                                   $this,
                                   'handle_start',
                                   array());

        $controller->register_hook('TPL_METAHEADER_OUTPUT',
                                   'BEFORE',
                                   $this,
                                   'handle_metaheaders',
                                   array());
    }

    /**
     * Do the magic
     */
    function handle_start(&$event, $param){
        require_once(BB2_CWD . "/bad-behavior/version.inc.php");
        require_once(BB2_CWD . "/bad-behavior/core.inc.php");

        bb2_start(array( 'log_table'     => 'badbehaviour',
                         'display_stats' => true,
                         'strict'        => false,
                         'verbose'       => false,
                         'skipblackhole' => $this->getConf('skipblackhole')
                 ));
    }

    /**
     * Extend the meta headers
     */
    function handle_metaheaders(&$event, $param){
        global $bb2_javascript;
        if (!$bb2_javascript) return;

        // we just use some values from the bb2 script but build our own (better) version

        preg_match('/(bb2_addLoadEvent\(function\(\) \{)(.*)(\}\);)/s',$bb2_javascript,$match);
        $data = 'addInitEvent(function(){'.$match[2].'});';

        $event->data['script'][] = array( 'type'=>'text/javascript', 'charset'=>'utf-8', '_data'=>$data);
    }

}

/*
 * Bad Behavior expects certain global functions. Mostly related to the DB logging,
 * which is not used in DokuWiki
 */

function bb2_relative_path() { return DOKU_BASE; }
function bb2_db_date() { return gmdate('Y-m-d H:i:s'); }
function bb2_db_affected_rows() { return false; }
function bb2_db_escape($string) { return $string; }
function bb2_db_num_rows($result) { return ($result === FALSE) ? 0 : count($result); }
function bb2_db_query($query) { return false; }
function bb2_db_rows($result) { return $result; }
function bb2_email() {
    $bb2 =& plugin_load('action','badbehaviour');
    return $bb2->getConf('email');
}

/**
 * This is an extension hook provided by BB2, we use it to do our
 * own logging.
 */
function bb2_banned_callback($settings, $package, $key){
    global $conf;

    $data = array();
    $data[] = time();
    $data[] = stripctl($package['ip']);
    $data[] = stripctl($package['request_method']);
    $data[] = stripctl($package['request_uri']);
    $data[] = stripctl($package['server_protocol']);
    $data[] = stripctl($package['user_agent']);
    $data[] = stripctl($key);

    io_saveFile($conf['cachedir'].'/badbehaviour.log',join("\t",$data)."\n",true);
}

