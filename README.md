# phpMorphy (reloaded)

===================

[![Latest Stable Version](https://poser.pugx.org/seoservice2020/phpmorphy/version)](https://packagist.org/packages/seoservice2020/phpmorphy)
[![Total Downloads](https://poser.pugx.org/seoservice2020/phpmorphy/downloads)](https://packagist.org/packages/seoservice2020/phpmorphy)
[![tests](https://github.com/seoservice2020/phpmorphy/workflows/tests/badge.svg)](https://github.com/seoservice2020/phpmorphy/actions)
[![codecov](https://codecov.io/gh/seoservice2020/phpmorphy/branch/master/graph/badge.svg)](https://codecov.io/gh/seoservice2020/phpmorphy)
[![License](https://poser.pugx.org/seoservice2020/phpmorphy/license)](https://packagist.org/packages/seoservice2020/phpmorphy)

phpMorphy is morphological analyzer library for Russian, English and German languages.
**This version supports only PHP 7.2, 7.3 and 7.4.**

 * [Website (in Russian)](http://phpmorphy.sourceforge.net/)
 * [Sourceforge project](http://sourceforge.net/projects/phpmorphy)

This library allows to retrieve following morph information for any word:

* base (normal) form;
* all forms;
* grammatical (part of speech, grammems) information.

## Installation

To install the library in your project using `Composer`, first add the following to your `composer.json` config file:

```javascript
{
    "require": {
        "seoservice2020/phpmorphy": "~1.0"
    }
}
```

Then run Composer's install or update commands to complete installation.

## Usage

See examples in [examples](examples) directory.

## Speed

### Single word mode

| mode          | base form       | all forms     | all forms with gram. info |
|:------------- | ---------------:| -------------:| -------------------------:|
| FILE          | 1000            |  800          | 600                       |
| SHM           | 2200            | 1100          | 800                       |
| MEM           | 2500            | 1200          | 900                       |

### Bulk mode

| mode          | base form       | all forms     | all forms with gram. info |
|:------------- | ---------------:| -------------:| -------------------------:|
| FILE          | 1700            | 800           | 700                       |
| SHM           | 3200            | 800           | 700                       |
| MEM           | 3500            | 800           | 700                       |

Note:
> All values are words per second speed.
> Test platform: PHP 5.2.3, AMD Duron 800 with 512Mb memory, WinXP.
