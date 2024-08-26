<?php
use OpenApi\Generator as OpenApiGenerator;

$openapi = OpenApiGenerator::scan(['./app/Http/Controllers']);

header('Content-Type: application/json');
echo $openapi->toJson();
