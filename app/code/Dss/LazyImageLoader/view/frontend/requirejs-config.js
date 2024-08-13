/**
* Digit Software Solutions.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
*
* @category  Dss
* @package   Dss_LazyImageLoader
* @author    Extension Team
* @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
*/
var config = {
    paths: {
        'dss/unveil': 'Dss_LazyImageLoader/js/jquery.unveil'
    },
	shim: {
		'dss/unveil': {
			deps: ['jquery']
		},
	}
};