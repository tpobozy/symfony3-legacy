<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BaseController implements ContainerAwareInterface
{
	use ContainerAwareTrait;


	public function redirect($url, $status = 302)
	{
		return new RedirectResponse($url, $status);
	}

	public function render($view, array $parameters = array(), Response $response = null)
	{
		return $this->get('templating')->renderResponse($view, $parameters, $response);
	}

	public function getUser()
	{
		// TODO
		// return $this->container->get('user');
	}

	public function getDoctrine()
	{
		return $this->container->get('doctrine');
	}

	public function get($key)
	{
		return $this->container->get($key);
	}

	public function getContainer()
	{
		return $this->container;
	}


}