<?php
namespace PublicUHC\TeamspeakAuth;

use Symfony\Component\Templating\Helper\AssetsHelper;

class AssetExtension extends \Twig_Extension
{
    private $assetHelper;

    public function __construct(AssetsHelper $assetsHelper)
    {
        $this->assetHelper = $assetsHelper;
    }

    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('asset', array($this, 'getAssetUrl'))];
    }

    public function getAssetUrl($path, $packageName = null)
    {
        return $this->assetHelper->getUrl($path, $packageName);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'assets';
    }
}
