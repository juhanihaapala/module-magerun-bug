# Module to reproduce magerun2 issue [#455](https://github.com/netz98/n98-magerun2/issues/455)

- when running `php bin/magento codaone:magerun:productsave` product is saved and no errors
- when running `magerun2 codaone:magerun:productsave` an error `Database lock wait timeout exceeded` is thrown
