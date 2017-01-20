<?php
namespace vaola;
use Plenty\Modules\DataExchange\Services\ExportPresetContainer;
use Plenty\Plugin\DataExchangeServiceProvider;
class vaolaServiceProvider extends DataExchangeServiceProvider
{
	public function register()
	{
	}
	public function exports(ExportPresetContainer $container)
	{
		$formats = [
			'vaolaDE',
			          
		];
		foreach ($formats as $format)
		{
			$container->add(
				$format,
				'vaola\ResultFields\\'.$format,
				'vaola\Generators\\'.$format,
				'vaola\Filters\\' . $format
			);
		}
	}
}