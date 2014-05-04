Feature: Reverse class namespace conversion
  Convert namespaced class names to PHP-5.2 pseudo-namespace class names.

  Scenario: Add namespace statement for classes
    Given I have a file in the directory "build" with the following content:
      """
      namespace My\Foo\Bar;
      class Baz
      {
      }
      """

    When I run "./bin/namespacify migrate --reverse 'My\Foo' Foo build"
    Then the file should have the following content:
      """
      class Foo_Bar_Baz
      {
      }
      """

  Scenario: Add namespace statement for interfaces
    Given I have a file in the directory "build" with the following content:
      """
      namespace My\Foo\Bar;
      interface Bazable
      {
      }
      """

    When I run "./bin/namespacify migrate --reverse 'My\Foo' Foo build"
    Then the file should have the following content:
      """
      interface Foo_Bar_Bazable
      {
      }
      """

  Scenario: Adjust object creation with import
    Given I have a file in the directory "build" with the following content:
      """
      use \My\Foo\Bar\Baz\Hooray;
      $a = new Hooray();
      """

    When I run "./bin/namespacify migrate --reverse 'My\Foo' Foo build"
    Then the file should have the following content:
      """
      $a = new Foo_Bar_Baz_Hooray();
      """

  Scenario: Adjust object creation with FQCN
    Given I have a file in the directory "build" with the following content:
      """
      $a = new \My\Foo\Bar\Baz\Hooray();
      """

    When I run "./bin/namespacify migrate --reverse 'My\Foo' Foo build"
    Then the file should have the following content:
      """
      $a = new Foo_Bar_Baz_Hooray();
      """

  Scenario: Adjust static class use
    Given I have a file in the directory "build" with the following content:
      """
      use \My\Foo\Bar\Baz\Hooray;
      $a = Hooray::HUZAAH;
      """

    When I run "./bin/namespacify migrate --reverse 'My\Foo' Foo build"
    Then the file should have the following content:
      """
      $a = Foo_Bar_Baz_Hooray::HUZAAH;
      """

#  Scenario: No namespace is converted when no matching class exists
#    Given I have a file in the directory "build" with the following content:
#      """
#      namespace Bar;
#      class Baz
#      {
#      }
#      """
#
#    When I run "./bin/namespacify migrate --reverse 'My\Foo' Foo build"
#    Then the file should have the following content:
#      """
#      namespace Bar;
#      class Baz
#      {
#      }
#      """