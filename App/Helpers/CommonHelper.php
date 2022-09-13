<?php
function reload($timer = null)
{
    if (is_null($timer)) {
        $timer = 50;
    } elseif ($timer == 'rand') {
        $timer = rand(500, 5000);
    }

    echo "<script >
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
}
function pre($var, $exit = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($exit)
        exit;
}