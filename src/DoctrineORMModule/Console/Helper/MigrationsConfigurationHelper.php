<?php

declare(strict_types=1);

namespace DoctrineORMModule\Console\Helper;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelperInterface;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Throwable;

use function strrpos;
use function substr;

class MigrationsConfigurationHelper implements
    HelperInterface,
    ConfigurationHelperInterface
{
    /** @var HelperSet */
    protected $helperSet;

    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setHelperSet(?HelperSet $helperSet = null): MigrationsConfigurationHelper
    {
        $this->helperSet = $helperSet;

        return $this;
    }

    public function getHelperSet(): ?HelperSet
    {
        return $this->helperSet;
    }

    public function getName(): string
    {
        return 'configuration';
    }

    public function getMigrationConfig(InputInterface $input): Configuration
    {
        $objectManagerAlias = $input->getOption('object-manager') ?: 'doctrine.entitymanager.orm_default';

        // Copied from DoctrineModule/ServiceFactory/AbstractDoctrineServiceFactory
        preg_match(
            '/^doctrine\.((?<mappingType>orm|odm)\.|)(?<serviceType>[a-z0-9_]+)\.(?<serviceName>[a-z0-9_]+)$/',
            $objectManagerAlias,
            $matches
        );

        return $this->container->get('doctrine.migrations_configuration.' . $matches['serviceName']);
    }
}
