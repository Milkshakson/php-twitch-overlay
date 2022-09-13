<?php

namespace App\Controllers;

use App\Libraries\{Dotenv, Request, Session, Twitch};
use Exception;

class ViewersController
{
    public function index()
    {

        require_once('./App/views/includes/header.php');
        $request = new Request();
        $streamer = $request->find('streamer');
        $autorizados = [
            'rogercwb',
            'milkshakson',
            'habbet3',
            'tritonpoker',
            'luizftorres'
        ];
        if (!in_array($streamer, $autorizados)) {
            exit("<h1 class='text-center text-danger'>Streamer não consta na lista de autorizados.</h1>");
        }
        $layout = empty($_GET['layout']) ? 'h' : $_GET['layout'];
        $env = new Dotenv();
        $twitch = new Twitch([
            'clientId' => $env->get('clientIdTwitch'),
            'clientSecret' => $env->get('clientSecretTwitch')
        ]);
        $viewers = $twitch->getChatters($streamer);
?>
<div class='transparencia container-main'>
    <?php
            try {
                $session = new Session();
                $storedTokenTwitch = $session->get('tokenTwitch');
                $twitch->auth($storedTokenTwitch);
                $session->set('tokenTwitch', $twitch->getToken(true));
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
            ?>
    <div class="twitch-users row <?= $classRow ?> opacity-25 text-center">
        <?php
                shuffle($viewers);
                $max = count($viewers) >= 10 ? 10 : count($viewers);
                echo "<div class='text-center $classCard'>";
                echo "<div class='text-center count-viewer-name text-success  col-lg-12 col-md-12'>Presentes</div>";
                echo "<div class='text-count-viewer'>" . count($viewers) . "</div>";
                echo "</div>";
                for ($i = 0; $i < $max; $i++) {
                    $viewer = $viewers[$i];
                    echo "<div class='text-center $classCard'>";
                    $nome = Ucfirst($viewer['nome']);
                    echo "<div class='text-center text-viewer-name text-success  col-lg-12 col-md-12'>$nome</div>";
                    $avatarUser = $twitch->fetch("https://api.twitch.tv/helix/users?login=$nome", 'get');
                    if (is_object($avatarUser)) {
                        $src = $avatarUser->data[0]->profile_image_url;
                        echo "<img class='bg-info rounded-circle' src='$src' $imgSize />";
                    }
                    echo "</div>";
                }
            } catch (Exception $e) {
                echo "<h1 class='text-center text-danger'>Houve um erro ao listar os avatares.</h1>";
            }
                ?>
    </div>
</div>
<?php require_once('./App/views/includes/footer.php');
        reload(60000);
    }
}