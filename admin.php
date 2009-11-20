<?php
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
define('BB2_CWD', dirname(__FILE__));

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'admin.php');

require_once(BB2_CWD.'/bad-behavior/responses.inc.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_badbehaviour extends DokuWiki_Admin_Plugin {

    /**
     * return some info
     */
    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    /**
     * Access for managers allowed
     */
    function forAdminOnly(){
        return false;
    }

    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 140;
    }

    /**
     * handle user request
     */
    function handle() {
    }

    /**
     * output appropriate html
     */
    function html() {
        if($_REQUEST['lookup']){
            $this->_lookup($_REQUEST['lookup']);
        }else{
            $this->_stats();
        }

        echo $this->_lookupform();
    }

    function _stats(){
        print $this->plugin_locale_xhtml('stats');

        $days = 7;
        $list = $this->_readlines($days);

        $all = 0;
        $stats = array();
        foreach($list as $line){
            if(!$line) continue;
            $data = explode("\t",$line);
            $stats[$data[6]] = (int) $stats[$data[6]] + 1;
            $all++;
        }
        arsort($stats);

        printf('<p><b>'.$this->getLang('blocked').'</b></p>',$all,$days);

        echo '<table class="inline">';
        echo '<tr>';
        echo '<th>'.$this->getLang('percent').'</th>';
        echo '<th>'.$this->getLang('count').'</th>';
        echo '<th>'.$this->getLang('reason').'</th>';
        echo '</tr>';
        foreach($stats as $code => $count){
            $resp = bb2_get_response($code);
            echo '<tr>';
            echo '<td>';
            printf("%.2f%%",100*$count/$all);
            echo '</td>';
            echo '<td>';
            echo $count;
            echo '</td>';
            echo '<td>';
            echo $resp['log'];
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    function _lookup($key){
        global $ID;
        global $conf;
        global $lang;

        print $this->plugin_locale_xhtml('lookup');

        $code = str_replace('-','',$key);
        $ip   = hexdec(substr($code,0,2)).'.'.
                hexdec(substr($code,2,2)).'.'.
                hexdec(substr($code,4,2)).'.'.
                hexdec(substr($code,6,2));
        $code = substr($code,8);

        $resp = bb2_get_response($code);
        printf('<p>'.$this->getLang('lkpresult').'</p>',
               $ip,$resp['log'],$resp['explanation'],hsc($key));

        printf('<p>'.$this->getLang('lkplist').'</p>',7);

        $lines = preg_grep('/'.preg_quote($ip).'/',$this->_readlines());
        if(count($lines)){
            echo '<table class="inline">';
            foreach($lines as $line){
                $fields = explode("\t",$line);
                $resp = bb2_get_response($fields[6]);
                echo '<tr>';
                echo '<td>'.strftime($conf['dformat'],$fields[0]).'</td>';
                echo '<td>'.hsc($fields[1]).'</td>';
                echo '<td>'.hsc($fields[2]).'</td>';
                echo '<td>'.hsc($fields[3]).'</td>';
                echo '<td>'.hsc($fields[4]).'</td>';
                echo '<td>'.hsc($fields[5]).'</td>';
                echo '<td>'.$resp['log'].'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }else{
            echo '<p><i>'.$lang['nothingfound'].'</i></p>';
        }
    }

    function _lookupform(){
        global $lang;
        echo '<div>';
        echo '<form action="" method="get">';
        echo '<input type="hidden" name="do" value="admin" />';
        echo '<input type="hidden" name="page" value="badbehaviour" />';
        echo '<label for="key__lookup">'.$this->getLang('lookup').':</label> ';
        echo '<input type="text" id="key__lookup" name="lookup" value="'.hsc($_REQUEST['lookup']).'" />';
        echo '<input type="submit" value="'.$lang['btn_search'].'" class="button" />';
        echo '</form>';
        echo '</div>';
    }

    /**
     * Read loglines backward
     */
    function _readlines($days=7){
        global $conf;
        $file = $conf['cachedir'].'/badbehaviour.log';

        $date  = time() - ($days*24*60*60);

        $data  = array();
        $lines = array();
        $chunk_size = 8192;

        if (!@file_exists($file)) return $data;
        $fp = fopen($file, 'rb');
        if ($fp===false) return $data;

        //seek to end
        fseek($fp, 0, SEEK_END);
        $pos = ftell($fp);
        $chunk = '';

        while($pos){

            // how much to read? Set pointer
            if($pos > $chunk_size){
                $pos -= $chunk_size;
                $read = $chunk_size;
            }else{
                $read = $pos;
                $pos  = 0;
            }
            fseek($fp,$pos);

            $tmp = fread($fp,$read);
            if($tmp === false) break;
            $chunk = $tmp.$chunk;

            // now split the chunk
            $cparts = explode("\n",$chunk);

            // keep the first part in chunk (may be incomplete)
            if($pos) $chunk = array_shift($cparts);

            // no more parts available, read on
            if(!count($cparts)) continue;

            // put the new lines on the stack
            $lines = array_merge($cparts,$lines);

            // check date of first line:
            list($cdate) = explode("\t",$cparts[0]);
            if($cdate < $date) break; // we have enough
        }
        fclose($fp);

        return $lines;
    }
}
//Setup VIM: ex: et ts=4 enc=utf-8 :
