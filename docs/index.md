---
layout: home

hero:
  name: cakephp-ide-helper
  text: IDE Helper for CakePHP
  tagline: Annotations, meta files, code-completion stubs, and Illuminator tasks — boost IDE and static analyzer support, avoid mistakes.
  image:
    src: /logo.svg
    alt: cakephp-ide-helper
  actions:
    - theme: brand
      text: Get Started
      link: /guide/
    - theme: alt
      text: Annotations
      link: /annotations/
    - theme: alt
      text: View on GitHub
      link: https://github.com/dereuromark/cakephp-ide-helper

features:
  - icon: 📝
    title: Annotations Updater
    details: Keep doc blocks and annotations on controllers, models, entities, templates, commands, and more in sync as your code evolves.
  - icon: 🧠
    title: Meta File Generator
    details: Produce `.phpstorm.meta.php` files so PhpStorm and VS Code understand factories, magic strings, and return types.
  - icon: 💡
    title: Code Completion Stubs
    details: IDE-agnostic stub generation for behaviors and SelectQuery generics, so chained ORM calls keep their types.
  - icon: ⚙️
    title: Illuminator Tasks
    details: Rewrite source files where annotations are not enough — for example, generate entity field constants automatically.
  - icon: 🔁
    title: CI and Watcher Friendly
    details: Run as a CI check (`-d --ci`), pre-commit hook, or live file watcher with chokidar or watchexec.
  - icon: 🧱
    title: Extensible
    details: Plug in your own annotators, callback tasks, generator tasks, and Illuminator tasks — replace built-ins or add new ones.
---
