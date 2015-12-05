<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends BaseController {

	public function indexAction(Request $request)
	{
		exit(__METHOD__);
	}

	public function containerAction(Request $request)
	{
		echo "<pre>";

		var_dump($this->get('kernel'));
		exit;
	}
}