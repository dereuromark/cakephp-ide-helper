<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

/**
 * Optional interface that a class annotator task can implement to declare
 * extra directories that the `bin/cake annotate classes` command should walk
 * in addition to its default `src/` (app + plugin classpaths) and
 * `tests/TestCase/` scans.
 *
 * The intended use is third-party packages whose subjects live outside the
 * conventional source tree — e.g. a test-fixture factory plugin whose
 * subclasses live under `tests/Factory/`. By declaring the path on the
 * task itself, the package does not need to ship its own bake/annotate
 * subcommand: registering the task in `IdeHelper.classAnnotatorTasks` is
 * enough for the existing command to reach the relevant files.
 *
 * `scanPaths()` is `static` so the command can query a task's paths
 * without first instantiating it with an `Io` and per-file content. Paths
 * are project-root relative for app context, and plugin-root relative
 * when the command is run with `-p <plugin>` (or `-p all`). Paths are
 * walked recursively.
 *
 * Convention: return paths with forward slashes and a trailing slash
 * (e.g. `'tests/Factory/'`), independent of OS. The command normalizes
 * to the OS-native separator before walking, so the dedup key is stable
 * when two tasks declare the same path.
 */
interface PathAwareClassAnnotatorTaskInterface extends ClassAnnotatorTaskInterface {

	/**
	 * @return array<string>
	 */
	public static function scanPaths(): array;

}
