<?php
namespace App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Symfony\DependencyInjection\Compiler\ExportableClassPass;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ExportExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Charge le fichier exporting.yaml
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('exporting.yaml');

        // Récupère et fusionne la configuration personnalisée (si tu veux créer un Configuration.php)
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Injecte la config comme paramètre de container
        $container->setParameter('exporting_sub_bundle', $config);
        $container->addCompilerPass(new ExportableClassPass());
    }
}
