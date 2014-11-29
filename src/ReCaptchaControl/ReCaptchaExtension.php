<?php

/**
 * This file is part of the ReCaptchaControl package
 *
 * Copyright (c) 2013 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/ReCaptchaControl
 */

namespace ReCaptchaControl;

use Nette;


/**
 * Nette\Forms reCAPTCHA compiler extension
 *
 * @author vojtech-dobes (https://github.com/vojtech-dobes)
 */
class ReCaptchaExtension extends Nette\DI\CompilerExtension
{

	/** @var string[] */
	protected $defaults = array(
		'methodName' => 'addReCaptcha',
		'secured' => FALSE,
	);

	/** @var string */
	protected $prefix;


	/** @return void */
	function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$container->addDefinition($this->prefix('recaptcha'))
				->setClass('ReCaptchaControl\ReCaptcha\ReCaptcha', array($config['publicKey'], $config['privateKey'], $config['secured']));
	}


	/**
	 * @param  Nette\PhpGenerator\ClassType $class
	 * @return void
	 */
	function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
		$initialize = $class->methods['initialize'];
		$config = $this->getConfig($this->defaults);

		$initialize->addBody('$context = $this;');
		$initialize->addBody('ReCaptchaControl\ReCaptchaControl::register($context->getByType(\'Nette\Http\IRequest\'), $context->getService(?), ?);',
				array($this->prefix('recaptcha'), $config['methodName']));
	}


	/**
	 * @param  Nette\Config\Configurator $configurator
	 * @param  string $prefix
	 * @return void
	 */
	static function register(Nette\Config\Configurator $configurator, $prefix = 'recaptcha')
	{
		$configurator->onCompile[] = function ($configurator, $compiler) use ($prefix) {
			$compiler->addExtension($prefix, new ReCaptchaExtension);
		};
	}

}
