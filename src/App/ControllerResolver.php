<?php

namespace App;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;


class ControllerResolver extends BaseControllerResolver
{
	protected $container;

	/**
	 * Constructor.
	 *
	 * @param ContainerInterface   $container A ContainerInterface instance
	 * @param ControllerNameParser $parser    A ControllerNameParser instance
	 * @param LoggerInterface      $logger    A LoggerInterface instance
	 */
	public function __construct(ContainerInterface $container, LoggerInterface $logger = null)
	{
		$this->container = $container;

		parent::__construct($logger);
	}

	/**
	 * Returns a callable for the given controller.
	 *
	 * @param string $controller A Controller string
	 *
	 * @return mixed A PHP callable
	 *
	 * @throws \LogicException           When the name could not be parsed
	 * @throws \InvalidArgumentException When the controller class does not exist
	 */
	protected function createController($controller)
	{
		if (false === strpos($controller, '::')) {
			$count = substr_count($controller, ':');

			if (1 == $count) {
				// controller in the service:method notation
				list($service, $method) = explode(':', $controller, 2);

				return array($this->container->get($service), $method);
			} elseif ($this->container->has($controller) && method_exists($service = $this->container->get($controller), '__invoke')) {
				return $service;
			} else {
				throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
			}
		}

		list($class, $method) = explode('::', $controller, 2);

		if (!class_exists($class)) {
			throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
		}

		$controller = $this->instantiateController($class);
		if ($controller instanceof ContainerAwareInterface) {
			$controller->setContainer($this->container);
		}

		return array($controller, $method);
	}
}
