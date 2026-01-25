<?php
namespace TestApp\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use TestApp\Controller\BarController;

#[UsesClass(BarController::class)]
class BarControllerTest extends IntegrationTestCase {
}
