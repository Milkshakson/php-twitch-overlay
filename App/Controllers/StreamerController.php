<?php

namespace App\Controllers;

use App\Libraries\Dotenv;
use App\Libraries\Request;
use App\Libraries\Session;
use App\Libraries\Twitch;
use App\Models\StreamerModel;
use Exception;

class StreamerController
{
    public function subList()
    {
        $streamerModel = new StreamerModel();
        $streamer = $streamerModel->findByName('milkshakson');
        $lista = $streamerModel->getSubList($streamer);
        $html = '<h2>Lista de inscritos do canal</h2>';
        foreach ($lista as $sub) {
            $html .= "<div>$sub->user_name</div>";
        }
        return $html;
    }
    public function viewersList()
    {

        $request = new Request();
        $streamerModel = new StreamerModel();

        $streamerName = $request->find('streamer');
        require_once('./App/views/includes/header.php');
        $html = '';
        $autorizados = [
            'rogercwb',
            'milkshakson',
            'habbet3',
            'tritonpoker',
            'luizftorres',
            'felipemojave'
        ];
        if (!in_array($streamerName, $autorizados)) {
            exit("<h1 class='text-center text-danger'>Streamer $streamerName não consta na lista de autorizados.</h1>");
        }
        $streamer = $streamerModel->findByName($streamerName);
        pre($streamer, 1);
        $subList = $streamerModel->getSubList($streamer);
        $layout = empty($_GET['layout']) ? 'h' : $_GET['layout'];
        $env = new Dotenv();
        $twitch = new Twitch([
            'clientId' => $env->get('clientIdTwitch'),
            'clientSecret' => $env->get('clientSecretTwitch')
        ]);
        $viewers = $twitch->getChatters($streamer);
?>
<?php
        $html .= "<div class='transparencia container-main'>";
        try {
            $session = new Session();
            $storedTokenTwitch = $session->get('tokenTwitch');
            $twitch->getClientCredentials();
            $classRow = "col-lg-12 col-md-12";
            $classCard = "col-lg-1 col-md-1";
            $imgSize = " width='120px'";
            $classTextViewerName = '';
            if ($layout == 'h') {
                $classRow = "col-lg-12 col-md-12";
                $classCard = "col-lg-1 col-md-1";
                $imgSize = " width='120px'";
                $classTextViewerName = 'text-horizontal';
            } elseif ($layout == 'v') {
                $classRow = "col-lg-1 col-md-1";
                $classCard = "col-lg-12 col-md-12";
                $imgSize = " height='55px'";
                $classTextViewerName = 'text-vertical';
            }
            $html .= "<div class='twitch-users row $classRow opacity-25 text-center'>";
            shuffle($viewers);
            $max = count($viewers) >= 10 ? 10 : count($viewers);
            $html .= "<div class='text-center $classCard'>";
            $html .= "<div class='text-center count-viewer-name text-success  col-lg-12 col-md-12'>Presentes</div>";
            $html .= "<div class='text-count-viewer'>" . count($viewers) . "</div>";
            $html .= "</div>";
            $usersToSearch = [];
            for ($i = 0; $i < $max; $i++) {
                $usersToSearch[] = $viewers[$i]['nome'];
            }
            $avatares = $twitch->getUsersInfo($usersToSearch);
            // $avatarUser = $twitch->getUsersInfo($nome);
            // for ($i = 0; $i < $max; $i++) {
            foreach ($avatares as $viewer) {
                // $viewer = $viewers[$i];
                if (is_object($viewer)) {
                    $html .= "<div class='text-center $classCard'>";
                    $nome = $viewer->display_name;
                    if (key_exists($nome, $subList)) {
                        $classSub = 'sub';
                    } else {
                        $classSub = '';
                    }
                    $html .= "<div class='text-center text-viewer-name $classSub  col-lg-12 col-md-12'>$nome</div>";
                    $src = $viewer->profile_image_url;
                    $html .= "<img class='bg-info  $classSub rounded-circle' src='$src' $imgSize />";
                    $html .= "</div>";
                }
            }
        } catch (Exception $e) {
            $html .= "<h1 class='text-center text-danger'>Houve um erro ao listar os avatares.</h1>";
        }
        $html .= reload(60000, false);
        $html .= '</div></div>';
        return $html;
    }

    public function authorize()
    {
        $scope = urlencode('channel:read:subscriptions channel_subscriptions');
        $twitch = new Twitch();
        $uri_return = urlencode($twitch->getRedirectUri());
        $env = new Dotenv();
        $clientId = $env->get('clientIdTwitch');
        $urlAuth =
            "https://id.twitch.tv/oauth2/authorize?response_type=code&client_id=$clientId&redirect_uri=$uri_return&scope=$scope";
        return ("<a href='$urlAuth' target='_blank'>$urlAuth</a>");
    }

    public function authorizeComplete()
    {
        include 'autoload.php';
        $env = new Dotenv();
        $session = new Session();
        if (key_exists('code', $_GET)) {
            $twitch = new Twitch([
                'clientId' => $env->get('clientIdTwitch'),
                'clientSecret' => $env->get('clientSecretTwitch'),
            ]);
            $code = $_GET['code'];
            $credentials = $twitch->getAuthorizationCode($code);
            pre($credentials);
            if ($credentials && property_exists($credentials, 'userId')) {
                $session->set('validAuth', $credentials);
                return '<h2>Credenciais salvas com sucesso.</h2>';
            } else {
                $session->set('validAuth', null);
                return '<h2>Falha ao salvar as credenciais.</h2>';
            }
        }
    }
}