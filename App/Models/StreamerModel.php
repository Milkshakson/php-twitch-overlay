<?php

namespace App\Models;

use App\Entities\Streamer;
use App\Libraries\Dotenv;
use App\Libraries\Session;
use App\Libraries\Twitch;
use DateTime;
use Exception;

class StreamerModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function findByName($name)
    {
        try {
            $streamerQuery = $this->query("select * from twitch_user where login = '$name'")->data[0];
            if ($streamerQuery) {
                $credentials = $this->getStreamerCredentials($streamerQuery->login, 'channel:read:subscriptions');
                $streamer = new Streamer();
                $streamer->setCredentials($credentials);
                return $streamer;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function getStreamerCredentials($login, $scope)
    {
        try {
            $credentials = $this->query("select * from twitch_authorization where login ='$login' and scope='$scope'");
            return $credentials->data[0];
        } catch (Exception $e) {
            return false;
        }
    }

    public function saveAuthorization($credentials)
    {
        try {
            $env = new Dotenv();
            $twitch = new Twitch([
                'clientId' => $env->get('clientIdTwitch'),
                'clientSecret' => $env->get('clientSecretTwitch'),
            ]);
            $user =  $twitch->getUserInfo($credentials->login);
            $created = new DateTime($user->created_at);
            $created = $created->format('Y-m-d H:i:s');
            $sqlCount = "select count(*) as count from twitch_user where login = '$credentials->login'";
            $countQuery = $this->query($sqlCount);
            $count = $countQuery->data ? $countQuery->data[0]->count : 0;
            if ($count > 0) {
                $sqlSalva = "uptate twitch_user  set 
                                display_name='$user->display_name',
                                profile_image_url= '$user->profile_image_url',
                                userId= $user->id,
                                description='$user->description',
                                created_at='$created'
                                where login='$user->login' ";
                $this->query($sqlSalva);
            } else {
                $sqlSalva = "insert into twitch_user (
                    login,
                    display_name, 
                    profile_image_url,
                    userId,
                    description,
                    created_at
                    ) values (

                        '$user->login',
                        '$user->display_name', 
                        '$user->profile_image_url',
                        $user->id,
                        '$user->description',
                        '$created'
                    )";

                $this->query($sqlSalva);
            }
            $expireDate = (!property_exists($credentials, 'exp') || is_null($credentials->exp)) ? null : $credentials->exp->format('Y-m-d H:i:s');
            foreach ($credentials->scope as $scope) {
                $this->query("delete from twitch_authorization where login = '$credentials->login' and scope ='$scope'");
                $insertAuthorization = "insert into twitch_authorization (" . //
                    " 
                login, 
                access_token,
                scope,
                expires_in,
                refresh_token,
                token_type,
                userId,
                expire_date
                ) values (" . //
                    "
                '$credentials->login', 
                 '$credentials->access_token',
                '$scope',
                '$credentials->expires_in' ,
                '$credentials->refresh_token',
                '$credentials->token_type',
                '$credentials->userId',
                '$expireDate'
                )";
                $this->query($insertAuthorization);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getSubList(Streamer $streamer)
    {
        //retorna a lista de subs do Streamer
        $env = new Dotenv();
        $twitch = new Twitch([
            'clientId' => $env->get('clientIdTwitch'),
            'clientSecret' => $env->get('clientSecretTwitch'),
        ]);
        $credentials = $streamer->getCredentials();
        $subList = $twitch->getSubList($credentials);
        if ($subList && property_exists($subList, 'refreshedCredential')) {
            if (property_exists($subList->refreshedCredential, 'scope') && is_string($subList->refreshedCredential->scope)) {
                $subList->refreshedCredential->scope = [$subList->refreshedCredential->scope];
            }
            $this->saveAuthorization($subList->refreshedCredential);
        }
        $newArray = [];
        if (property_exists($subList, 'data')) {
            foreach ($subList->data as $sub) {
                $newArray[$sub->user_name] = $sub;
            }
        }
        return $newArray;
    }
}