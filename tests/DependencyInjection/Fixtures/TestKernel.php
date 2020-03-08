<?php

namespace Doctrine\Bundle\DoctrineBundle\Tests\DependencyInjection\Fixtures;

use Doctrine\Bundle\DBALBundle\DoctrineDBALBundle;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /** @var string|null */
    private $projectDir;

    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles() : iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineDBALBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(static function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', ['secret' => 'F00']);

            $container->loadFromExtension('doctrine_dbal', [
                ['driver' => 'pdo_sqlite'],
            ]);

            // Register a NullLogger to avoid getting the stderr default logger of FrameworkBundle
            $container->register('logger', NullLogger::class);
        });
    }

    public function getProjectDir() : string
    {
        if ($this->projectDir === null) {
            $this->projectDir = sys_get_temp_dir() . '/sf_kernel_' . md5(mt_rand());
        }

        return $this->projectDir;
    }

    public function getRootDir() : string
    {
        return $this->getProjectDir();
    }
}
