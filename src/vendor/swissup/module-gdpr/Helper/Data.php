<?php

namespace Swissup\Gdpr\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_ENABLED = 'swissup_gdpr/general/enabled';

    /**
     * @var string
     */
    const CONFIG_PATH_CONSENTS = 'swissup_gdpr/consents';

    /**
     * @var string
     */
    const CONFIG_PATH_ANONYMIZATION_PLACEHOLDER = 'swissup_gdpr/request/delete_data/placeholder';

    /**
     * Check if module is enabled
     *
     * @return boolean
     */
    public function isGdprEnabled()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get all consents
     *
     * @return array
     */
    public function getConsents()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_CONSENTS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get placeholoder for anonymized data
     *
     * @return string
     */
    public function getAnonymizationPlaceholder()
    {
        return $this->getConfigValue(self::CONFIG_PATH_ANONYMIZATION_PLACEHOLDER);
    }

    /**
     * Get specific config value
     *
     * @param  string $path
     * @param  string $scope
     * @return string
     */
    public function getConfigValue($path, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scope);
    }
}
