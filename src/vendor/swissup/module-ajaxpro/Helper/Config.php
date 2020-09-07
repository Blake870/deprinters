<?php

namespace Swissup\Ajaxpro\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const VALIDATION     = 'ajaxpro/main/validation';
    const CART_ENABLE    = 'ajaxpro/main/cart';
    const CART_HANDLE    = 'ajaxpro/main/cartHandle';
    const CART_TYPE      = 'ajaxpro/main/cartType';
    const FLOATING_CART_ENABLE = 'ajaxpro/main/floatingCart';
    const PRODUCT_ENABLE = 'ajaxpro/main/product';
    const OVERRIDE_MINICART = 'ajaxpro/main/overrideMinicart';

    /**
     *
     * @param  string $path
     * @param  string $scope
     * @return string
     */
    public function getConfig($path, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scope);
    }

    /**
     *
     * @param  string $path
     * @param  string $scope
     * @return boolean
     */
    public function isSetFlag($path, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->isSetFlag($path, $scope);
    }

    /**
     *
     * @return bool
     */
    public function isForceValidation()
    {
        return $this->isSetFlag(self::VALIDATION);
    }

    /**
     *
     * @return boolean
     */
    public function isCartViewEnable()
    {
        return $this->isSetFlag(self::CART_ENABLE);
    }

    /**
     *
     * @return string
     */
    public function getCartHandle()
    {
        return $this->getConfig(self::CART_HANDLE);
    }

    /**
     *
     * @return string
     */
    public function getCartDialogType()
    {
        return $this->getConfig(self::CART_TYPE);
    }

    /**
     *
     * @return boolean
     */
    public function isProductViewEnable()
    {
        return $this->isSetFlag(self::PRODUCT_ENABLE);
    }

    /**
     *
     * @return bool
     */
    public function isOverrideMinicart()
    {
        return $this->isSetFlag(self::OVERRIDE_MINICART) &&
            $this->getCartHandle() == 'ajaxpro_popup_minicart';
    }

    /**
     *
     * @return bool
     */
    public function isFloatingCartEnable()
    {
        return $this->isSetFlag(self::FLOATING_CART_ENABLE);
    }
}
