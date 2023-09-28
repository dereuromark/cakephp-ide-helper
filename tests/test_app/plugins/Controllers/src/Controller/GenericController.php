<?php
namespace Controllers\Controller;

use Cake\Controller\Controller;

#[\AllowDynamicProperties]
class GenericController extends Controller {

	protected ?string $defaultTable = '';

}
