<?php

namespace GenSys\Unit\Service\Generator;

use function str_replace;

use GenSys\Unit\Factory\MockDependencyFactory;
use GenSys\Unit\Model\MockDependency;
use Nette\PhpGenerator\ClassType;
use ReflectionClass;
use ReflectionException;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\Filesystem\Filesystem;

class NetteGenerator implements GeneratorStrategy
{
    const TEST_FOLDER_PATH = __DIR__. '/../../../tests/unit/';
    const METHOD_SETUP = 'setUp';
    const EXTEND_TESTCASE = 'PHPUnit\Framework\TestCase';

    /** @var MockDependencyFactory */
    private $mockDependencyFactory;

    /**
     * NetteGenerator constructor.
     */
    public function __construct()
    {
        $this->mockDependencyFactory = new MockDependencyFactory();
    }

    /**
     * @param string $originalClass
     * @throws ReflectionException
     */
    public function createTest(string $originalClass)
    {
        $namespaceArray = explode('\\', $originalClass);
        $testClassName = array_pop($namespaceArray) . 'Test';
        $namespace = implode('\\', $namespaceArray);

        $phpNamespace = new PhpNamespace($namespace);
        $phpNamespace->addUse(self::EXTEND_TESTCASE);
        $classType = $phpNamespace->addClass($testClassName);
        $classType->addExtend('PHPUnit\Framework\TestCase');

        $this->addSetupMethod($classType, $originalClass);

        $this->write($phpNamespace);
    }

    /**
     * @param PhpNamespace $phpNamespace
     */
    private function write(PhpNamespace $phpNamespace)
    {
        $namespace = str_replace('\\', '/', $phpNamespace->getName() . '/');
        $testFolder = self::TEST_FOLDER_PATH . $namespace;

        if (!file_exists($testFolder)) {
            $fileSystem = new Filesystem();
            $fileSystem->mkdir($testFolder);
        }

        foreach ($phpNamespace->getClasses() as $classType) {
            file_put_contents($testFolder . $classType->getName() . '.php', "<?php\n\n" . (string) $phpNamespace);
        }
    }

    /**
     * @param ClassType $classType
     * @param string $fullyQualifiedClassName
     * @throws ReflectionException
     */
    private function addSetupMethod(ClassType $classType, string $fullyQualifiedClassName)
    {
        $setUpMethod = $classType->addMethod(self::METHOD_SETUP);
        $mockDependencies = $this->getMockDependencies($fullyQualifiedClassName);
        foreach ($mockDependencies as $mockDependency) {
            $dependencyProperty = $classType->addProperty($mockDependency->getPropertyName());
            $dependencyProperty->addComment('@var ' . $mockDependency->getFullyQualifiedClassName());
            $setUpMethod->addBody($mockDependency->getBody());
        }
    }

    /**
     * @param string $fullyQualifiedClassName
     * @return MockDependency[]
     * @throws ReflectionException
     */
    private function getMockDependencies(string $fullyQualifiedClassName)
    {
        $reflectionClass = new ReflectionClass($fullyQualifiedClassName);
        $mockDependencies = [];
        foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
            $mockDependencies[] = $this->mockDependencyFactory->createFromReflectionParameter($parameter);
        }

        return $mockDependencies;
    }
}