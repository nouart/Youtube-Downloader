<?php
namespace myPHPnotes;

use Symfony\Component\Process\ProcessBuilder;
use Noodlehaus\Config;
/**
 * Video Processing Class
 */
class Processor
{
	protected $processBuilder;
	protected $config;

	function __construct(Config $config)
	{
		$this->config = $config;
		$this->processBuilder = new ProcessBuilder();
		$environment = $this->config->get('environment');
		if ($environment == "windows") {
			$this->processBuilder->setPrefix([$this->config->get('paths.youtube-dl')]);
		}else {
			$this->processBuilder->setPrefix(array_merge([$this->config->get('paths.python')], [$this->config->get('paths.youtube-dl')]));
		}
	}
		
		
	public function getEntity($url, $format, $property = "dump-json")
	{
		$arguments = $this->processBuilder->setArguments(["-4","--".$property, $url]);

		if (isset($format)){
			$this->processBuilder->add('-F', $format);
		}
		
		$command = $this->processBuilder->getProcess()->getCommandLine();
		$process = $this->processBuilder->getProcess();
		$process->run();
	
		if (!$process->isSuccessful()) {
			//if process is unsuccessful
			$error = $process->getErrorOutput();
			throw new \Exception("Error Processing Request".$error, 1);
		}else 
		{
			return trim($process->getOutput());
		}
	}

	public function getJSON($url, $format = null)
	{
		return json_decode($this->getEntity($url, $format, 'dump-single-json'));

	}

}