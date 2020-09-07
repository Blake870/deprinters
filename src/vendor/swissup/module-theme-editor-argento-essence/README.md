# Argento Essence theme styles

## Installation

### For clients

Please do not install this module. It will be installed automatically as a dependency.

### For developers

Use this approach if you have access to our private repositories!

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-theme-editor-argento-essence --prefer-source
bin/magento module:enable Swissup_Core Swissup_ThemeEditor Swissup_ThemeEditorArgentoEssence
bin/magento setup:upgrade
```
