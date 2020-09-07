# Argento Mall theme editor

Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-theme-editor-argento-mall --prefer-source
bin/magento module:enable Swissup_Core Swissup_ThemeEditor Swissup_ThemeEditorArgentoMall
bin/magento setup:upgrade
```
