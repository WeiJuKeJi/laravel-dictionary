<?php

namespace WeiJuKeJi\LaravelDictionary\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use WeiJuKeJi\LaravelDictionary\Support\ApiResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;
}
