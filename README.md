# ShipperHQShipper

This module for Magento 2 is to be used with ShipperHQ Shipper module.

This adds the functionality to have a products Options determined the dimensions sent to ShipperHQ for use with dimensional rules.

To use, turn on the product detail page, under the ShipperHQ dimensions section.
Fill in the text fields like the following:

{Height}

Where Height is the "Title" of the option.

You may use formulas, like when padding is added around the item.

{Height} + 1

You may use conditionals.

({Height} <10 ? 10 : {Height} + 1)
