<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Use the getItemsProducts method from ApiConsumerController
    public function index(ApiConsumerController $apiConsumer)
    {
        return $apiConsumer->getItemsProducts();
    }
}
