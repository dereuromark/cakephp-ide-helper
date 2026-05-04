# Migrating from 4.x

A few commands were renamed when moving to 5.x:

| 4.x | 5.x |
|-----|-----|
| `bin/cake code_completion generate` | `bin/cake generate code_completion` |
| `bin/cake phpstorm generate` | `bin/cake generate phpstorm` |
| `bin/cake illuminator` | `bin/cake illuminate code` |

If you have composer scripts or CI jobs referencing the old commands, update
them accordingly.
