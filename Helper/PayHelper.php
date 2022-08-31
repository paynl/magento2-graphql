<?php

namespace Paynl\Graphql\Helper;

class PayHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param string $defaultValue
     * @return mixed|string
     */
    public static function getVersion($defaultValue = '')
    {
        $composerFilePath = sprintf('%s/%s', rtrim(__DIR__, '/'), '../composer.json');
        if (file_exists($composerFilePath)) {
            $composer = json_decode(file_get_contents($composerFilePath), true);
            if (isset($composer['version'])) {
                return $composer['version'];
            }
        }
        return $defaultValue;
    }
}
