#!/usr/bin/env php
<?php

if(2 == (ini_get('mbstring.func_overload') & 2)) {
    die("don`t overload string functions in mbstring extension, see mbstring.func_overload option");
}

if($argc < 4) {
    echo "Usage " . $argv[0] . " XML_FILE OUT_DIR ENCODING [WITH_FORM_NO - 1/0] [BUILD_DIALING_ANCODES_MAP - 1/0] [PATH_TO_MORPHY_BUILDER] [PATH_TO_AOT_SOURCES]";
    exit;
}


function getArg(int $index, $default = null)
{
    global $argv;
    return isset($argv[$index]) ? $argv[$index] : $default;
}


// for backward compatibility, check the env var first
if (!($morphy_builder_dir = @getenv('MORPHY_DIR'))) {
    // if env var is not set and no arg provided, use phpmorphy provided builder
    $morphy_builder_dir = getArg(6, __DIR__ . '/../morph-builder/0.3.1-win32/');
}


// for backward compatibility, check the env var first
if (!($aot_root = @getenv('RML'))) {
    // if env var is not set and no arg provided,
    // assume that aot is located beside phpmorphy in aot folder
    $aot_root = getArg(6, __DIR__ . '/../../../aot/');
}


// set env var for script exec for aot tools needs
@putenv("RML=$aot_root");
define('BUILD_DICT_BIN_DIR', __DIR__);
define('MORPHY_BUILDER', $morphy_builder_dir . '/bin/morphy_builder.exe');


if(false == ($phprc = @getenv('PHPRC'))) {
    define('PHP_BIN', '/usr/bin/env php');
} else {
    define('PHP_BIN', $phprc . '/php');
}

function doError($msg) {
    echo $msg;
    exit(1);
}

class ShellArgsEscaper {
    protected static $need_wrap;

    static function escape($arg) {
        if(!isset(self::$need_wrap)) {
            self::$need_wrap = self::needWrap();
        }

        if(self::$need_wrap) {
            // double slashes at end of argument
            $orig_len = strlen($arg);
            $slashes = $orig_len - strlen(rtrim($arg, '\\'));
            $arg .= str_repeat('\\', $slashes);
        }

        return escapeshellarg($arg);
    }

    static protected function needWrap() {
        if(substr(PHP_OS, 0, 3) == 'WIN') {
            $test = '\a\b\c\\';

            $result = escapeshellarg($test);

            return substr($result, -3, 2) != '\\\\';
        }

        return false;
    }
}

function doExec($title, $file, $args) {
    echo $title . PHP_EOL;
    
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    $cmd = '';
    switch(strtolower($ext)) {
        case 'php':
            $cmd = PHP_BIN . ' -f ' . ShellArgsEscaper::escape($file) . ' --';
            break;
        default:
            $cmd = ShellArgsEscaper::escape($file);
            
    }
    
    foreach($args as $k => $v) {
        if(is_null($v)) {
            if(is_string($k)) {
                $cmd .= ' ' . $k;
            }
        } else {
            if(is_string($k)) {
                $cmd .= ' ' . $k . '=' . ShellArgsEscaper::escape($v);
            } else {
                $cmd .= ' ' . ShellArgsEscaper::escape($v);
            }
        }
    }
    
    $desc = array(
        1 => array("pipe", "w"),  // stdout
        2 => array("pipe", "w") // stderr
    );
    
    $opts = array(
        'binary_pipes' => true,
        'bypass_shell' => true
    );
    
    $pipes = array();
    
    if(false === ($handle = proc_open($cmd, $desc, $pipes, null, null, $opts))) {
        doError('Can`t execute \'' . $cmd . '\' command');
    }
    
    if(1) {
        while(!feof($pipes[1])) {
            fputs(STDOUT, fgets($pipes[1]));
        }
    } else {
        stream_copy_to_stream($pipes[1], STDOUT);
    }
    
    $stderr = trim(stream_get_contents($pipes[2]));
    
    fclose($pipes[1]);
    fclose($pipes[2]);
    $errorcode = proc_close($handle);
    
    if($errorcode) {
        doError(
            PHP_EOL . PHP_EOL . "Command '" . $cmd .'\' exit with code = ' . $errorcode . ', error = \'' . $stderr . '\''
        );
    }
    
    echo "OK." . PHP_EOL;
}

function get_locale($xml) {
    $reader = new XMLReader();
    if(false === $reader->open($xml)) {
        return false;
    }
    
    while($reader->read()) {
        if($reader->nodeType == XMLReader::ELEMENT) {
            if($reader->localName === 'locale') {
                $result = $reader->getAttribute('name');
                
                $result = strlen($result) ? $result : false;
                break;
            }
        }
    }
    
    $reader->close();
    
    return $result;
}

function locale_to_dialing($locale) {
    static $map = array(
        'ru_RU' => 'Russian',
        'en_EN' => 'English',
        'de_DE' => 'German',
    );

    if(isset($map[$locale])) {
        return $map[$locale];
    }

    return false;
}

if(false === ($locale = get_locale($argv[1]))) {
    doError("Can`t retrieve locale name from '" . $argv[1] . "' file");
}

$out_dir = $argv[2];
$morph_data_file = $out_dir . '/morph_data.' . strtolower($locale) . '.bin';

echo "Found '$locale' locale in $argv[1]" . PHP_EOL;

$args = array(
    '--xml' => $argv[1],
    '--out-dir' => $argv[2],
    '--out-encoding' => $argv[3],
    '--force-encoding-single-byte' => null,
    '--verbose' => null,
    '--case' => 'upper',
);

if (getArg(4, false)) {
    $args['--with-form-no'] = 'yes';
}

doExec('Build dictionary', MORPHY_BUILDER, $args);

doExec('Extract gramtab', BUILD_DICT_BIN_DIR . '/extract-gramtab.php', array($morph_data_file, $out_dir));
doExec('Extract graminfo header', BUILD_DICT_BIN_DIR . '/extract-graminfo-header.php', array($morph_data_file, $out_dir));
doExec('Create ancodes cache', BUILD_DICT_BIN_DIR . '/extract-ancodes.php', array($morph_data_file, $out_dir));

if (getArg(5, false)) {
    if(false !== ($language = locale_to_dialing($locale))) {
        doExec('Create dialing ancodes map', BUILD_DICT_BIN_DIR . '/extract-ancodes-map.php', array($morph_data_file, $language, $out_dir));
    } else {
        echo "Locale '$locale' unsupported for dialing dictionaries. Skip ancodes map." . PHP_EOL;
    }
}
