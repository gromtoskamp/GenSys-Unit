<?php

namespace GenSys\Unit\Service\Generator;

use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\Filesystem\Filesystem;

class NetteGenerator implements GeneratorStrategy
{
    const TEST_FOLDER_PATH = __DIR__. '/../../../tests/unit/';

    const METHOD_SETUP = 'setUp';

    public function createTest($fullyQualifiedClassName)
    {
        $namespaceArray = explode('\\', $fullyQualifiedClassName);

        $className = array_pop($namespaceArray);
        $namespace = implode('\\', $namespaceArray);

        $phpNamespace = new PhpNamespace($namespace);
        $classType = $phpNamespace->addClass('Rest');

        $setUpMethod = $classType->addMethod(self::METHOD_SETUP);
        $setUpMethod->addBody("echo 'test';\nexit;");

        $namespace = \str_replace('\\', '/', $namespace . '/');

        $fileSystem = new Filesystem();
        $fileSystem->mkdir(self::TEST_FOLDER_PATH . $namespace);
        file_put_contents(self::TEST_FOLDER_PATH . $namespace . $className . '.php', "<?php\n\n" . (string) $phpNamespace);
    }
}