Feature: Indirect usage conversion
  Convert PHP 5.2-style class names in strings and comments

  Scenario: Convert @var annotation for properties
    Given I have a file in the directory "build" with the following content:
      """
      class Foo_Bar
      {
          /** @var Foo_Bar_Huzzah */
          private $huzzah;
      }
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      namespace My\Foo;

      class Bar
      {
          /** @var \My\Foo\Bar\Huzzah */
          private $huzzah;
      }
      """

  Scenario: Convert @param annotation for method type hints
    Given I have a file in the directory "build" with the following content:
      """
      class Foo_Bar
      {
          /**
           * @param Foo_Bar_Huzzah $foo Test me!
           */
          public function baz($foo)
          {}
      }
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      namespace My\Foo;

      class Bar
      {
          /**
           * @param \My\Foo\Bar\Huzzah $foo Test me!
           */
          public function baz($foo)
          {}
      }
      """

  Scenario: Convert class name in strings
    Given I have a file in the directory "build" with the following content:
      """
      $a = get_class('Foo_Bar_Baz');
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      $a = get_class('My\\Foo\\Bar\\Baz');
      """

  Scenario: Convert class name in strings containing other stuff
    Given I have a file in the directory "build" with the following content:
      """
      $a = 'Foo_Bar_Baz::some_method';
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      $a = 'My\\Foo\\Bar\\Baz::some_method';
      """
