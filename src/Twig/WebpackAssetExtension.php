<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WebpackAssetExtension extends AbstractExtension
{
    private string $manifestPath;

    public function __construct(ParameterBagInterface $params)
    {
        $this->manifestPath = $params->get('kernel.project_dir') . '/public/build/manifest.json';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('webpack_asset', [$this, 'getAssetPath']),
        ];
    }

    public function getAssetPath(string $asset): string
    {
        if (!file_exists($this->manifestPath)) {
            throw new \RuntimeException('Webpack manifest file not found.');
        }

        $manifest = json_decode(file_get_contents($this->manifestPath), true);

        if (!isset($manifest[$asset])) {
            throw new \InvalidArgumentException(sprintf('Asset "%s" not found in manifest.json.', $asset));
        }

        return $manifest[$asset];
    }
}
