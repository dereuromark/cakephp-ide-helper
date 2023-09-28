<?php
namespace TestApp\Controller;

/**
 * @property \TestApp\Model\Table\BarBarsTable $BarBars
 */
#[\AllowDynamicProperties]
class DynamicPropertiesController extends AppController {

	protected ?string $defaultTable = 'BarBars';

}
