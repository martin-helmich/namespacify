Feature: Indirect usage conversion
  Convert PHP 5.2-style class names in strings and comments

  Scenario: Convert @var annotation for properties
    Given I have a file in the directory "build" with the following content:
      """
      <?php
      class Foo_Bar {
        /** @var Foo_Bar_Huzzah */
        private $huzzah;
      }
      """

    When I run "./namespacify --source-namespace Foo --target-namespace 'My\Foo' --directory build"
    Then the file should have the following content:
      """
      <?php
      namespace My\Foo;

      class Bar {
        /** @var \My\Foo\Bar\Huzzah */
        private $huzzah;
      }
      """

  Scenario: Convert @var annotation for method type hints
    Given I have a file in the directory "build" with the following content:
      """
      <?php
      class Foo_Bar {
        /**
         * @param Foo_Bar_Huzzah $foo Test me!
         */
        public function baz($foo) {};
      }
      """

    When I run "./namespacify --source-namespace Foo --target-namespace 'My\Foo' --directory build"
    Then the file should have the following content:
      """
      <?php
      namespace My\Foo;

      class Bar {
        /**
         * @param \My\Foo\Bar\Huzzah $foo Test me!
         */
        public function baz($foo) {};
      }
      """

  Scenario: Convert class name in strings
    Given I have a file in the directory "build" with the following content:
      """
      <?php
      $a = get_class('Foo_Bar_Baz');
      """

    When I run "./namespacify --source-namespace Foo --target-namespace 'My\Foo' --directory build"
    Then the file should have the following content:
      """
      <?php
      $a = get_class('\\My\\Foo\\Bar\\Baz');
      """