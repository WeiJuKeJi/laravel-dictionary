<?php

namespace WeiJuKeJi\LaravelDictionary\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use WeiJuKeJi\LaravelDictionary\Http\Controllers\Concerns\RespondsWithApi;

abstract class Controller extends BaseController
{
    use RespondsWithApi;
}
