<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 4/20/2019
 * Time: 15:06
 */


/**
 * tonic v3.0
 *
 * Lightweight PHP templating engine
 *
 * @author Ricardo Gamba <rgamba@gmail.com>
 * @license BSD 3-Clause License
 */
namespace Core\tpl\tonic;
use Core\config\Config;
use Nette\Utils\Strings;

class Tonic{

    public $themes_dir = '';
    /**
     * Enable context awareness
     */
    public static $context_aware = true;
    /**
     * Local timezone (to use with toLocal() modifier)
     */
    public static $local_tz = 'GMT';
    /**
     * Include path
     */
    public static $root='';
    /**
     * Enable template caching
     */
    public static $enable_content_cache = false;
    /**
     * Caching directory (must have write permissions)
     */
    public static $cache_dir = "cache/";
    /**
     * Cache files lifetime
     */
    public $cache_lifetime = 120; //86400
    /**
     * Default extension for includes
     */
    public $default_extension='.html';
    private $file;
    private $assigned=array();
    private $output="";
    private $source;
    private $content;
    private $or_content;
    private $is_php = false;
    private $cur_context = null;
    private static $modifiers = null;
    private static $globals=array();
    private $blocks = array();
    private $blocks_override = array();
    private $base = null;
    /**
     * Object constructor
     * @param $file template file to load
     */
    public function __construct($file=NULL){
        self::initModifiers();
        if($file !== null){
            $this->file=$file;
            $this->load();
        }
    }

    public function set_themes_dir($dir){
        $this->themes_dir = $dir;
    }
    /**
     * Create a new custom modifier
     * @param Name of the modifier
     * @param Lambda function, modifier function
     */
    public static function extendModifier($name, $func){
        if(!empty(self::$modifiers[$name]))
            return false;
        if(!is_callable($func))
            return false;
        self::$modifiers[$name] = $func;
        return true;
    }
    /**
     * Set the global environment variables for all templates
     * @param associative array with the global variables
     */
    public static function setGlobals($g=array()){
        if(!is_array($g))
            return false;
        self::$globals=$g;
    }
    /**
     * Load the desired template
     * @param <type> $file
     * @return <type>
     */
    public function load($file=NULL){
        if($file!=NULL)
            $this->file=$file;
        if(empty($this->file)) return false;
        $ext = explode('.',$file);
        $ext = $ext[count($ext)-1];
        if($ext === "php"){
            $this->is_php = true;
        }else{
            if(!file_exists(self::$root . $this->file)) {
                echo "<span style=\"display: inline-block; background: red; color: white; padding: 2px 8px; border-radius: 10px; font-family: 'Lucida Console', Monaco, monospace, sans-serif; font-size: 80%\"><b>tonic</b>: unable to load file '".self::$root . $this->file."'</span>";
                return false;
            }
            $this->source=file_get_contents(self::$root . $this->file);
            $this->content=&$this->source;
        }
        $this->or_content = $this->content;
        return $this;
    }
    /**
     * Load from string instead of file
     *
     * @param mixed $str
     */
    public function loadFromString($str){
        $this->source=$str;
        $this->content=&$this->source;
        return $this;
    }
    /**
     * Assign value to a variable inside the template
     * @param <type> $var
     * @param <type> $val
     */
    public function assign($var,$val){
        $this->assigned[$var]=$val;
        return $this;
    }
    public function getContext(){
        return $this->assigned;
    }
    /**
     * Magic method alias for self::assign
     * @param <type> $k
     * @param <type> $v
     */
    public function __set($k,$v){
        $this->assign($k,$v);
    }

    public function __isset($k){

    }
    /**
     * Assign multiple variables at once
     * This method should always receive get_defined_vars()
     * as the first argument
     * @param <type> get_defined_vars()
     * @return <type>
     */
    public function setContext($vars){
        if(!is_array($vars))
            return false;
        foreach($vars as $k => $v){
            $this->assign($k,$v);
        }
        return $this;
    }

    /**
     * Return compiled template
     * @return string
     * @throws \Exception
     */
    public function render($replace_cache=false){
        if($replace_cache)
            if(file_exists(self::$cache_dir.sha1($this->file)))
                unlink(self::$cache_dir.sha1($this->file));
        if(!$this->is_php){
            if(!$this->getFromCache()){
                $this->assignGlobals();
                $this->handleExtends();
                $this->handleBlockMacros();
                $this->handleBlocks();
                $this->handleIncludes();
                $this->handleIfMacros();
                $this->handleLoopMacros();
                $this->handleLoops();
                $this->handleIfs();
                $this->handleVar();
                $this->handleVars();
                $this->compile();
            }
        }else{
            $this->renderPhp();
        }
        if($this->base !== null) {
            // This template has inheritance
            $parent = new Tonic($this->base);
            $parent->setContext($this->assigned);
            $parent->overrideBlocks($this->blocks);
            return $parent->render();
        }
        return $this->output;
    }
    /**
     * For internal use only for template inheritance.
     */
    public function overrideBlocks($blocks) {
        $this->blocks_override = $blocks;
    }
    /**
     * Backwards compatibility for cache.
     */
    public function __get($var) {
        switch($var) {
            case 'enable_content_cache':
                // Backwards compatibility support
                return self::$enable_content_cache;
                break;
            case 'cache_dir':
                return self::$cache_dir;
                break;
            default:
                throw new \Exception("Tried to access invalid property " . $var);
        }
    }

    private function getFromCache(){
        if(self::$enable_content_cache!==true || !file_exists(self::$cache_dir.sha1($this->file))) {
            return false;
        }
        $file_expiration = filemtime(self::$cache_dir.sha1($this->file)) + (int)$this->cache_lifetime;

        if($file_expiration < time()){
            unlink(self::$cache_dir.sha1($this->file));
            return false;
        }

        $this->assignGlobals();
        foreach($this->assigned as $var => $val)
            ${$var}=$val;
        ob_start();
        include_once self::$cache_dir.sha1($this->file);
        $this->output=ob_get_clean();
        return true;
    }
    private function renderPhp(){
        $this->assignGlobals();
        if(!file_exists($this->file))
            die("TemplateEngine::renderPhp() - File not found (".$this->file.")");
        ob_start();
        Sys::get('module_controller')->includeView($this->file,$this->assigned);
        $this->output=ob_get_clean();
        return true;
    }
    private function assignGlobals(){
        self::$globals['__func'] = null;
        $this->setContext(self::$globals);
    }

    private function compile(){
        foreach($this->assigned as $var => $val){
            ${$var}=$val;
        }
        if(self::$enable_content_cache===true){
            $this->saveCache();
        }
        ob_start();
        $e=eval('?>'.$this->content);
        $this->output=ob_get_clean();
        if($e===false){
            die("Error: ".$this->output." <hr />".$this->content);
        }
    }


    private function saveCache(){
        $file_name=sha1($this->file);
        $cache=@fopen(self::$cache_dir.$file_name, 'wb');
        @fwrite($cache,$this->content);
        @fclose($cache);
    }
    private function removeWhiteSpaces($str) {
        $in = false;
        $escaped = false;
        $ws_string = "";
        for($i = 0; $i <= strlen($str)-1; $i++) {
            $char = substr($str,$i,1);
            $je = false;
            $continue = false;
            switch($char) {
                case '\\':
                    $je = true;
                    $escaped = true;
                    break;
                case '"':
                    if(!$escaped) {
                        $in = !$in;
                    }
                    break;
                case " ":
                    if(!$in) {
                        $continue = true;
                    }
                    break;
            }
            if (!$je) {
                $escaped = false;
            }
            if(!$continue) {
                $ws_string .= $char;
            }
        }
        return $ws_string;
    }
    private function handleIncludes(){
        $matches=array();
        preg_match_all('/\{\s*include\s*(.+?)\s*}/',$this->content,$matches);
        if(!empty($matches)){
            foreach($matches[1] as $i => $include){
                $include=trim($include);
                $include=explode(',',$include);
                $params=array();
                if(count($include)>1){
                    $inc=$include[0];
                    unset($include[0]);
                    foreach($include as $kv){
                        @list($key,$val)=@explode('=',$kv);
                        $params[$key]=empty($val) ? true : $val;
                    }
                    $include=$inc;
                }else
                    $include = $include[0];
                if (strpos($include, 'http') === 0) {
                    $rep = file_get_contents($include);
                } else {

                    ob_start();
                    if($this->themes_dir !== ''){

                      //  $rep = file_get_contents($this->themes_dir.'/'.$include);

                        $rest = fopen($this->themes_dir.'/'.$include, 'rb');
                        $rep = fread($rest,filesize($this->themes_dir.'/'.$include));
                           fclose($rest);

                    }else {
                        $inc = new Tonic($include);
                        $inc->setContext($this->assigned);
                        try {
                            $rep = $inc->render();
                        } catch (\Exception $e) {
                        }
                        $err = ob_get_clean();
                        if (!empty($err))
                            $rep = $err;
                    }
                }
                $this->content=str_replace($matches[0][$i],$rep,$this->content);
            }
        }
    }

    /**
     * @param $params
     * @return array
     */
    private function getParams($params): array
    {
        $i=0;
        $p=array();
        $escaped=false;
        $in_str=false;
        $act="";
        while($i<strlen($params)){
            $char=substr($params,$i,1);
            $i++;
            switch($char){
                case "\\":
                    if($escaped==true){
                        $escaped=false;
                        $act.=$char;
                    }else
                        $escaped=true;
                    break;
                case '"':
                    if($escaped==true){
                        $act.=$char;
                        break;
                    }
                    $in_str=($in_str==false ? true : false);
                    break;
                case ',':
                    if($in_str==true){
                        $act.=$char;
                        break;
                    }
                    $p[]=$act;
                    $act="";
                    break;
                default:
                    $escaped=false;
                    $act.=$char;
                    break;
            }
        }
        $p[]=$act;
        return $p;
    }
    private static function callModifier() {

        $args = func_get_args();
        if(empty($args[0])){
            return "[empty modifier]";
        }
        if(empty(self::$modifiers[$args[0]])){
            return "[invalid modifier '$args[0]']";
        }
        try {
            $ret = call_user_func_array(self::$modifiers[$args[0]],array_slice($args,1));
        } catch(\Exception $e){
            throw new \Exception("<span style=\"display: inline-block; background: red; color: white; padding: 2px 8px; border-radius: 10px; font-family: 'Lucida Console', Monaco, monospace, sans-serif; font-size: 80%\"><b>$args[0]</b>: ".$e->getMessage()."</span>");
        }
        return $ret;
    }
    private function applyModifiers(&$var,$mod,$match = ""){
        $context = null;
        $mods = $mod;
        if(self::$context_aware == true) {
            if(!empty($match) && !in_array("ignoreContext()", $mod, true)) {
                $context = $this->getVarContext($match, $this->cur_context);
                switch($context["tag"]){
                    default:
                        if($context['in_tag']){
                            $mod[] = 'contextTag(' . $context['in_str'] . ')';
                        } else {
                            $mod[] = 'contextOutTag()';
                        }
                        break;
                    case 'script':
                        $mod[] = 'contextJs(' . $context['in_str'] . ')';
                        break;
                }
            }
        }
        $this->cur_context = $context;
        if(count($mod) <= 0){
            return;
        }
        $ov=$var;
        foreach($mod as $name){
            $modifier=explode('(',$name,2);
            $name=$modifier[0];
            $params=substr($modifier[1],0,-1);
            $params=$this->getParams($params);
            foreach(self::$modifiers as $_name => $mods) {
                if($_name != $name)
                    continue;

                $ov = 'self::callModifier("'.$_name.'",'.$ov.(!empty($params) ? ',"'.implode('","',$params).'"' : "").')';
            }
            continue;
        }
        $var=$ov;
    }
    private function getVarContext($str, $context = null){
        if($context === null) {
            $cont = $this->content;
            $in_str = false;
            $str_char = '';
            $in_tag = false;
            $prev_tag = '';
            $prev_char = '';
        } else {
            $cont = substr($this->content,$context['offset']);
            $in_str = $context['in_str'];
            $str_char = $context['str_char'];
            $in_tag = $context['in_tag'];
            $prev_tag = $context['tag'];
            $prev_char = $context['prev_char'];
        }
        $i = strpos($cont, $str);
        if($i === false){
            return false;
        }
        $escaped = false;
        $capturing_tag_name = false;
        $char = '';
        for($j = 0; $j <= $i; $j++){
            $prev_char = $char;
            $char = $cont[$j];
            switch($char){
                case "\\":
                    $escaped = true;
                    continue 2;
                    break;
                case "'":
                case '"':
                    if(!$escaped){
                        if($in_str && $char === $str_char) {
                            $str_char = $char;
                        }
                        $in_str = !$in_str;
                    }
                    break;
                case '>':
                    if(!$in_str){
                        if($prev_char === '?'){
                            continue 2;
                        }
                        $in_tag = false;
                        if($capturing_tag_name) {
                            $capturing_tag_name = false;
                        }
                    }
                    break;
                case '<':
                    if(!$in_str){
                        if($cont[$j + 1] === '?'){
                            continue 2;
                        }
                        $prev_tag = "";
                        $in_tag = true;
                        $capturing_tag_name = true;
                        continue 2;
                    }
                    break;
                case ' ':
                    if($capturing_tag_name){
                        $capturing_tag_name = false;
                    }
                default:
                    if($capturing_tag_name){
                        $prev_tag .= $char;
                    }
            }
            if($escaped) {
                $escaped = false;
            }
        }
        return array(
            "tag" => $prev_tag,
            "in_tag" => $in_tag,
            "in_str" => $in_str,
            "offset" => $i + (int)$context['offset'],
            "str_char" => $str_char,
            "prev_char" => $prev_char
        );
    }
    private function escapeCharsInString($str, $escapeChar, $repChar, $strDelimiter='"') {

        $ret = "";
        $inQuote = false;
        $escaped = false;
        for($i = 0, $iMax = strlen($str); $i <= $iMax; $i++) {
            $char = substr($str, $i, 1);
            switch($char) {
                case '\\':
                    $escaped = true;
                    $ret .= $char;
                    break;
                case $strDelimiter:
                    if(!$escaped) {
                        $inQuote = !$inQuote;
                    }
                    $ret .= $char;
                    break;
                default:
                    if($inQuote && $char == $escapeChar) {
                        $ret .= $repChar;
                    } else {
                        $ret .= $char;
                    }
            }
            if($escaped) {
                $escaped = false;
            }
        }
        return $ret;
    }
    private function handleVars(){
        $matches=array();
        preg_match_all('/\{\s*\$(.+?)\s*\}/',$this->content,$matches);
        if(!empty($matches)){
            foreach($matches[1] as $i => $var_name){
                $real_var = $var_name;//strpos($var_name, 'preventTag') !== false;

                $var_name = $this->escapeCharsInString($var_name, '.', '**dot**');
                $var_name=explode('.',$var_name);
                if(count($var_name)>1){
                    $vn=$var_name[0];
                    if(empty($vn)){
                        $vn = "__func";
                    }
                    unset($var_name[0]);
                    $mod=array();
                    foreach($var_name as $j => $index){
                        $index = str_replace('**dot**', '.', $index);
                        $index=explode('->',$index,2);
                        $obj='';
                        if(count($index)>1){
                            $obj='->'.$index[1];
                            $index=$index[0];
                        }else {
                            $index = $index[0];
                        }
                        if(substr($index,-1,1)===")"){
                            $mod[]=$index.$obj;
                        }else{
                            if(strpos($index, '$') === 0) {
                                $vn .= "[$index]$obj";
                            }
                            else {
                                $vn .= "['$index']$obj";
                            }
                        }
                    }
                    $var_name='$'.$vn;
                    $mod=$this->applyModifiers($var_name,$mod,$matches[0][$i]);
                }else{
                    $var_name='$'.$var_name[0];
                    $mod=$this->applyModifiers($var_name,array(),$matches[0][$i]);
                }
                $rep='<?php try{ echo @'.$var_name.'; } catch(\Exception $e) { echo $e->getMessage(); } ?>';

                if(strpos($real_var, 'app_url') !== false){
                    $co = new Config();
                    $this->content=$this->str_replace_first($matches[0][$i],$co->app_url,$this->content);
                }

               else if(strpos($real_var, 'theme_url') !== false){
                  $co = new Config();
                    $this->content=$this->str_replace_first($matches[0][$i],$co->getThemeUrl(),$this->content);
                }else{
                    $this->content=$this->str_replace_first($matches[0][$i],$rep,$this->content);
                }

            }
        }
    }
    private function str_replace_first($find, $replace, $string) {
        $pos = strpos($string,$find);
        if ($pos !== false) {
            return substr_replace($string,$replace,$pos,strlen($find));
        }
        return "";
    }
    private function findVarInString(&$string){
        return $this->findVariableInString($string);
    }
    private  function findVariableInString(&$string){
        $var_match=array();
        preg_match_all('/\$([a-zA-Z0-9_\-\(\)\.\",>]+)/',$string,$var_match);
        if(!empty($var_match[0])){
            foreach($var_match[1] as $j => $var){
                $_var_name=explode('.',$string);
                if(count($_var_name)>1){
                    $vn=$_var_name[0];
                    unset($_var_name[0]);
                    $mod=array();
                    foreach($_var_name as $k => $index){
                        $index=explode('->',$index,2);
                        $obj='';
                        if(count($index)>1){
                            $obj='->'.$index[1];
                            $index=$index[0];
                        }else
                            $index=$index[0];
                        if(substr($index,-1,1)===")"){
                            $mod[]=$index.$obj;
                        }else{
                            $vn.="['$index']$obj";
                        }
                    }
                    $_var_name='$'.$vn;
                    $this->applyModifiers($_var_name,$mod);
                }else{
                    $_var_name='$'.$_var_name[0];
                }
                $string=str_replace(@$var_match[0][$j],'".'.$_var_name.'."',$string);
            }
        }
        return $var_match;
    }
    private function handleIfMacros(){
        $match = $this->matchTags('/<([a-xA-Z_\-0-9]+).+?tn-if\s*=\s*"(.+?)".*?>/','{endif}');
        if (empty($match)) {
            return false;
        }
        $this->content = preg_replace('/<([a-xA-Z_\-0-9]+)(.+?)tn-if\s*=\s*"(.+?)"(.*?)>/','{if $3}<$1$2$4>',$this->content);
    }
    private function handleLoopMacros(){
        $match = $this->matchTags('/<([a-xA-Z_\-0-9]+).+?tn-loop\s*=\s*"(.+?)".*?>/','{endloop}');
        if (empty($match)) {
            return false;
        }
        $this->content = preg_replace('/<([a-xA-Z_\-0-9]+)(.+?)tn-loop\s*=\s*"(.+?)"(.*?)>/','{loop $3}<$1$2$4>',$this->content);
    }
    private function handleBlockMacros(){
        $match = $this->matchTags('/<([a-xA-Z_\-0-9]+).+?tn-block\s*=\s*"(.+?)".*?>/','{endblock}');
        if (empty($match)) {
            return false;
        }
        $this->content = preg_replace('/<([a-xA-Z_\-0-9]+)(.+?)tn-block\s*=\s*"(.+?)"(.*?)>/','{block $3}<$1$2$4>',$this->content);
    }
    private function matchTags($regex, $append=""){
        $matches = array();
        if (!preg_match_all($regex,$this->content,$matches)) {
            return false;
        }
        $offset = 0;
        $_offset = 0;
        $ret = array();
        foreach($matches[0] as $k => $match){
            $_cont = substr($this->content,$offset);
            $in_str = false;
            $escaped = false;
            $i = strpos($_cont, $match);
            $tag = $matches[1][$k];
            $len_match = strlen($match);
            $offset += $i + $len_match;
            $str_char = "";
            $lvl = 1;
            $prev_char = "";
            $prev_tag = "";
            $struct = "";
            $in_tag = false;
            $capturing_tag_name = false;
            $_m = array();
            foreach($matches as $z => $v){
                $_m[$z] = $matches[$z][$k];
            }
            $ret[$k] = array(
                'match' => $match,
                'matches' => $_m,
                'all' => $match,
                'inner' => "",
                'starts_at' => $offset - $len_match,
                'ends_at' => 0,
            );
            for($j = $i + strlen($match), $jMax = strlen($_cont); $j <= $jMax; $j++) {
                $char = $_cont[$j];
                $prev_char = $char;
                $struct .= $char;
                $break = false;
                switch ($char) {
                    case "\\":
                        $escaped = true;
                        continue 2;
                        break;
                    case "'":
                    case '"':
                        if(!$escaped){
                            if($in_str && $char == $str_char) {
                                $str_char = $char;
                            }
                            $in_str = !$in_str;
                        }
                        break;
                    case '>':
                        if(!$in_str){
                            if($in_tag) {
                                $in_tag = false;
                                if( $prev_tag === '/' .$tag){
                                    $lvl--;
                                    if($lvl <= 0) {
                                        $break=true;
                                    }
                                } else if(strpos($prev_tag, '/') === 0){
                                    $lvl--;
                                } else {
                                    if($prev_char !== '/' && !in_array(str_replace('/',"",$prev_tag), array('area','base','br','col','command','embed','hr','img','input','keygen','link','meta','param','source','track','wbr'))){
                                        $lvl++;
                                    }
                                }
                                if($capturing_tag_name) {
                                    $capturing_tag_name = false;
                                }
                            }
                        }
                        break;
                    case '<':
                        if($in_tag){
                            continue 2;
                        }
                        if(!$in_str){
                            $prev_tag = "";
                            $in_tag = true;
                            $capturing_tag_name = true;
                            continue 2;
                        }
                        break;
                    case ' ':
                        if($capturing_tag_name){
                            $capturing_tag_name = false;
                        }
                    default:
                        if($capturing_tag_name){
                            $prev_tag .= $char;
                        }
                }
                if($escaped) {
                    $escaped = false;
                }
                if($break){
                    break;
                }
            }
            $ret[$k]['all'] .= $struct;
            $struct_len = strlen($struct);
            $ret[$k]['inner'] = substr($struct,0,$struct_len - strlen($tag)-3);
            $ret[$k]['ends_at'] = $ret[$k]['starts_at'] + $struct_len + $len_match;
            if($break && !empty($append)){
                $this->content = substr_replace($this->content,$append,$ret[$k]['ends_at'],0);
            }
        }
        return $ret;
    }
    private function handleExtends(){
        $matches=array();
        preg_match_all('/\{\s*(extends )\s*(.+?)\s*\}/',$this->content,$matches);
        $base = $matches[2];
        if(count($base) <= 0)
            return;
        if(count($base)>1)
            throw new \Exception("Each template can extend 1 parent at the most");
        $base = $base[0];
        if(strpos($base, '"') === 0) {
            $base = substr($base, 1);
        }
        if(substr($base, -1) === '"') {
            $base = substr($base, 0, -1);
        }
        $base = self::$root . $base;
        if(!file_exists($base)) {
            throw new \Exception("Unable to extend base template ". $base);
        }
        $this->base = $base;
        $this->content = str_replace($matches[0][0], "", $this->content);
    }
    private function handleIfs(){
        $matches=array();
        preg_match_all('/\{\s*(if|elseif)\s*(.+?)\s*\}/',$this->content,$matches);
        if(!empty($matches)){
            foreach($matches[2] as $i => $condition){
                $condition=trim($condition);
                $condition=str_replace(array(
                    'eq',
                    'gt',
                    'lt',
                    'neq',
                    'or',
                    'gte',
                    'lte'
                ),array(
                    '==',
                    '>',
                    '<',
                    '!=',
                    '||',
                    '>=',
                    '<='
                ),$condition);
                $var_match=array();
                preg_match_all('/\$([a-zA-Z0-9_\-\(\)\.]+)/',$condition,$var_match);
                if(!empty($var_match)){
                    foreach($var_match[1] as $j => $var){
                        $var_name=explode('.',$var);
                        if(count($var_name)>1){
                            $vn=$var_name[0];
                            unset($var_name[0]);
                            $mod=array();
                            foreach($var_name as $k => $index){
                                $index=explode('->',$index,2);
                                $obj='';
                                if(count($index)>1){
                                    $obj='->'.$index[1];
                                    $index=$index[0];
                                }else
                                    $index=$index[0];
                                if(substr($index,-1,1) === ')'){
                                    $mod[]=$index.$obj;
                                }else{
                                    $vn.="['$index']$obj";
                                }
                            }
                            $var_name='$'.$vn;
                            $this->applyModifiers($var_name,$mod);
                        }else{
                            $var_name='$'.$var_name[0];
                        }
                        $condition=str_replace(@$var_match[0][$j],$var_name,$condition);
                    }
                }
                $rep='<?php '.$matches[1][$i].'(@'.$condition.'): ?>';
                $this->content=str_replace($matches[0][$i],$rep,$this->content);
            }
        }
        $this->content=preg_replace('/\{\s*(\/if|endif)\s*\}/','<?php endif; ?>',$this->content);
        $this->content=preg_replace('/\{\s*else\s*\}/','<?php else: ?>',$this->content);
    }
    private function handleBlocks(){
        $matches=array();
        preg_match_all('/\{\s*(block)\s*(.+?)\s*\}/',$this->content,$matches);
        $blocks = $matches[2];
        if(count($blocks) <= 0)
            return;
        foreach($blocks as $i => $block) {
            $block = trim($block);
            $rv = '<?php ob_start(array(&$this, "ob_'.$block.'")); ?>';
            $this->content = str_replace($matches[0][$i], $rv, $this->content);
        }
        $this->content=preg_replace('/\{\s*endblock\s*\}/','<?php ob_end_flush(); ?>',$this->content);
    }
    public function __call($name, $args) {
        $n = explode('_', $name);
        if($n[0] === 'ob') {
            $this->blocks[$n[1]] = $args[0];
        }
        if($this->base != null)
            return "";

        return empty($this->blocks_override[$n[1]]) ? $args[0] : $this->blocks_override[$n[1]];
    }

    private function handleVar(){

        $matches= [];
        preg_match_all('/\{\s*(var|let|set)\s*(.+?)\s*\}/',$this->content,$matches);
        if(!empty($matches)){
            foreach($matches[2] as $i => $var) {

                    $var = str_replace(' = ', '**in**', $var);

                    $var_det = explode('**in**', $var);

                  if(is_array($var_det)) {
                      $key = $var_det[0];
                      $key = $this->removeWhiteSpaces($key);
                      $key = str_replace('$', '', $key);
                      $val = $var_det[1];
                  }
                    $this->assign($key, $val);
                    $rep = '<?php $' . $key . ' = ' . $val . '; ?>';
                    $this->content = str_replace($matches[0][$i], $rep, $this->content);

            }
        }
    }

    private function handleLoops(){
        $matches=array();
        preg_match_all('/\{\s*(loop|for)\s*(.+?)\s*\}/',$this->content,$matches);
        if(!empty($matches)){
            foreach($matches[2] as $i => $loop){
                $loop = str_replace(' in ', '**in**', $loop);
                $loop = $this->removeWhiteSpaces($loop);
                $loop_det=explode('**in**',$loop);
                $loop_name=$loop_det[1];
                unset($loop_det[1]);
                $loop_name=explode('.',$loop_name);
                if(count($loop_name)>1){
                    $ln=$loop_name[0];
                    unset($loop_name[0]);
                    foreach($loop_name as $j => $suffix)
                        $ln.="['$suffix']";
                    $loop_name=$ln;
                }else{
                    $loop_name=$loop_name[0];
                }
                $key=NULL;
                $val=NULL;
                $loop_vars = explode(',',$loop_det[0]);
                if (count($loop_vars) > 1) {
                    $key = $loop_vars[0];
                    $val = $loop_vars[1];
                } else {
                    $val = $loop_vars[0];
                }
                foreach($loop_det as $j => $_val){
                    @list($k,$v)=explode(',',$_val);
                    if($k === 'key'){
                        $key=$v;
                        continue;
                    }
                    if($k === 'item'){
                        $val=$v;
                        continue;
                    }
                }
                $rep='<?php foreach('.$loop_name.' as '.(!empty($key) ? $key.' => '.$val : ' '.$val).'): ?>';
                $this->content=str_replace($matches[0][$i],$rep,$this->content);
            }
        }
        $this->content=preg_replace('/\{\s*(\/loop|endloop|\/for|endfor)\s*\}/','<?php endforeach; ?>',$this->content);
    }
    public static function removeSpecialChars($text){
        $find = ['á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ',' ','"',"'"];
        $rep  = ['a','e','i','o','u','A','E','I','O','U','n','N','-',"",""];
        return str_replace($find,$rep,$text);
        //return(strtr($text,$tofind,$replac));
    }
    public static function zeroFill($text,$digits){
        $ret = '';
        if(strlen($text)<$digits){
            $ceros=$digits-strlen($text);
            for($i=0;$i<=$ceros-1;$i++){
                $ret.="0";
            }
            $ret=$ret.$text;
            return $ret;
        }

        return $text;
    }
    private static function initModifiers(){





        self::extendModifier('upper', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return strtoupper($input);
        });
        self::extendModifier('firstupper', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return ucfirst($input);
        });
        self::extendModifier('url', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return str_replace('%2F','\/',$input);
        });
        self::extendModifier('lower', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return strtolower($input);
        });
        self::extendModifier('capitalize', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return ucwords($input);
        });
        self::extendModifier('base64_decode', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return base64_decode($input);
        });
        self::extendModifier('abs', static function($input) {
            if(!is_numeric($input)){
                return $input;
            }
            return abs($input);
        });
        self::extendModifier('isEmpty', static function($input) {
            return empty($input);
        });
        self::extendModifier('truncate', static function($input, $len) {
            if(empty($len)) {
                throw new \Exception('length parameter is required');
            }
            return substr($input,0,$len).(strlen($input) > $len ? '...' : '');
        });
        self::extendModifier('count', static function($input) {
            return count($input);
        });
        self::extendModifier('length', static function($input) {
            return count($input);
        });
        self::extendModifier('toLocal', static function($input) {
            if(!is_object($input)){
                throw new \Exception('variable is not a valid date');
            }
            return date_timezone_set($input, timezone_open(self::$local_tz));
        });
        self::extendModifier('toTz', static function($input, $tz) {
            if(!is_object($input)){
                throw new \Exception('variable is not a valid date');
            }
            return date_timezone_set($input, timezone_open($tz));
        });
        self::extendModifier('toGMT', static function($input, $tz) {
            if(!is_object($input)){
                throw new \Exception('variable is not a valid date');
            }
            if(empty($tz)){
                throw new \Exception('timezone is required');
            }
            return date_timezone_set($input, timezone_open('GMT'));
        });
        self::extendModifier('date', static function($input, $format) {

            if(empty($format)){
                throw new \Exception('date format is required');
            }
            return date($format);
        });
        self::extendModifier('date_format', static function($input, $format) {
            if(!is_object($input)){
                throw new \Exception('variable is not a valid date');
            }
            if(empty($format)){
                throw new \Exception('date format is required');
            }
            return date_format($input,$format);
        });
        self::extendModifier('nl2br', static function($input) {
            return nl2br($input);
        });
        self::extendModifier('stripSlashes', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return stripslashes($input);
        });

        self::extendModifier('substract', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input - (float)$val;
        });
        self::extendModifier('multiply', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input * (float)$val;
        });
        self::extendModifier('divide', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input / (float)$val;
        });
        self::extendModifier('mod', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input % (float)$val;
        });
        self::extendModifier('encodeTags', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return htmlspecialchars($input,ENT_NOQUOTES);
        });
        self::extendModifier('decodeTags', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return htmlspecialchars_decode($input);
        });
        self::extendModifier('stripTags', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return strip_tags($input);
        });
        self::extendModifier('urlDecode', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return urldecode($input);
        });
        self::extendModifier('addSlashes', static function($input){
            return addslashes($input);
        });
        self::extendModifier('urlFriendly', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return urlencode(self::removeSpecialChars(strtolower($input)));
        });
        self::extendModifier('trim', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return trim($input);
        });


        self::extendModifier('sha1', static function($input) {
            if(!is_string($input)){
                throw new \Exception('input must be string');
            }
            return sha1($input);
        });
        self::extendModifier('safe', static function($input) {
            return htmlentities($input, ENT_QUOTES);
        });
        self::extendModifier('numberFormat', static function($input, $precision = 2) {
            if(!is_numeric($input)){
                throw new \Exception('input must be numeric');
            }
            return number_format($input,(int)$precision);
        });
        self::extendModifier('lastIndex', static function($input) {
            if(!is_array($input)){
                throw new \Exception('input must be an array');
            }
            return current(array_reverse(array_keys($input)));
        });
        self::extendModifier('lastValue', static function($input) {
            if(!is_array($input)){
                throw new \Exception('input must be an array');
            }
            return current(array_reverse($input));
        });
        self::extendModifier('jsonEncode', static function($input) {
            return json_encode($input);
        });
        self::extendModifier('substr', static function($input, $a = 0, $b = 0) {
            return substr($input,$a,$b);
        });
        self::extendModifier('join', static function($input, $glue) {
            if(!is_array($input)){
                throw new \Exception('input must be an array');
            }
            if(empty($glue)){
                throw new \Exception('string glue is required');
            }
            return implode($glue,$input);
        });
        self::extendModifier('explode', static function($input, $del) {
            if(!is_string($input)){
                throw new \Exception('input must be a string');
            }
            if(empty($del)){
                throw new \Exception('delimiter is required');
            }
            return explode($del,$input);
        });
        self::extendModifier('replace', static function($input, $search, $replace) {
            if(!is_string($input)){
                throw new \Exception('input must be a string');
            }
            if(empty($search)){
                throw new \Exception('search is required');
            }
            if(empty($replace)){
                throw new \Exception('replace is required');
            }
            return str_replace($search,$replace,$input);
        });
        self::extendModifier('preventTagEncode', static function($input) {
            return $input;
        });

        self::extendModifier('default', static function($input, $default) {
            return (empty($input) ? $default : $input);
        });
        self::extendModifier('contextJs', static function($input, $in_str) {
            if( (is_object($input) || is_array($input)) && !$in_str){
                return json_encode($input);
            }

            if(is_numeric($input) || is_bool($input)){
                return $input;
            }

            if($input === null) {
                return 'null';
            }

            if(!$in_str){
                return '"' . addslashes($input) .'"';
            }

            if(is_object($input) || is_array($input)) {
                $input = json_encode($input);
            }
            return addslashes($input);
        });
        self::extendModifier('contextOutTag', static function($input) {
            if(is_object($input) || is_array($input)){
                return $input;
            }

            return htmlentities($input,ENT_QUOTES);
        });

        self::extendModifier('contextTag', static function($input, $in_str) {
            if((is_object($input) || is_array($input)) && $in_str){
                return http_build_query($input);
            }

            if($in_str) {
                return urlencode($input);
            }

            return htmlentities($input,ENT_QUOTES);
        });

        self::extendModifier('addDoubleQuotes', static function($input){
            return '"' . $input . '"';
        });

        self::extendModifier('ifEmpty', static function($input, $true_val, $false_val = null) {
            if(empty($true_val)){
                throw new \Exception('true value is required');
            }
            $ret = $input;
            if(empty($ret)) {
                $ret = $true_val;
            } else if($false_val) {
                $ret = $false_val;
            }
            return $ret;
        });
        self::extendModifier("if", static function($input, $condition, $true_val, $false_val = null, $operator = 'eq') {
            if(empty($true_val)){
                throw new \Exception('true value is required');
            }
            switch($operator){
                case '':
                case '==':
                case '===':
                case '=':
                case 'eq':
                default:
                    $operator= '===';
                    break;
                case '<':
                case 'lt':
                    $operator= '<';
                    break;
                case '>':
                case 'gt':
                    $operator= '>';
                    break;
                case '<=':
                case 'lte':
                    $operator= '<=';
                    break;
                case '>=':
                case 'gte':
                    $operator= '>=';
                    break;
                case 'neq':
                    $operator = '!==';
                    break;
            }
            $ret = $input;
            if(eval('return ("'.$condition.'"'.$operator.'"'.$input.'");')) {
                $ret = $true_val;
            } else if($false_val) {
                $ret = $false_val;
            }
            return $ret;
        });
    }
}