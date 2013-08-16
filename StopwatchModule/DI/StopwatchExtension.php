<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace StopwatchModule\DI;

use Nette\Config\Compiler;
use Nette\Config\CompilerExtension;
use Nette\Config\Configurator;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class StopwatchExtension extends CompilerExtension
{

	/**
	 * Processes configuration data. Intended to be overridden by descendant.
	 * @return void
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('stopwatch'))
			->setClass('StopwatchModule\Stopwatch')
			->addSetup('Nette\Diagnostics\Debugger::$bar->addPanel(?)', array('@self'));

		$container->getDefinition('application')
			->addSetup('$_this; $service->onStartup[] = function () {?->start("application"); }', array($this->prefix('@stopwatch')))
			->addSetup('$service->onRequest[] = function () {?->stop("application: request", "application"); }', array($this->prefix('@stopwatch')))
			->addSetup('$service->onResponse[] = function () {?->stop("application: response", "application"); }', array($this->prefix('@stopwatch')))
			->addSetup('$service->onShutdown[] = function () {?->stop("application: shutdown", "application"); }', array($this->prefix('@stopwatch')));
	}


	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = 'stopwatch')
	{
		$class = get_called_class();
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) use ($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}
