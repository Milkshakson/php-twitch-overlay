<?php

namespace App\Libraries;

class Input
{
    public function ipAddress()
    {
        return $this->IPFiltraProxy();
    }

    private function IPFiltraProxy()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
            $retorno = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $retorno = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $retorno = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $retorno = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $retorno = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $retorno = $_SERVER['REMOTE_ADDR'];
        else
            $retorno = '0.0.0.0';

        $MultiplosIp = explode(",", $retorno);
        if (sizeof($MultiplosIp) > 0)
            $retorno = $MultiplosIp[0];

        $ipPorta = explode(":", $retorno);
        if (sizeof($ipPorta) > 0)
            $retorno = $ipPorta[0];

        if ($retorno == "::1")
            $retorno = "localhost";
        return $retorno;
    }
}