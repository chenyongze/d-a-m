<?php
mf_enable_xhprof();

function mf_enable_xhprof($autosave = true) {
    if ( !function_exists('xhprof_enable') ) return;    // 判断 XHProf 扩展是否安装
    $GLOBALS['MFXhprofDataQuery']['httpHost'] = $_SERVER['HTTP_HOST'];
    $GLOBALS['MFXhprofDataQuery']['request'] = $_SERVER['REQUEST_URI'];
    $GLOBALS['MFXhprofDataQuery']['requestId'] = $_SERVER['HTTP_REQUESTID'];
    $GLOBALS['MFXhprofDataQuery']['timestamp'] = $_SERVER['REQUEST_TIME_FLOAT'];
    $GLOBALS['MFXhprofDataPost']['post'] = $_POST;
    $GLOBALS['MFXhprofDataPost']['get'] = $_GET;
    $GLOBALS['MFXhprofDataPost']['cookie'] = $_COOKIE;
    $GLOBALS['MFXhprofDataPost']['server'] = $_SERVER;
    $GLOBALS['MFXhprofDataSaved'] = false;

    if ($autosave) {
        register_shutdown_function('mf_disable_xhprof');
    }

    xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
}

function mf_disable_xhprof() {
    if ( !function_exists('xhprof_enable') ) return;    // 判断 XHProf 扩展是否安装
    if ($GLOBALS['MFXhprofDataSaved']) {
        return;
    } else {
        $GLOBALS['MFXhprofDataSaved'] = true;
    }
    global $MFXhprofDataQuery, $MFXhprofDataQuery;
    $MFXhprofDataPost['data'] = xhprof_disable();
    $MFXhprofDataQuery['requestTime'] = microtime(1) - $MFXhprofDataQuery['timestamp'];
    $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, 'http://profiler.admin.mofang.com.tw/api/log?' . http_build_query($MFXhprofDataQuery));
    curl_setopt($ch, CURLOPT_URL, 'http://profiler/api/log?' . http_build_query($MFXhprofDataQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($MFXhprofDataPost));
    $ret = curl_exec($ch);
}