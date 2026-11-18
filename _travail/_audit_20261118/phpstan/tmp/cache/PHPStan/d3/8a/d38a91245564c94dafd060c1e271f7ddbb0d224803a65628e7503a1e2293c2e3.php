<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-directory
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v3-6.73.0.5-dev-master@e4f5f6c-8.4.25-',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'Directory',
        'filename' => 'phpstorm-stubs:standard/standard_0.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'Directory',
    'shortName' => 'Directory',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Instances of Directory are created by calling the dir() function, not by the new operator.
 * @link https://php.net/manual/en/class.directory.php
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 51,
    'startColumn' => 5,
    'endColumn' => 5,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'path' => 
      array (
        'declaringClassName' => 'Directory',
        'implementingClassName' => 'Directory',
        'name' => 'path',
        'modifiers' => 2177,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * @var string The directory that was opened.
 * @since 8.1
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 9,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'handle' => 
      array (
        'declaringClassName' => 'Directory',
        'implementingClassName' => 'Directory',
        'name' => 'handle',
        'modifiers' => 2177,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * @var resource Can be used with other directory functions such as {@see readdir()}, {@see rewinddir()} and {@see closedir()}.
 * @since 8.1
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 9,
        'endColumn' => 38,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'close' => 
      array (
        'name' => 'close',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.0\' => \'void\']',
                'attributes' => 
                array (
                  'startLine' => 26,
                  'endLine' => 26,
                  'startTokenPos' => 41,
                  'startFilePos' => 877,
                  'endTokenPos' => 47,
                  'endFilePos' => 893,
                ),
              ),
              'default' => 
              array (
                'code' => '\'\'',
                'attributes' => 
                array (
                  'startLine' => 26,
                  'endLine' => 26,
                  'startTokenPos' => 53,
                  'startFilePos' => 905,
                  'endTokenPos' => 53,
                  'endFilePos' => 906,
                ),
              ),
            ),
          ),
        ),
        'docComment' => '/**
 * Close directory handle.
 * Same as closedir(), only dir_handle defaults to $this.
 * @param resource $dir_handle [optional]
 * @link https://php.net/manual/en/directory.close.php
 */',
        'startLine' => 26,
        'endLine' => 29,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Directory',
        'implementingClassName' => 'Directory',
        'currentClassName' => 'Directory',
        'aliasName' => NULL,
      ),
      'rewind' => 
      array (
        'name' => 'rewind',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.0\' => \'void\']',
                'attributes' => 
                array (
                  'startLine' => 36,
                  'endLine' => 36,
                  'startTokenPos' => 77,
                  'startFilePos' => 1271,
                  'endTokenPos' => 83,
                  'endFilePos' => 1287,
                ),
              ),
              'default' => 
              array (
                'code' => '\'\'',
                'attributes' => 
                array (
                  'startLine' => 36,
                  'endLine' => 36,
                  'startTokenPos' => 89,
                  'startFilePos' => 1299,
                  'endTokenPos' => 89,
                  'endFilePos' => 1300,
                ),
              ),
            ),
          ),
        ),
        'docComment' => '/**
 * Rewind directory handle.
 * Same as rewinddir(), only dir_handle defaults to $this.
 * @param resource $dir_handle [optional]
 * @link https://php.net/manual/en/directory.rewind.php
 */',
        'startLine' => 36,
        'endLine' => 39,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Directory',
        'implementingClassName' => 'Directory',
        'currentClassName' => 'Directory',
        'aliasName' => NULL,
      ),
      'read' => 
      array (
        'name' => 'read',
        'parameters' => 
        array (
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
                  'name' => 'false',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.0\' => \'string|false\']',
                'attributes' => 
                array (
                  'startLine' => 47,
                  'endLine' => 47,
                  'startTokenPos' => 113,
                  'startFilePos' => 1703,
                  'endTokenPos' => 119,
                  'endFilePos' => 1727,
                ),
              ),
              'default' => 
              array (
                'code' => '\'\'',
                'attributes' => 
                array (
                  'startLine' => 47,
                  'endLine' => 47,
                  'startTokenPos' => 125,
                  'startFilePos' => 1739,
                  'endTokenPos' => 125,
                  'endFilePos' => 1740,
                ),
              ),
            ),
          ),
        ),
        'docComment' => '/**
 * Read entry from directory handle.
 * Same as readdir(), only dir_handle defaults to $this.
 * @param resource $dir_handle [optional]
 * @return string|false
 * @link https://php.net/manual/en/directory.read.php
 */',
        'startLine' => 47,
        'endLine' => 50,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Directory',
        'implementingClassName' => 'Directory',
        'currentClassName' => 'Directory',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));