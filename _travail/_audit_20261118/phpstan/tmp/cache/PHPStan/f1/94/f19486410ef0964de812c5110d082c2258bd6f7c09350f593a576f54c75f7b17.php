<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-define
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v3-6.73.0.5-dev-master@e4f5f6c-8.4.25-',
   'data' => 
  array (
    'name' => 'define',
    'parameters' => 
    array (
      'constant_name' => 
      array (
        'name' => 'constant_name',
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
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 9,
        'endColumn' => 29,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'value' => 
      array (
        'name' => 'value',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
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
                'code' => '[\'8.1\' => \'mixed\']',
                'attributes' => 
                array (
                  'startLine' => 31,
                  'endLine' => 31,
                  'startTokenPos' => 21,
                  'startFilePos' => 1159,
                  'endTokenPos' => 27,
                  'endFilePos' => 1176,
                ),
              ),
              'default' => 
              array (
                'code' => '\'null|array|bool|int|float|string\'',
                'attributes' => 
                array (
                  'startLine' => 31,
                  'endLine' => 31,
                  'startTokenPos' => 33,
                  'startFilePos' => 1188,
                  'endTokenPos' => 33,
                  'endFilePos' => 1221,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 31,
        'endLine' => 32,
        'startColumn' => 9,
        'endColumn' => 20,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'case_insensitive' => 
      array (
        'name' => 'case_insensitive',
        'default' => 
        array (
          'code' => '\\false',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 58,
            'startFilePos' => 1336,
            'endTokenPos' => 58,
            'endFilePos' => 1340,
          ),
        ),
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
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
            'name' => 'JetBrains\\PhpStorm\\Deprecated',
            'isRepeated' => false,
            'arguments' => 
            array (
              'since' => 
              array (
                'code' => '"7.3"',
                'attributes' => 
                array (
                  'startLine' => 33,
                  'endLine' => 33,
                  'startTokenPos' => 48,
                  'startFilePos' => 1295,
                  'endTokenPos' => 48,
                  'endFilePos' => 1299,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 33,
        'endLine' => 34,
        'startColumn' => 9,
        'endColumn' => 38,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
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
    ),
    'docComment' => '/**
 * Defines a named constant
 * @link https://php.net/manual/en/function.define.php
 * @param string $constant_name <p>
 * The name of the constant.
 * </p>
 * @param null|array|bool|int|float|string $value <p>
 * The value of the constant.
 * In PHP 5, value must be a scalar value (integer, float, string, boolean, or null).
 * In PHP 7, array values are also accepted.
 * It is possible to define resource constants,
 * however it is not recommended and may cause unpredictable behavior.
 * </p>
 * @param bool $case_insensitive [optional] <p>
 * If set to true, the constant will be defined case-insensitive.
 * The default behavior is case-sensitive; i.e.
 * CONSTANT and Constant represent
 * different values.
 * Defining case-insensitive constants is deprecated as of PHP 7.3.0.
 * </p>
 * <p>
 * Case-insensitive constants are stored as lower-case.
 * </p>
 * @return bool true on success or false on failure.
 */',
    'startLine' => 29,
    'endLine' => 37,
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
        'name' => 'define',
        'filename' => 'phpstorm-stubs:Core/Core.stub',
        'extensionName' => 'Core',
        'aliasName' => NULL,
      ),
    ),
  ),
));