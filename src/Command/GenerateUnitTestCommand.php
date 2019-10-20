<?php

namespace GenSys\Unit\Command;

use GenSys\Unit\Service\GenerateUnitTestService;
use GenSys\Unit\Service\Generator\NetteGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateUnitTestCommand extends Command
{
    protected static $defaultName = 'gensys:unit';

    public function configure()
    {
        $this->setDescription('Generate a Unit Test boilerplate for given classname.');
        $this->addArgument('className', InputArgument::REQUIRED, 'Name of the class to generate a Unit Test for.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('className');
        $generateUnitTestService = new GenerateUnitTestService(new NetteGenerator());
        $generateUnitTestService->generateUnitTest($className);
        $output->writeln('test');
    }
}
