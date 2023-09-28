<?php
namespace TestApp\Controller;

/**
 * Existing docblock
 */
#[\AllowDynamicProperties]
class DynamicPropertiesExistingDocblockController extends AppController {

	protected ?string $defaultTable = 'BarBars';

}
