<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'JO - Museo',
	'description' => '',
	'category' => 'fe',
	'author' => 'JUSTORANGE',
	'author_email' => 'info@justorange.de',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '11.5.12',
	'constraints' => [
		'depends' => [
			'typo3' => '6.2-0.0.0'
		],
		'conflicts' => [

		],
		'suggests' => [

		]
	],
    'autoload' => [
        'psr-4' => [
            'JO\\JoMuseo\\' => 'Classes',
            'MyCLabs\\Enum\\' => 'Classes/Helper/php-enum-1.8.3',
            'ZipStream\\' => 'Classes/Helper/ZipStream-PHP-2.2.0',
            'Psr\\SimpleCache\\' => 'Classes/Helper/simple-cache',
            'PhpOffice\\PhpSpreadsheet\\' => 'Classes/Helper/PhpSpreadsheet'
        ],
    ]
];
