Feature: Class alias generation
  Generate alias files for converted class names

  Scenario: Create alias file.
    Given I have a file in the directory "build" with the following content:
      """
      class Foo_Bar
      {
          /** @var Foo_Bar_Huzzah */
          private $huzzah;
      }
      """

    When I run "./bin/namespacify migrate --alias-file build/aliases.php Foo 'My\Foo' build"
    Then the file "build/aliases.php" should have the following content:
      """
        class_alias('My\\Foo\\Bar', 'Foo_Bar');
      """