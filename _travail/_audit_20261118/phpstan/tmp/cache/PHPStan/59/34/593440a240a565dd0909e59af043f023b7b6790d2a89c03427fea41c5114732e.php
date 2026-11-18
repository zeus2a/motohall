<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-scandir
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v3-6.73.0.5-dev-master@e4f5f6c-8.4.25-',
   'data' => 
  array (
    'name' => 'scandir',
    'parameters' => 
    array (
      'directory' => 
      array (
        'name' => 'directory',
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\FileReference',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'startLine' => 26,
        'endLine' => 27,
        'startColumn' => 9,
        'endColumn' => 25,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'sorting_order' => 
      array (
        'name' => 'sorting_order',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 28,
            'startFilePos' => 993,
            'endTokenPos' => 28,
            'endFilePos' => 993,
          ),
        ),
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 9,
        'endColumn' => 30,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'context' => 
      array (
        'name' => 'context',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 35,
            'startFilePos' => 1015,
            'endTokenPos' => 35,
            'endFilePos' => 1018,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 9,
        'endColumn' => 23,
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
              'name' => 'array',
              'isIdentifier' => true,
            ),
          ),
          1 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'false',
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
 * List files and directories inside the specified path
 * @link https://php.net/manual/en/function.scandir.php
 * @param string $directory <p>
 * The directory that will be scanned.
 * </p>
 * @param int $sorting_order <p>
 * By default, the sorted order is alphabetical in ascending order. If
 * the optional sorting_order is set to non-zero,
 * then the sort order is alphabetical in descending order.
 * </p>
 * @param resource $context [optional] <p>
 * For a description of the context parameter,
 * refer to the streams section of
 * the manual.
 * </p>
 * @return array|false an array of filenames on success, or false on
 * failure. If directory is not a directory, then
 * boolean false is returned, and an error of level
 * E_WARNING is generated.
 */',
    'startLine' => 25,
    'endLine' => 32,
    'startColumn' => 5,
    'endColumn' => 5,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'scandir',
        'filename' => 'phpstorm-stubs:standard/standard_7.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));