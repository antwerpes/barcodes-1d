# Changelog

All notable changes to `antwerpes/barcodes-1d` will be documented in this file.

## 1.1.0 - 2022-06-30

- Updated `Barcodes::create()` factory signature.

```php
// Before
Barcodes::create('A12345', Format::CODE_128, ['mode' => 'A'])->toSVG();
// After
Barcodes::create('A12345', Format::CODE_128, 'svg', ['mode' => 'A']);
```

## 1.0.0 - 2022-06-30

- Initial release

