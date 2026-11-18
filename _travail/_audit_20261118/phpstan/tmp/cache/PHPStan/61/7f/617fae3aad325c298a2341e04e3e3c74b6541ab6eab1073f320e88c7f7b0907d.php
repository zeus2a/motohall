<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-recursiveiteratoriterator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v3-6.73.0.5-dev-master@e4f5f6c-8.4.25-',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'RecursiveIteratorIterator',
        'filename' => 'phpstorm-stubs:SPL/SPL.stub',
        'extensionName' => 'SPL',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'RecursiveIteratorIterator',
    'shortName' => 'RecursiveIteratorIterator',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Can be used to iterate through recursive iterators.
 * @link https://php.net/manual/en/class.recursiveiteratoriterator.php
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 226,
    'startColumn' => 5,
    'endColumn' => 5,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'OuterIterator',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'LEAVES_ONLY' => 
      array (
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'name' => 'LEAVES_ONLY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 13,
            'startTokenPos' => 28,
            'startFilePos' => 352,
            'endTokenPos' => 28,
            'endFilePos' => 352,
          ),
        ),
        'docComment' => '/**
 * The default. Lists only leaves in iteration.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 9,
        'endColumn' => 37,
      ),
      'SELF_FIRST' => 
      array (
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'name' => 'SELF_FIRST',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '1',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 17,
            'startTokenPos' => 41,
            'startFilePos' => 489,
            'endTokenPos' => 41,
            'endFilePos' => 489,
          ),
        ),
        'docComment' => '/**
 * Lists leaves and parents in iteration with parents coming first.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 9,
        'endColumn' => 36,
      ),
      'CHILD_FIRST' => 
      array (
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'name' => 'CHILD_FIRST',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '2',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 54,
            'startFilePos' => 626,
            'endTokenPos' => 54,
            'endFilePos' => 626,
          ),
        ),
        'docComment' => '/**
 * Lists leaves and parents in iteration with leaves coming first.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 9,
        'endColumn' => 37,
      ),
      'CATCH_GET_CHILD' => 
      array (
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'name' => 'CATCH_GET_CHILD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '16',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 67,
            'startFilePos' => 765,
            'endTokenPos' => 67,
            'endFilePos' => 766,
          ),
        ),
        'docComment' => '/**
 * Special flag: Ignore exceptions thrown in accessing children.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 9,
        'endColumn' => 42,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'iterator' => 
          array (
            'name' => 'iterator',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Traversable',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 36,
            'endLine' => 36,
            'startColumn' => 13,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'mode' => 
          array (
            'name' => 'mode',
            'default' => 
            array (
              'code' => 'self::LEAVES_ONLY',
              'attributes' => 
              array (
                'startLine' => 38,
                'endLine' => 38,
                'startTokenPos' => 109,
                'startFilePos' => 1472,
                'endTokenPos' => 111,
                'endFilePos' => 1488,
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'int\']',
                    'attributes' => 
                    array (
                      'startLine' => 37,
                      'endLine' => 37,
                      'startTokenPos' => 87,
                      'startFilePos' => 1416,
                      'endTokenPos' => 93,
                      'endFilePos' => 1431,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 37,
                      'endLine' => 37,
                      'startTokenPos' => 99,
                      'startFilePos' => 1443,
                      'endTokenPos' => 99,
                      'endFilePos' => 1444,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 37,
            'endLine' => 38,
            'startColumn' => 13,
            'endColumn' => 41,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'flags' => 
          array (
            'name' => 'flags',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 40,
                'endLine' => 40,
                'startTokenPos' => 139,
                'startFilePos' => 1614,
                'endTokenPos' => 139,
                'endFilePos' => 1614,
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'int\']',
                    'attributes' => 
                    array (
                      'startLine' => 39,
                      'endLine' => 39,
                      'startTokenPos' => 117,
                      'startFilePos' => 1557,
                      'endTokenPos' => 123,
                      'endFilePos' => 1572,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 39,
                      'endLine' => 39,
                      'startTokenPos' => 129,
                      'startFilePos' => 1584,
                      'endTokenPos' => 129,
                      'endFilePos' => 1585,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 39,
            'endLine' => 40,
            'startColumn' => 13,
            'endColumn' => 26,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Construct a RecursiveIteratorIterator
 * @link https://php.net/manual/en/recursiveiteratoriterator.construct.php
 * @param Traversable $iterator The iterator being constructed from. Either a RecursiveIterator
 * or IteratorAggregate.
 * @param int $mode [optional] The operation mode. See class constants for details.
 * @param int $flags [optional] A bitmask of special flags. See class constants for details.
 * @since 5.1
 */',
        'startLine' => 35,
        'endLine' => 43,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Rewind the iterator to the first element of the top level inner iterator
 * @link https://php.net/manual/en/recursiveiteratoriterator.rewind.php
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 50,
        'endLine' => 53,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'valid' => 
      array (
        'name' => 'valid',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Check whether the current position is valid
 * @link https://php.net/manual/en/recursiveiteratoriterator.valid.php
 * @return bool true if the current position is valid, otherwise false
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 60,
        'endLine' => 63,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'key' => 
      array (
        'name' => 'key',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Access the current key
 * @link https://php.net/manual/en/recursiveiteratoriterator.key.php
 * @return mixed The key of the current element.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 70,
        'endLine' => 73,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'current' => 
      array (
        'name' => 'current',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Access the current element value
 * @link https://php.net/manual/en/recursiveiteratoriterator.current.php
 * @return mixed The current elements value.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 80,
        'endLine' => 83,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'next' => 
      array (
        'name' => 'next',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Move forward to the next element
 * @link https://php.net/manual/en/recursiveiteratoriterator.next.php
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 90,
        'endLine' => 93,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'getDepth' => 
      array (
        'name' => 'getDepth',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Get the current depth of the recursive iteration
 * @link https://php.net/manual/en/recursiveiteratoriterator.getdepth.php
 * @return int The current depth of the recursive iteration.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 100,
        'endLine' => 103,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'getSubIterator' => 
      array (
        'name' => 'getSubIterator',
        'parameters' => 
        array (
          'level' => 
          array (
            'name' => 'level',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 114,
                'endLine' => 114,
                'startTokenPos' => 313,
                'startFilePos' => 4403,
                'endTokenPos' => 313,
                'endFilePos' => 4406,
              ),
            ),
            'type' => 
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
                      'name' => 'int',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
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
                    'code' => '[\'8.0\' => \'int|null\']',
                    'attributes' => 
                    array (
                      'startLine' => 113,
                      'endLine' => 113,
                      'startTokenPos' => 289,
                      'startFilePos' => 4336,
                      'endTokenPos' => 295,
                      'endFilePos' => 4356,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 113,
                      'endLine' => 113,
                      'startTokenPos' => 301,
                      'startFilePos' => 4368,
                      'endTokenPos' => 301,
                      'endFilePos' => 4369,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 113,
            'endLine' => 114,
            'startColumn' => 13,
            'endColumn' => 34,
            'parameterIndex' => 0,
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
                  'name' => 'RecursiveIterator',
                  'isIdentifier' => false,
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * The current active sub iterator
 * @link https://php.net/manual/en/recursiveiteratoriterator.getsubiterator.php
 * @param int $level [optional]
 * @return RecursiveIterator|null The current active sub iterator.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 111,
        'endLine' => 117,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'getInnerIterator' => 
      array (
        'name' => 'getInnerIterator',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'RecursiveIterator',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Get inner iterator
 * @link https://php.net/manual/en/recursiveiteratoriterator.getinneriterator.php
 * @return RecursiveIterator The current active sub iterator.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 124,
        'endLine' => 127,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'beginIteration' => 
      array (
        'name' => 'beginIteration',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Begin Iteration
 * @link https://php.net/manual/en/recursiveiteratoriterator.beginiteration.php
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 134,
        'endLine' => 137,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'endIteration' => 
      array (
        'name' => 'endIteration',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * End Iteration
 * @link https://php.net/manual/en/recursiveiteratoriterator.enditeration.php
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 144,
        'endLine' => 147,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'callHasChildren' => 
      array (
        'name' => 'callHasChildren',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Has children
 * @link https://php.net/manual/en/recursiveiteratoriterator.callhaschildren.php
 * @return bool true if the element has children, otherwise false
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 154,
        'endLine' => 157,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'callGetChildren' => 
      array (
        'name' => 'callGetChildren',
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
                  'name' => 'RecursiveIterator',
                  'isIdentifier' => false,
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Get children
 * @link https://php.net/manual/en/recursiveiteratoriterator.callgetchildren.php
 * @return RecursiveIterator|null A <b>RecursiveIterator</b>.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 164,
        'endLine' => 167,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'beginChildren' => 
      array (
        'name' => 'beginChildren',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Begin children
 * @link https://php.net/manual/en/recursiveiteratoriterator.beginchildren.php
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 174,
        'endLine' => 177,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'endChildren' => 
      array (
        'name' => 'endChildren',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * End children
 * @link https://php.net/manual/en/recursiveiteratoriterator.endchildren.php
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 184,
        'endLine' => 187,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'nextElement' => 
      array (
        'name' => 'nextElement',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Next element
 * @link https://php.net/manual/en/recursiveiteratoriterator.nextelement.php
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 194,
        'endLine' => 197,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'setMaxDepth' => 
      array (
        'name' => 'setMaxDepth',
        'parameters' => 
        array (
          'maxDepth' => 
          array (
            'name' => 'maxDepth',
            'default' => 
            array (
              'code' => '-1',
              'attributes' => 
              array (
                'startLine' => 212,
                'endLine' => 212,
                'startTokenPos' => 532,
                'startFilePos' => 7911,
                'endTokenPos' => 533,
                'endFilePos' => 7912,
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'int\']',
                    'attributes' => 
                    array (
                      'startLine' => 211,
                      'endLine' => 211,
                      'startTokenPos' => 510,
                      'startFilePos' => 7851,
                      'endTokenPos' => 516,
                      'endFilePos' => 7866,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 211,
                      'endLine' => 211,
                      'startTokenPos' => 522,
                      'startFilePos' => 7878,
                      'endTokenPos' => 522,
                      'endFilePos' => 7879,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 211,
            'endLine' => 212,
            'startColumn' => 13,
            'endColumn' => 30,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Set max depth
 * @link https://php.net/manual/en/recursiveiteratoriterator.setmaxdepth.php
 * @param int $maxDepth [optional] <p>
 * The maximum allowed depth. Default -1 is used
 * for any depth.
 * </p>
 * @return void
 * @throws \\Exception Emits an Exception if maxDepth is less than -1.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 209,
        'endLine' => 215,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
        'aliasName' => NULL,
      ),
      'getMaxDepth' => 
      array (
        'name' => 'getMaxDepth',
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
                  'name' => 'int',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Get max depth
 * @link https://php.net/manual/en/recursiveiteratoriterator.getmaxdepth.php
 * @return int|false The maximum accepted depth, or false if any depth is allowed.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 222,
        'endLine' => 225,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'RecursiveIteratorIterator',
        'implementingClassName' => 'RecursiveIteratorIterator',
        'currentClassName' => 'RecursiveIteratorIterator',
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