<?php
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * @property \App\Controller\Component\RequestHandlerComponent $RequestHandler
 */
class AppController extends Controller {

	/**
	 * @var array
	 */
	public $components = ['Flash', 'RequestHandler'];

}
