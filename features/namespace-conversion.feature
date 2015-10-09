Feature: Class namespace conversion
  Convert PHP 5.2-style class names to namespaced class names.

  Scenario: Add namespace statement for classes
    Given I have a file in the directory "build" with the following content:
      """
      class Foo_Bar_Baz
      {
      }
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      namespace My\Foo\Bar;

      class Baz
      {
      }
      """

  Scenario: Add namespace statement for interfaces
    Given I have a file in the directory "build" with the following content:
      """
      interface Foo_Bar_Bazable
      {
      }
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      namespace My\Foo\Bar;

      interface Bazable
      {
      }
      """

  Scenario: Adjust object creation
    Given I have a file in the directory "build" with the following content:
      """
      $a = new Foo_Bar_Baz_Hooray();
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      use \My\Foo\Bar\Baz\Hooray;
      $a = new Hooray();
      """

  Scenario: Adjust static class use
    Given I have a file in the directory "build" with the following content:
      """
      $a = Foo_Bar_Baz_Hooray::HUZAAH;
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      use \My\Foo\Bar\Baz\Hooray;
      $a = Hooray::HUZAAH;
      """

  Scenario: No namespace is added when no matching class exists
    Given I have a file in the directory "build" with the following content:
      """
      class Bar_Baz
      {
      }
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      class Bar_Baz
      {
      }
      """

  Scenario: Conflicting class names are correctly imported
    Given I have a file in the directory "build" with the following content:
    """
      class Foo_Bar_Baz extends \Other\Bar\Baz
      {
      }
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
    """
      namespace My\Foo\Bar;

      class Baz extends \Other\Bar\Baz
      {
      }
      """

  Scenario: Existing imports are handled correctly
    Given I have a file in the directory "build" with the following content:
      """
      use Other\Bar\SuperBaz;

      class Foo_Bar_Baz extends SuperBaz
      {
      }
      """

    When I run "./bin/namespacify migrate Foo 'My\Foo' build"
    Then the file should have the following content:
      """
      namepace My\Foo\Bar;
      use Other\Bar\SuperBaz;

      class Baz extends SuperBaz
      {
      }
      """
