<?php

declare(strict_types=1);

namespace LicenseChecker\Tests\Composer;

use LicenseChecker\Composer\DependencyTree;
use LicenseChecker\Composer\DependencyTreeRetriever;
use LicenseChecker\Dependency;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DependencyTreeTest extends TestCase
{
    /**
     * @var MockObject & DependencyTreeRetriever
     */
    private MockObject $retriever;
    private DependencyTree $dependencyTree;

    protected function setUp(): void
    {
        $this->retriever = $this->createMock(DependencyTreeRetriever::class);
        $this->dependencyTree = new DependencyTree($this->retriever);
    }

    /**
     * @test
     */
    public function itCanParseJsonFromComposer(): void
    {
        $this->retriever->method('getDependencyTree')->willReturn($this->getDependencyTree());

        $dependencies = $this->dependencyTree->getDependencies(false);
        $expected = [
            (new Dependency('direct/dependency'))
                ->addDependency('subdependency/one')
                ->addDependency('subdependency/two')
                ->addDependency('subdependency/three'),
        ];

        $this->assertEquals($expected, $dependencies);
    }

    private function getDependencyTree(): string
    {
        return '{
            "installed": [
                {
                    "name": "direct/dependency",
                    "version": "v0.1",
                    "description": "Some direct dependency",
                    "requires": [
                        {
                            "name": "subdependency/one",
                            "version": "^1.0"
                        },
                        {
                            "name": "subdependency/two",
                            "version": "^2.0",
                            "requires": [
                                {
                                    "name": "subdependency/three",
                                    "version": "^3.0"
                                }
                            ]
                        }
                    ]
                }
            ]
        }';
    }
}
