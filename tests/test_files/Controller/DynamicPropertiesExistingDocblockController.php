<?php
namespace TestApp\Controller;

/**
 * Existing docblock
 *
 * @property \TestApp\Model\Table\BarBarsTable $BarBars
 */
#[\AllowDynamicProperties]
class DynamicPropertiesExistingDocblockController extends AppController {

	protected ?string $defaultTable = 'BarBars';

}
