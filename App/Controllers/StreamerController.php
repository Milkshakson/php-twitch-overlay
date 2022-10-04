<?php

namespace App\Controllers;

use App\Libraries\Dotenv;
use App\Libraries\Request;
use App\Libraries\Session;
use App\Libraries\Twitch;
use App\Models\StreamerModel;
use Exception;

class StreamerController extends BaseController
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

        $streamerName = strtolower($request->find('streamer'));
        require_once(APPPATH . 'views/includes/header.php');
        $html = "<div class='content-overlay'>";
        $autorizados = [
            'rogercwb',
            'milkshakson',
            'habbet3',
            'tritonpoker',
            'luizftorres',
            'felipemojave'
        ];
        if (!in_array($streamerName, $autorizados)) {
            exit("<h1 class='text-center text-danger  content-overlay'>Streamer $streamerName não consta na lista de autorizados.</h1>");
        }
        $streamer = $streamerModel->findByName($streamerName);
        if (!$streamer) {
            exit("<div class='text-center text-danger content-overlay'>As credenciais para o streamer não estão corretas.</div>");
        }
        $subList = $streamerModel->getSubList($streamer);
        $layout = empty($_GET['layout']) ? 'h' : $_GET['layout'];
        $env = new Dotenv();
        $twitch = new Twitch([
            'clientId' => $env->get('clientIdTwitch'),
            'clientSecret' => $env->get('clientSecretTwitch')
        ]);
        $viewers = $twitch->getChatters($streamerName);
?>
<?php
        $html .= "<div class='transparencia container-main'>";
        try {
            $session = new Session();
            $twitch->getClientCredentials();
            $classRow = "col-lg-12 col-md-12";
            $classCard = "col-lg-1 col-md-1";
            $imgSize = " width='120px'";
            if ($layout == 'h') {
                $classRow = "col-lg-12 col-md-12";
                $classCard = "col-lg-1 col-md-1";
                $imgSize = " width='120px'";
            } elseif ($layout == 'v') {
                $classRow = "col-lg-1 col-md-1";
                $classCard = "col-lg-12 col-md-12";
                $imgSize = " height='55px'";
            }
            $html .= "<div class='twitch-users row $classRow opacity-25 text-center'>";

            $html .= "<div class='text-center $classCard'>";
            $html .= "<div class='text-center count-viewer-name text-success  col-lg-12 col-md-12'>Presentes</div>";
            $html .= "<div class='text-count-viewer'>" . count($viewers) . "</div>";
            $html .= "</div>";
            $usersToSearch = [];
            $viewersNoBot = array_filter($viewers, function ($viewer) {
                return !$viewer['isBot'];
            });
            $modList = array_column(array_filter($viewers, function ($viewer) {
                return $viewer['tipo'] == 'moderators';
            }), 'nome');
            if (count($viewersNoBot) <= 10) {
                $viewersNoBot = $viewers;
            }
            shuffle($viewersNoBot);
            $max = count($viewersNoBot) >= 10 ? 10 : count($viewersNoBot);
            for ($i = 0; $i < $max; $i++) {
                $usersToSearch[] = $viewersNoBot[$i]['nome'];
            }
            $avatares = $twitch->getUsersInfo($usersToSearch);
            foreach ($avatares as $viewer) {
                if (is_object($viewer)) {
                    $html .= "<div class='text-center $classCard'>";
                    $nome = $viewer->display_name;
                    if (key_exists($nome, $subList)) {
                        $classSub = 'sub';
                    } else {
                        $classSub = '';
                    }

                    if (in_array($viewer->display_name, $modList)) {
                        $nome = "<i class='ri-sword-fill text-success pe-1'></i>" . $nome;
                    }
                    if ($twitch->isBot($viewer->display_name)) {
                        $nome = "<i class='bx bx-bot text-danger pe-1'></i>" . $nome;
                    }



                    $html .= "<div class='text-viewer-name $classSub w-100 d-flex flex-nowrap justify-content-center align-items-center'>$nome</div>";
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
        $html .= "</div>";
        return $html;
    }
}