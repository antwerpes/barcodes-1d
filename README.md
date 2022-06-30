<p align="center">
	<img src="./.art/logo.png" alt="Logo"/>
	<br><br>
	<a href="https://packagist.org/packages/antwerpes/barcodes-1d"><img src="https://img.shields.io/packagist/v/antwerpes/barcodes-1d.svg?style=flat-square" alt="Packagist"/></a>
	<a href="https://github.com/antwerpes/barcodes-1d/actions?query=workflow%3Alint+branch%3Amaster"><img src="https://img.shields.io/github/workflow/status/antwerpes/barcodes-1d/lint?label=code%20style" alt="Code Style"/></a>
	<a href="https://packagist.org/packages/antwerpes/barcodes-1d"><img src="https://img.shields.io/packagist/dt/antwerpes/barcodes-1d.svg?style=flat-square" alt="Downloads"></a>
	<a href="https://github.com/antwerpes/barcodes-1d/blob/master/LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"/></a>
</p>

# Barcodes 1D

Barcode generator written in PHP for encoding and rendering most common barcode formats to multiple image output formats.

## Features

- Supports most common barcode formats (Code128, EAN, UPC, ...)
- Can render to SVG, PNG, JPG or WEBP using GD
- Images (PNG/JPG/WEBP) may be configured to scale up to any size
- Supports common customization requirements (size, color, text display, font size, background color, font for image rendering, or format-specific options such as guard bars or quiet zones)

## Supported barcodes
| Format                  | Variations                     |
| ----------------------- | ------------------------------ |
| Code 128                | Auto-Mode and forced A/B/C     |
| EAN                     | EAN-2 / EAN-5 / EAN-8 / EAN-13 |
| UPC                     | UPC-A / UPC-E                  |
| MSI                     | Mod10, Mod11, Mod1010, Mod1110 |
| Codabar                 |                                |
| Code 11                 |                                |
| Code 2 of 5             |                                |
| Code 2 of 5 Interleaved |                                |
| Code 39                 |                                |
| Code 93                 |                                |
| ITF-14                  |                                |
| Pharmacode              |                                |

## Installation

Install the package via composer:

```bash
composer require antwerpes/barcodes-1d
```

## Usage

Use the `Barcodes` factory class to create an encoder and render its output to SVG or a base64-encoded image:

```php
use Antwerpes\Barcodes\Barcodes;
use Antwerpes\Barcodes\Barcodes\Common\BarcodeFormat;

// SVG
$svg = Barcodes::create('12345678', BarcodeFormat::CODE_128)->toSVG();
file_put_contents('img.svg', $svg);

// Or as an image
$image = Barcodes::create('12345678', BarcodeFormat::CODE_128)->toImage();
file_put_contents('img.png', base64_decode($image));

// Example for scaled up (4x) WEBP
$image = Barcodes::create('12345678', BarcodeFormat::CODE_128, [
    'image_format' => 'webp',
    'image_scale' => 4,
])->toImage();
file_put_contents('img.webp', base64_decode($image));
```

### Customization

Check out the [options](#options) for an overview of all customization options.

## Examples
#### Code 128
![Code 128](./.art/code128.svg)
```php
Barcodes::create('A12345', BarcodeFormat::CODE_128)->toSVG();
```
#### EAN-13
![EAN-13](./.art/ean13.svg) ![EAN-13 Flat](./.art/ean13-flat.svg) ![EAN-13 Quiet Zone](./.art/ean13-quiet-zone.svg)
```php
Barcodes::create('5901234123457', BarcodeFormat::EAN_13)->toSVG();
Barcodes::create('5901234123457', BarcodeFormat::EAN_13, ['flat' => true])->toSVG();
Barcodes::create('5901234123457', BarcodeFormat::EAN_13, ['with_quiet_zone' => true])->toSVG();
```
#### EAN-2 / EAN-5 / EAN-8
![EAN-2](./.art/ean2.svg) ![EAN-5](./.art/ean5.svg) ![EAN-8](./.art/ean8.svg)
```php
Barcodes::create('53', BarcodeFormat::EAN_2)->toSVG();
Barcodes::create('52495', BarcodeFormat::EAN_5)->toSVG();
Barcodes::create('96385074', BarcodeFormat::EAN_8)->toSVG();
```
#### UPC-A / UPC-E
![UPC-A](./.art/upca.svg) ![UPC-E](./.art/upce.svg)
```php
Barcodes::create('123456789999', BarcodeFormat::UPC_A)->toSVG();
Barcodes::create('01245714', BarcodeFormat::UPC_E)->toSVG();
// These also work and will produce the same result
Barcodes::create('124571', BarcodeFormat::UPC_E)->toSVG();
Barcodes::create('01210000457', BarcodeFormat::UPC_E)->toSVG();
Barcodes::create('012100004574', BarcodeFormat::UPC_E)->toSVG();
```
#### Codabar
![Codabar](./.art/codabar.svg)
```php
Barcodes::create('A12345B', BarcodeFormat::CODABAR)->toSVG();
```
#### Code 11
![Code 11](./.art/code11.svg)
```php
Barcodes::create('01234-5678', BarcodeFormat::CODE_11)->toSVG();
```
#### Code 2 of 5 / Code 2 of 5 Interleaved / ITF-14
![Code 25](./.art/code25.svg) ![Code 25 Interleaved](./.art/code25i.svg) ![ITF-14](./.art/itf14.svg)
```php
Barcodes::create('1234567', BarcodeFormat::CODE_25)->toSVG();
Barcodes::create('12345670', BarcodeFormat::CODE_25_INTERLEAVED)->toSVG();
Barcodes::create('98765432109213', BarcodeFormat::ITF_14)->toSVG();
```
#### Code 39 / Code 93
![Code 39](./.art/code39.svg) ![Code 93](./.art/code93.svg)
```php
Barcodes::create('AB12', BarcodeFormat::CODE_39)->toSVG();
Barcodes::create('TEST93', BarcodeFormat::CODE_93)->toSVG();
```
#### MSI
![MSI](./.art/msi.svg)
```php
Barcodes::create('8052', BarcodeFormat::MSI)->toSVG();
```
#### Pharmacode
![MSI](./.art/pharmacode.svg)
```php
Barcodes::create('1234', BarcodeFormat::PHARMACODE)->toSVG();
```

## Options

Generic options for all barcode formats:

| Option          | Default          | Type          | Comment                                                      |
| --------------- | ---------------- | ------------- | ------------------------------------------------------------ |
| `width`         | `2`              | `int`         | Width of a single bar                                        |
| `height`        | `100`            | `int`         | Height of a regular bar                                      |
| `text_margin`   | `2`              | `int`         | Distance between barcode and text                            |
| `text_align`    | `center`         | `string`      | `left` / `center` / `right`                                  |
| `background`    | `#ffffff`        | `string\|null` | Background color. Use `null` for transparent background      |
| `color`         | `#000000`        | `string`      | Color of the barcode and text                                |
| `text_color`    | –                | `string`      | Text color. Falls back to `color` if not set                 |
| `margin`        | `10`             | `int`         | Margin on all sides of the barcode                           |
| `margin_top`    | –                | `int`         | Falls back to `margin` if not set                            |
| `margin_right`  | –                | `int`         | Falls back to `margin` if not set                            |
| `margin_bottom` | –                | `int`         | Falls back to `margin` if not set                            |
| `margin_left`   | –                | `int`         | Falls back to `margin` if not set                            |
| `display_value` | `true`           | `bool`        | Should the code text be displayed below the barcode?         |
| `font_size`     | `20`             | `int`         | Large font sizes may break the layout                        |
| `image_format`  | `png`            | `string`      | `png` / `jpg` / `webp`. Only used when rendering to an image. |
| `image_font`    | `JetBrains Mono` | `string`      | Path to custom font TTF for image rendering (not used for SVG). |
| `image_scale` | `1` | `int` | Only for image-rendering. Sets the final image scale (e.g. 2x or 4x) |

There are additional options for some barcode formats:

**Code128**

| Option | Default | Type     | Comment                                                      |
| ------ | ------- | -------- | ------------------------------------------------------------ |
| `mode` | `AUTO`  | `string` | Encoding mode (`AUTO` / `A`/ `B`/ `C`). Use `AUTO` for most efficient encoding. |

**EAN-8 / EAN-13 / UPC-A / UPC-E**

| Option            | Default | Type   | Comment                                                      |
| ----------------- | ------- | ------ | ------------------------------------------------------------ |
| `flat`            | `false` | `bool` | Flat encoding (without the guard bars)                       |
| `with_quiet_zone` | `false` | `bool` | Enables quiet zones with (`<` and/or `>` symbols). Only relevant for EAN-8 / EAN-13 |

**Code39**

| Option            | Default | Type   | Comment                             |
| ----------------- | ------- | ------ | ----------------------------------- |
| `enable_checksum` | `false` | `bool` | Calculate and append mod43 checksum |
| `full_ascii`      | `false` | `bool` | Enable full ASCII mode              |

**Code39**

| Option       | Default | Type   | Comment                |
| ------------ | ------- | ------ | ---------------------- |
| `full_ascii` | `false` | `bool` | Enable full ASCII mode |

**MSI**

| Option        | Default | Type          | Comment                                       |
| ------------- | ------- | ------------- | --------------------------------------------- |
| `check_digit` | `null`  | `string|null` | `MOD_10` / `MOD_11` / `MOD_1010` / `MOD_1110` |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
