<?php

namespace GenSys\Unit\Service\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
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

    /** @var MockDependency[] */
    private $mockDependencies = [];

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
        $phpNamespace->addUse(TestCase::class);
        $phpNamespace->addUse(MockObject::class);
        $classType = $phpNamespace->addClass($testClassName);
        $classType->addExtend(TestCase::class);

        $reflectionClass = new ReflectionClass($originalClass);

        $this->addSetupMethod($classType, $reflectionClass);
        $this->addTestMethods($classType, $reflectionClass);

        $this->addMockDependenciesBody($classType, $phpNamespace);

        $this->write($phpNamespace);
    }

    private function addTestMethods(ClassType $classType, ReflectionClass $reflectionClass)
    {
        foreach($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (strpos($reflectionMethod->getName(), '__') !== false) {
                continue;
            }

            $this->addTestMethod($classType, $reflectionMethod);
        }
    }

    public function addTestMethod(ClassType $classType, ReflectionMethod $reflectionMethod)
    {
        $testName = 'test' . ucfirst($reflectionMethod->getName());
        $testMethod =$classType->addMethod($testName);

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterClass = $parameter->getClass();
            if (null !== $parameterClass) {
                $mockDependency = $this->mockDependencyFactory->createFromReflectionParameter($parameter);
                $this->mockDependencies[$parameterClass->getName()] = $mockDependency;
                $testMethod->addBody($mockDependency->getVariableName() . ' = clone ' . $mockDependency->getPropertyCall() . ';');
            }
        }
    }

    private function addMockDependenciesBody(ClassType $classType, PhpNamespace $phpNamespace)
    {
        foreach ($this->mockDependencies as $mockDependency) {
            $dependencyProperty = $classType->addProperty($mockDependency->getPropertyName());
            $dependencyProperty->addComment('@var ' . $mockDependency->getClassName() . '|MockObject');
            $classType->getMethod(self::METHOD_SETUP)->addBody($mockDependency->getBody());
            $phpNamespace->addUse($mockDependency->getFullyQualifiedClassName());
        }
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
     * @param ReflectionClass $reflectionClass
     * @throws ReflectionException
     */
    private function addSetupMethod(ClassType $classType, ReflectionClass $reflectionClass)
    {
        $setUpMethod = $classType->addMethod(self::METHOD_SETUP);
        $this->addMockDependenciesFromClass($reflectionClass);
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return MockDependency[]
     * @throws ReflectionException
     */
    private function addMockDependenciesFromClass(ReflectionClass $reflectionClass)
    {
        foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
            $this->mockDependencies[] = $this->mockDependencyFactory->createFromReflectionParameter($parameter);
        }
    }
}