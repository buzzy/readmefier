<?php

$spec = yaml_parse(file_get_contents('https://scalr.io/api/iacp/v3/openapi-preview.yml'));

//MAKE SURE REFS USES FULL URLS
array_walk_recursive($spec, function(&$val, $key) {

    if ($key == '$ref' && substr($val, 0, 9) == 'examples/') {
        $val = 'https://scalr.io/api/iacp/v3/' . $val;
    }

});

//ADD EMPTY ENUM TO RELATIONSHIP TYPES TO PREVENT README TO AUTO-FILL
foreach ($spec['components']['schemas'] as $component => &$value) {

    if (!isset($value['properties']['relationships']['properties'])) continue;

    foreach ($value['properties']['relationships']['properties'] as $relationship => &$val) {
        if (!isset($val['properties']['data']['properties']['type']['enum'])) continue;

        array_unshift($val['properties']['data']['properties']['type']['enum'], '');
    }    
}

$yaml = yaml_emit($spec);

$yaml = str_replace('- items: []', '- items: {}', $yaml);

file_put_contents('readme.yaml', $yaml);