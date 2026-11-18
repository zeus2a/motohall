<?php declare(strict_types = 1);

// odsl-C:\zeus_dev\zeus_projets\deepseek-harness\sites-web\moto-hall_laravel\home\capi3439\public_html\motohall.zeusstack.fr\documents.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-aegis_resoudre_dossier
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.73.0.5-8.4.25-b9b0504f2d3575dd51287cef9788dc75633e07188323587d425094ab9474bb7b',
   'data' => 
  array (
    'name' => 'aegis_resoudre_dossier',
    'parameters' => 
    array (
      'racine' => 
      array (
        'name' => 'racine',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 33,
        'endColumn' => 46,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'demande' => 
      array (
        'name' => 'demande',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 49,
        'endColumn' => 63,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
    ),
    'returnsReference' => false,
    'returnType' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
      'data' => 
      array (
        'types' => 
        array (
          0 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'string',
              'isIdentifier' => true,
            ),
          ),
          1 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'null',
              'isIdentifier' => true,
            ),
          ),
        ),
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Résout un dossier situé sous documents/ (tolérance de casse, refus des
 * remontées « ../ »).
 *
 * L\'implémentation vit désormais dans le moteur (includes/_functions.php,
 * fonction aegis_resoudre_dans), parce que fichier.php en a besoin aussi :
 * deux copies de cette logique, c\'est une faille le jour où on n\'en corrige
 * qu\'une. Ce nom reste pour ne pas toucher aux appels existants.
 */',
    'startLine' => 49,
    'endLine' => 52,
    'startColumn' => 1,
    'endColumn' => 1,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'aegis_resoudre_dossier',
        'filename' => 'C:/zeus_dev/zeus_projets/deepseek-harness/sites-web/moto-hall_laravel/home/capi3439/public_html/motohall.zeusstack.fr/documents.php',
      ),
    ),
  ),
));