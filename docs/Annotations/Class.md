## Custom Class Annotators

The following examples are showcasing how to write custom class annotators
in your CakePHP application.

### GetByUuid()
Imagine you have a method `getByUuid()` for all your tables to look up
records not by their AIID, but by their UUID.
The returned result would then be "known" by IDE/PHPStan as `EntityInterface`.

You can create a custom annotator to add a concrete entity annotation automatically.
It adds an inline `@var` annotation above each `getByUuid()` method assignment.

The following example uses PHP AST to parse the class files and find the relevant method calls.
This is more exact than using regex or PHPCS tokenizing, with less false positives.

It then uses the existing IdeHelper code modifier that leverages the PHPCS tokenizing to
add the inline annotation to the class file using `annotateInlineContent()`.

```php
<?php

namespace App\Annotator\ClassAnnotatorTask;

use Cake\ORM\TableRegistry;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\VariableAnnotation;
use IdeHelper\Annotator\ClassAnnotatorTask\AbstractClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

/**
 * Usage of getByUuid() should have inline annotations added.
 */
class TableGetAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface
{
    /**
     * @param string $path
     * @param string $content
     *
     * @return bool
     */
    public function shouldRun(string $path, string $content): bool
    {
        if (!str_contains($path, DS . 'src' . DS)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function annotate(string $path): bool
    {
        $found = $this->findGetByUuidCalls();

        $result = true;
        foreach ($found as $row) {
            $annotations = [
                AnnotationFactory::createOrFail(
                    VariableAnnotation::TAG,
                    $row['entity'],
                    $row['var'],
                ),
            ];
            $result &= $this->annotateInlineContent($path, $this->content, $annotations, $row['line']);
        }

        return $result;
    }

    protected function findGetByUuidCalls(): array
    {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $ast = $parser->parse($this->content);

        $found = [];

        $traverser = new NodeTraverser();

        $traverser->addVisitor(new class ($found) extends NodeVisitorAbstract {
            protected array $found;

            /**
             * @param array $found
             */
            public function __construct(array &$found)
            {
                $this->found = &$found;
            }

            /**
             * @param \PhpParser\Node $node
             *
             * @return null
             */
            public function enterNode(Node $node): null
            {
                if (
                    $node instanceof Assign &&
                    $node->expr instanceof MethodCall &&
                    $node->expr->name instanceof Identifier &&
                    $node->expr->name->toString() === 'getByUuid'
                ) {
                    $varName = $node->var instanceof Variable ? $node->var->name : null;

                    $caller = $node->expr->var;
                    if (
                        $caller instanceof PropertyFetch &&
                        $caller->var instanceof Variable &&
                        $caller->var->name === 'this' &&
                        $caller->name instanceof Identifier
                    ) {
                        $modelName = $caller->name->toString();
                        $entityClass = TableRegistry::getTableLocator()->get($modelName)->getEntityClass();

                        $this->found[] = [
                            'line' => $node->getStartLine(),
                            'var' => '$' . $varName,
                            'entity' => '\\' . $entityClass,
                        ];
                    }
                }

                return null;
            }
        });

        $traverser->traverse($ast);

        return $found;
    }
}
```

