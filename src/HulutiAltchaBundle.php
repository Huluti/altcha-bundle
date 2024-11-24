<?php

declare(strict_types=1);

namespace Huluti\AltchaBundle;

use Huluti\AltchaBundle\HulutiAltchaBundleCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class HulutiAltchaBundle extends AbstractBundle
{
    protected string $extensionAlias = 'huluti_altcha';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new HulutiAltchaBundleCompilerPass());
    }
    
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->booleanNode('enable')->defaultTrue()->end()
                ->booleanNode('floating')->defaultFalse()->end()
                ->scalarNode('hmacKey')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // load an XML, PHP or YAML file
        $container->import('../config/services.yml');
    }
}
