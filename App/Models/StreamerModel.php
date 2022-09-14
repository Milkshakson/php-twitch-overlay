<?php

namespace App\Models;

use App\Entities\Streamer;

class StreamerModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function findByName($name)
    {
        return new Streamer();
    }
}