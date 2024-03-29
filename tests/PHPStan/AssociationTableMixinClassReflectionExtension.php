<?php declare(strict_types = 1);

namespace IdeHelper\PHPStan;

use Cake\ORM\Association;
use Cake\ORM\Table;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;

class AssociationTableMixinClassReflectionExtension implements PropertiesClassReflectionExtension, MethodsClassReflectionExtension, BrokerAwareExtension {

	private ?Broker $broker = null;

	/**
	 * @param \PHPStan\Broker\Broker $broker Class reflection broker
	 * @return void
	 */
	public function setBroker(Broker $broker): void {
		$this->broker = $broker;
	}

	/**
	 * @return \PHPStan\Reflection\ClassReflection
	 */
	protected function getTableReflection(): ClassReflection {
		return $this->broker->getClass(Table::class);
	}

	/**
	 * @param \PHPStan\Reflection\ClassReflection $classReflection Class reflection
	 * @param string $methodName Method name
	 * @return bool
	 */
	public function hasMethod(ClassReflection $classReflection, string $methodName): bool {
		if (!$classReflection->isSubclassOf(Association::class)) {
			return false;
		}

		return $this->getTableReflection()->hasMethod($methodName);
	}

	/**
	 * @param \PHPStan\Reflection\ClassReflection $classReflection Class reflection
	 * @param string $methodName Method name
	 * @return \PHPStan\Reflection\MethodReflection
	 */
	public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection {
		return $this->getTableReflection()->getMethod($methodName);
	}

	/**
	 * @param \PHPStan\Reflection\ClassReflection $classReflection Class reflection
	 * @param string $propertyName Method name
	 * @return bool
	 */
	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool {
		if (!$classReflection->isSubclassOf(Association::class)) {
			return false;
		}

		return $this->getTableReflection()->hasProperty($propertyName);
	}

	/**
	 * @param \PHPStan\Reflection\ClassReflection $classReflection Class reflection
	 * @param string $propertyName Method name
	 * @return \PHPStan\Reflection\PropertyReflection
	 */
	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection {
		return $this->getTableReflection()->getProperty($propertyName);
	}

}
