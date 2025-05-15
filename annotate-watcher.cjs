// watcher.js
const chokidar = require('chokidar');
const { exec } = require('child_process');

// Helper to parse CLI arguments
function getPathsFromArgs() {
	const arg = process.argv.find(arg => arg.startsWith('--path='));
	if (!arg) return ['src/', 'templates/']; // default path
	const value = arg.split('=')[1];
	return value.split(',').map(p => p.trim()).filter(Boolean);
}

const watchPaths = getPathsFromArgs();
console.log(`🔍 Watching: ${watchPaths.join(', ')}`);

// Initialize watcher
chokidar.watch(watchPaths, {
	ignored: /(^|[\/\\])\../, // ignore dotfiles
	persistent: true,
})
	.on('change', path => {
		if (!path.endsWith('.php')) return; // skip non-PHP files

		console.log(`📝 File changed: ${path}`);

		// Run CakePHP command
		exec('bin/cake annotate all --file ' + `${path}`, (error, stdout, stderr) => {
			if (error) {
				console.error(`❌ Error executing CakePHP command: ${error.message}`);
				return;
			}
			if (stderr) {
				console.error(`⚠️ STDERR: ${stderr}`);
			}
			console.log(`✅ Output:\n${stdout}`);
		});
	});
