<?php
function reload($timer = null, $echo = true)
{
    if (is_null($timer)) {
        $timer = 50;
    } elseif ($timer == 'rand') {
        $timer = rand(500, 5000);
    }

    $return =  "<script >
    var timer = $timer;
    try_reload();
    function try_reload(){
        if(timer<=0){
            window.location.reload(1);
        }else{
            timer= timer-1000;
            setTimeout(function(){
                try_reload();
            },1000);
        }
    }
    </script>";
    if ($echo)
        echo $return;
    else
        return
            $return;
}

if (!function_exists('str_starts_with')) {
    function str_starts_with($str, $start)
    {
        return (@substr_compare($str, $start, 0, strlen($start)) == 0);
    }
}