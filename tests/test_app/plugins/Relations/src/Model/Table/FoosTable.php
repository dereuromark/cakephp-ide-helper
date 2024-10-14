<?php
namespace Relations\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\ORM\Table;

class FoosTable extends Table {

	protected array $_fields = [
		'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true],
		'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null],
		'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null],
		'params' => ['type' => 'json', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
	];

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		// Required relation
		$this->belongsTo('Relations.Users');
	}

	/**
	 * @return \Cake\Database\Schema\TableSchemaInterface
	 */
	public function getSchema(): TableSchemaInterface {
		$tableSchema = new TableSchema($this->getTable());

		foreach ($this->_fields as $field => $attributes) {
			$tableSchema->addColumn($field, $attributes);
		}

		return $tableSchema;
	}

}
