<?php

namespace App\Http\Controllers;

use App\Jobs\RabbitMQJob;
use Illuminate\Http\Request;

class RabbitController extends Controller
{
    public function rabbitDispatch(Request $req)
    {
        RabbitMQJob::dispatch($req->message);
    }
}
