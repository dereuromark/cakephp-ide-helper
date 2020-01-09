<?php
namespace Relations\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\ORM\Table;

class BarsTable extends Table {

	/**
	 * @var array
	 */
	protected $_fields = [
		'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true],
		'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null],
		'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null],
	];

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		// Nullable relation
		$this->belongsTo('Relations.Users');
	}

	/**
	 * @return \Cake\Database\Schema\TableSchema
	 */
	public function getSchema() {
		$tableSchema = new TableSchema($this->getTable());

		foreach ($this->_fields as $field => $attributes) {
			$tableSchema->addColumn($field, $attributes);
		}

		return $tableSchema;
	}

}
