<?php

use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher as EventDispatcher;
use App\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as DiYamlFileLoader;



class MainKernel implements HttpKernelInterface
{
	protected $environment;
	protected $legacyKernel;
	protected $httpKernel;
	protected $container;


	public function __construct($environment)
	{
		$this->environment = $environment;

		$this->container = $this->getContainer();

		$this->boot();
	}

	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		$url = $request->getUri();

		if (strpos($url, 'index.php') !== FALSE) {
			return $this->legacyKernel->handle($request, $type, $catch);
		}

		return $this->httpKernel->handle($request, $type, $catch);
	}

	protected function boot()
	{
		$dispatcher = new EventDispatcher($this->container);
		$resolver = new ControllerResolver($this->container);

		$this->httpKernel = new HttpKernel($dispatcher, $resolver);
		$this->legacyKernel = new LegacyIntranetKernel($dispatcher);

		$locator = new FileLocator(array(__DIR__.'/config'));
		$loader = new YamlFileLoader($locator);
		$collection = $loader->load('routes.yml');

		$context = new RequestContext();
		$matcher = new UrlMatcher($collection, $context);
		$dispatcher->addSubscriber(new RouterListener($matcher, $context));
	}

	public function getContainer()
	{
		if ($this->container) {
			return $this->container;
		}

		return $this->buildContainer();
	}

	protected function buildContainer()
	{
		$container = new ContainerBuilder();

		$loader = new DiYamlFileLoader($container, new FileLocator(__DIR__.'/config'));
		$loader->load('config_' . $this->environment . '.yml');

		$container->set('kernel', $this);
		$container->setParameter('key', 'value');

		$this->container = $container;

		//$container->compile();

		return $container;
	}

}


