import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'cakephp-ide-helper',
  description: 'Annotations, meta files, code-completion, and Illuminator tasks for CakePHP — boost IDE and static analyzer support.',
  base: '/cakephp-ide-helper/',
  head: [
    ['link', { rel: 'icon', href: '/cakephp-ide-helper/favicon.svg', type: 'image/svg+xml' }],
  ],
  themeConfig: {
    logo: '/logo.svg',
    nav: [
      { text: 'Guide', link: '/guide/', activeMatch: '/guide/' },
      { text: 'Annotations', link: '/annotations/', activeMatch: '/annotations/' },
      { text: 'Code Completion', link: '/code-completion/', activeMatch: '/code-completion/' },
      { text: 'Generator', link: '/generator/', activeMatch: '/generator/' },
      { text: 'Illuminator', link: '/illuminator/', activeMatch: '/illuminator/' },
      { text: 'Reference', link: '/reference/configuration', activeMatch: '/reference/' },
      {
        text: 'Links',
        items: [
          { text: 'GitHub', link: 'https://github.com/dereuromark/cakephp-ide-helper' },
          { text: 'Packagist', link: 'https://packagist.org/packages/dereuromark/cakephp-ide-helper' },
          { text: 'Issues', link: 'https://github.com/dereuromark/cakephp-ide-helper/issues' },
          { text: 'Wiki', link: 'https://github.com/dereuromark/cakephp-ide-helper/wiki' },
          { text: 'IdeHelperExtra', link: 'https://github.com/dereuromark/cakephp-ide-helper-extra' },
        ],
      },
    ],
    sidebar: {
      '/guide/': [
        {
          text: 'Guide',
          items: [
            { text: 'Introduction', link: '/guide/' },
            { text: 'Installation', link: '/guide/installation' },
            { text: 'Usage', link: '/guide/usage' },
            { text: 'IDE Support', link: '/guide/ide-support' },
            { text: 'Migrating from 4.x', link: '/guide/migration' },
          ],
        },
      ],
      '/annotations/': [
        {
          text: 'Annotations',
          items: [
            { text: 'Overview', link: '/annotations/' },
            { text: 'Controllers', link: '/annotations/controllers' },
            { text: 'Models', link: '/annotations/models' },
            { text: 'View, Components, Helpers', link: '/annotations/view' },
            { text: 'Templates', link: '/annotations/templates' },
            { text: 'Commands and Routes', link: '/annotations/commands' },
            { text: 'Classes', link: '/annotations/classes' },
            { text: 'Callbacks', link: '/annotations/callbacks' },
            { text: 'Operations', link: '/annotations/operations' },
            { text: 'Custom Class Annotators', link: '/annotations/custom-class-annotator' },
          ],
        },
      ],
      '/code-completion/': [
        {
          text: 'Code Completion',
          items: [
            { text: 'Overview', link: '/code-completion/' },
          ],
        },
      ],
      '/generator/': [
        {
          text: 'Generator',
          items: [
            { text: 'Overview', link: '/generator/' },
            { text: 'Available Tasks', link: '/generator/tasks' },
            { text: 'Custom Tasks and Directives', link: '/generator/custom-tasks' },
            { text: 'Operations', link: '/generator/operations' },
          ],
        },
      ],
      '/illuminator/': [
        {
          text: 'Illuminator',
          items: [
            { text: 'Overview', link: '/illuminator/' },
          ],
        },
      ],
      '/reference/': [
        {
          text: 'Reference',
          items: [
            { text: 'Configuration', link: '/reference/configuration' },
            { text: 'Contributing', link: '/reference/contributing' },
          ],
        },
      ],
    },
    socialLinks: [
      { icon: 'github', link: 'https://github.com/dereuromark/cakephp-ide-helper' },
    ],
    search: {
      provider: 'local',
    },
    editLink: {
      pattern: 'https://github.com/dereuromark/cakephp-ide-helper/edit/master/docs/:path',
      text: 'Edit this page on GitHub',
    },
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright Mark Scherer',
    },
  },
})
