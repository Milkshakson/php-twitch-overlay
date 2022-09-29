<?php

use App\Config\Url;

$configurl = new Url();
$baseUrl = $configurl->get('baseUrl');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="<?= $baseUrl ?>assets/css/twitch.css" rel="stylesheet">


    <link rel="stylesheet" href="<?= $baseUrl ?>assets\third-party\remixicon\remixicon.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets\third-party\boxicons\css\boxicons.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets\third-party\bootstrap-icons\bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</head>

<body>