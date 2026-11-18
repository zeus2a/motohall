<?php declare(strict_types = 1);

// odsl-C:\zeus_dev\zeus_projets\deepseek-harness\sites-web\moto-hall_laravel\home\capi3439\zeusstack\motohall\includes\_functions.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-aegis_resoudre_dans
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.73.0.5-8.4.25-d775015a2a68ecbccec66636471d6e8f9d832e0cda39b6e1f1a859e1ee6ff200',
   'data' => 
  array (
    'name' => 'aegis_resoudre_dans',
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
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 30,
        'endColumn' => 43,
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
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 46,
        'endColumn' => 60,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'attendu' => 
      array (
        'name' => 'attendu',
        'default' => 
        array (
          'code' => '\'dir\'',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 343,
            'startFilePos' => 3166,
            'endTokenPos' => 343,
            'endFilePos' => 3170,
          ),
        ),
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
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 63,
        'endColumn' => 85,
        'parameterIndex' => 2,
        'isOptional' => true,
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
 * Résout un chemin demandé SOUS un dossier racine, en TOLÉRANT LA CASSE.
 *
 * !! POURQUOI CETTE FONCTION EXISTE (20/09/2026, déplacée ici le 19/10/2026) :
 *    les pages envoyaient « ATELIER » alors que le dossier s\'appelle « atelier ».
 *    Sur Windows la casse ne compte pas, donc cela fonctionnait sur le poste de
 *    Frédéric ; sur le serveur Linux, realpath() échouait et la page répondait
 *    « 400 Bad Request » — mesuré en ligne sur les trois dossiers racine.
 *
 *    Elle vit maintenant dans le moteur parce que DEUX fichiers en ont besoin :
 *    documents.php (qui liste) et fichier.php (qui sert le fichier). Deux copies
 *    de cette logique, c\'est une faille qui s\'ouvre le jour où on n\'en corrige
 *    qu\'une.
 *
 * @param string $racine  dossier auquel on ne doit jamais échapper
 * @param string $demande chemin relatif, avec des « / »
 * @param string $attendu \'dir\' pour un dossier, \'file\' pour un fichier
 * @return string|null    chemin réel, ou null si refusé / inexistant
 */',
    'startLine' => 80,
    'endLine' => 140,
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
        'name' => 'aegis_resoudre_dans',
        'filename' => 'C:/zeus_dev/zeus_projets/deepseek-harness/sites-web/moto-hall_laravel/home/capi3439/zeusstack/motohall/includes/_functions.php',
      ),
    ),
  ),
));