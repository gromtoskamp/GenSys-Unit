<?php

namespace GenSys\Unit\Service\Generator;

use GenSys\Unit\Resources\Dummy\Object\DummyObject;
use Nette\PhpGenerator\ClassType;
use ReflectionClass;
use ReflectionParameter;
use function str_replace;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\Filesystem\Filesystem;

class NetteGenerator implements GeneratorStrategy
{
    const TEST_FOLDER_PATH = __DIR__. '/../../../tests/unit/';

    const METHOD_SETUP = 'setUp';

    public function createTest(string $originalClass)
    {
        $namespaceArray = explode('\\', $originalClass);

        $testClassName = array_pop($namespaceArray) . 'Test';
        $namespace = implode('\\', $namespaceArray);

        $phpNamespace = new PhpNamespace($namespace);
        $classType = $phpNamespace->addClass($testClassName);

        $this->addSetUpToClassType($classType, $originalClass);

        $namespace = str_replace('\\', '/', $namespace . '/');

        $fileSystem = new Filesystem();
        $fileSystem->mkdir(self::TEST_FOLDER_PATH . $namespace);
        file_put_contents(self::TEST_FOLDER_PATH . $namespace . $testClassName . '.php', "<?php\n\n" . (string) $phpNamespace);
    }

    private function addSetUpToClassType(ClassType $classType, string $fullyQualifiedClassName)
    {
        $setUpMethod = $classType->addMethod(self::METHOD_SETUP);
        $setUpMethod->setBody($this->getSetUpBody($fullyQualifiedClassName));
    }

    private function getSetUpBody(string $fullyQualifiedClassName)
    {
        $reflectionClass = new ReflectionClass($fullyQualifiedClassName);
        $mockDependencies = [];
        foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
            $mockDependencies[] = $this->getMockDependency($parameter);
        }

        return implode("\n", $mockDependencies);
    }

    private function getMockDependency(ReflectionParameter $parameter)
    {
        return '$this->getMockBuilder(\'' . $parameter->getClass()->getName() . '\')->disableOriginalConstructor()->getMock();';
    }
}