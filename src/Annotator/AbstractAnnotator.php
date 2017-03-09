<?php
namespace IdeHelper\Annotator;

use Cake\Core\InstanceConfigTrait;
use IdeHelper\Console\Io;
use PHP_CodeSniffer;
use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Fixer;

/**
 */
abstract class AbstractAnnotator {

	use InstanceConfigTrait;

	const CONFIG_DRY_RUN = 'dry-run';
	const CONFIG_PLUGIN = 'plugin';
	const CONFIG_NAMESPACE = 'namespace';

	/**
	 * @var \Cake\Console\ConsoleIo
	 */
	protected $_io;

	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		self::CONFIG_PLUGIN => null,
	];

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array $config
	 */
	public function __construct(Io $io, array $config) {
		$this->_io = $io;
		$this->setConfig($config);

		$namespace = $this->getConfig(static::CONFIG_PLUGIN) ?: 'App';
		$namespace = str_replace('/', '\\', $namespace);
		$this->setConfig(static::CONFIG_NAMESPACE, $namespace);
	}

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	abstract public function annotate($path);

	/**
	 * @param string $file
	 *
	 * @return \PHP_CodeSniffer_File
	 */
	protected function _getFile($file) {
		$_SERVER['argv'] = [];

		$phpcs = new PHP_CodeSniffer();
		$phpcs->process([], null, []);
		return new PHP_CodeSniffer_File($file, [], [], $phpcs);
	}

	/**
	 * @param string $path
	 * @param string $contents
	 * @return void
	 */
	protected function _storeFile($path, $contents) {
		if ($this->config(static::CONFIG_DRY_RUN)) {
			return;
		}dd($this->config());
		file_put_contents($path, $contents);
	}

	/**
	 * @return \PHP_CodeSniffer_Fixer
	 */
	protected function _getFixer() {
		return new PHP_CodeSniffer_Fixer();
	}

}
