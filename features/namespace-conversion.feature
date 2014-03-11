Feature: Class namespace conversion
  Convert PHP 5.2-style class names to namespaced class names.

  Scenario: Add namespace statement for classes
    Given I have a file in the directory "build" with the following content:
      """
      <?php
      class Foo_Bar_Baz { }
      """

    When I run "./namespacify --source-namespace Foo --target-namespace 'My\Foo' --directory build"
    Then the file should have the following content:
      """
      <?php
      namespace My\Foo\Bar;

      class Baz { }
      """

  Scenario: Add namespace statement for interfaces
    Given I have a file in the directory "build" with the following content:
      """
      <?php
      interface Foo_Bar_Bazable { }
      """

    When I run "./namespacify --source-namespace Foo --target-namespace 'My\Foo' --directory build"
    Then the file should have the following content:
      """
      <?php
      namespace My\Foo\Bar;

      interface Bazable { }
      """

  Scenario: Adjust object creation
    Given I have a file in the directory "build" with the following content:
      """
      <?php
      $a = new Foo_Bar_Baz_Hooray();
      """

    When I run "./namespacify --source-namespace Foo --target-namespace 'My\Foo' --directory build"
    Then the file should have the following content:
      """
      <?php
      $a = new \My\Foo\Bar\Baz\Hooray();
      """

  Scenario: Adjust static class use
    Given I have a file in the directory "build" with the following content:
      """
      <?php
      $a = Foo_Bar_Baz_Hooray::HUZAAH;
      """

    When I run "./namespacify --source-namespace Foo --target-namespace 'My\Foo' --directory build"
    Then the file should have the following content:
      """
      <?php
      $a = \My\Foo\Bar\Baz\Hooray::HUZAAH;
      """