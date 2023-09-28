<?php
namespace TestApp\Controller;

#[\AllowDynamicProperties]
class DynamicPropertiesController extends AppController {

	protected ?string $defaultTable = 'BarBars';

}
