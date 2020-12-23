# phpMorphy (reloaded)

[![Latest Stable Version](https://poser.pugx.org/seoservice2020/phpmorphy/version)](https://packagist.org/packages/seoservice2020/phpmorphy)
[![Total Downloads](https://poser.pugx.org/seoservice2020/phpmorphy/downloads)](https://packagist.org/packages/seoservice2020/phpmorphy)
[![tests](https://github.com/seoservice2020/phpmorphy/workflows/tests/badge.svg)](https://github.com/seoservice2020/phpmorphy/actions)
[![codecov](https://codecov.io/gh/seoservice2020/phpmorphy/branch/master/graph/badge.svg)](https://codecov.io/gh/seoservice2020/phpmorphy)
[![License](https://poser.pugx.org/seoservice2020/phpmorphy/license)](https://packagist.org/packages/seoservice2020/phpmorphy)

phpMorphy is morphological analyzer library for Russian, Ukrainian, English and German languages.

**This version supports only PHP 7.2, 7.3 and 7.4.**

* [Website (in Russian)](http://phpmorphy.sourceforge.net/)
* [Sourceforge project](http://sourceforge.net/projects/phpmorphy)

This library allows to retrieve following morph information for any word:

* base (normal) form;
* all forms;
* grammatical (part of speech, grammems) information.

## Installation

Run the following command from your terminal:

```bash
composer require seoservice2020/phpmorphy
```

Or add this to require section in your `composer.json` file:

```json
{
    "require": {
        "seoservice2020/phpmorphy": "~2.2"
    }
}
```

then run ```composer update```

## Usage

See examples in [examples](examples) directory.

## Building dictionaries

To build your dictionary from one of the sources:

1) Create an XML file from dictionary source native format, e.g. for AOT, use `bin/dict-processing/convert-mrd2xml.php` script:

    ```bash
    php bin/dict-processing/convert-mrd2xml.php path/to/aot/dict/file.mwz path/to/otput/
    ```

    Also for Russian language, you may use `bin/dict-processing/convert-russian-jo.php` to convert XML with Russian dictionary into format without `ё` letter.

2) Build phpMorphy dictionaries files using `bin/dict-build/build-dict.php`:

    At now package has some morphy builder tool for Windows (see `bin/morph-builder/` folder), but you can specify your own morphy builder tool version.
    **Important! Morphy builder executable should be in `bin/morphy_builder.exe` file.**

    You may need to provide source-specific data for script, e.g. for [AOT](http://aot.ru/) you will need to provide path to [AOT sources](https://github.com/sokirko74/aot) root.

    Both morphy builder path and AOT path arguments are optional. As it was before, you also may provide environment variables:

    * `MORPHY_DIR` - morphy builder tool root path
    * `RML` - AOT sources root path

    Environment variables are checked first for backward compatibility.

    Example:

    ```bash
    php bin/dict-build/build-dict.php path/to/xml/ru_RU.xml path/to/otput/ utf-8 1 1 path/to/morphy/builder/root/folder/ path/to/aot/root/folder
    ```

## Speed (DEPRECATED)

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
