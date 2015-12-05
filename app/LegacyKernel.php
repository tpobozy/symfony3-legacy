<?php

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LegacyIntranetKernel implements HttpKernelInterface
{
	protected $dispatcher;

	public function __construct(ContainerAwareEventDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		ob_start();

		// container object, accessible from 'legacy_index.php' file
		// USAGE: $container->get('app.doctrine');
		$container = $this->dispatcher->getContainer();

		require_once APP_PATH."/legacy_index.php";

		return new Response(ob_get_clean());
	}

}


