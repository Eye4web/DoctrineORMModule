<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use DoctrineORMModule\Console\Loader;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

use function class_exists;
use function strtolower;
use function ucfirst;

/**
 * Service factory for migrations command
 */
class MigrationsCommandFactory implements FactoryInterface
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = ucfirst(strtolower($name));
    }

    /**
     * {@inheritDoc}
     *
     * @return AbstractCommand
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $className = 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command';

        if (! class_exists($className)) {
            throw new InvalidArgumentException();
        }

        $config = $container->get('config');

        return new $className(
            DependencyFactory::fromEntityManager(
                new Loader\MigrationsConfiguration($config['doctrine']['migrations_configuration']),
                new Loader\MigrationsEntityManager($container)
            )
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createService(ServiceLocatorInterface $container): DoctrineCommand
    {
        return $this($container, 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command');
    }
}
