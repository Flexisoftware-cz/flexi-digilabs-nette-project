<?php

namespace App;

class Config
{

	/**
	 * @param array $parameters
	 */
	public function __construct(protected array $parameters)
	{
	}

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	public function getParameter(string $key)
	{
		return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : null;
	}

	/**
	 * @return array
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}
}