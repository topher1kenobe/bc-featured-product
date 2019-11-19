<!-- DO NOT EDIT THIS FILE; it is auto-generated from readme.txt -->
# BigCommerce Featured Product

Provides a metabox on posts and pages listing existing BigCommerce products.

**Contributors:** [topher1kenobe](http://profiles.wordpress.org/topher1kenobe)  
**Tags:** [posts](http://wordpress.org/plugins/tags/posts), [pages](http://wordpress.org/plugins/tags/pages), [bigcommerce](http://wordpress.org/plugins/tags/bigcommerce), [ecommerce](http://wordpress.org/plugins/tags/ecommerce), [featured](http://wordpress.org/plugins/tags/featured)  
**Requires at least:** 3.0  
**Tested up to:** 5.4  
**Stable tag:** 1.0  
**License:** [GPLv2 or later](http://www.gnu.org/licenses/gpl-2.0.html)  

## Description ##

This plugin provides a metabox on posts and pages listing existing BigCommerce products.  The end user is allowed to choose one and make it associated with the post or page via meta data.

Practically speaking, Featured Products work exactly like Featured Images.  The Post or Page and Featured Products are merely attached, and you must use a template tag or WordPress functions to render the Product.

## Installation ##

This section describes how to install the plugin and get it working.

1. Upload the `bc-featured-product` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create new or Edit a Post.

## Usage ##

Page or Post meta has a key called `_bc_featured_product`.	A very simple way to render the slider is like this:

```<?php
    $featured = get_post_meta( get_the_ID(), '_bc_featured_product', true );

    if (
        ! empty( $featured )
        &&
        function_exists( 'bigcommerce' )
    ) {
        echo do_shortcode( '[bigcommerce_product post_id=' . absint( $featured ) . ']' );
    }
?>```

## Frequently Asked Questions ##

### 1.0 ###
* Initial release.


