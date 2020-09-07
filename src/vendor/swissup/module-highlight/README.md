# Highlight

## Installation

Looking for a release installation instuctions?
[See extension manual](http://documentation.swissuplabs.com/m2/highlight/).

#### Composer based installation

Run the following commands:

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-highlight --prefer-source
git checkout master
bin/magento module:enable Swissup_Core Swissup_Highlight
bin/magento setup:upgrade
```
