namespacify
===========

[![Build Status](https://travis-ci.org/martin-helmich/namespacify.svg?branch=master)](https://travis-ci.org/martin-helmich/namespacify)

`namespacify` converts PHP 5.2-style pseudo namespaces into real PHP namespaces
(and vice versa).

Example
-------

### Pseudo to real namespaces

Take the following input file:

    <?php

    class MyPackage_Model_User {

        /** @var MyPackage_Model_Group */
        private $group;

        /** @var string */
        private $name;

        public function __construct($name, MyPackage_Model_Group $group=NULL) {
            if ($group === NULL) {
                $group = MyPackage_Model_Group::getNullGroup();
            }

            $this->group = $group;
            $this->name  = $name;
        }
    }

Use the following command to convert the pseudo namespaces into real namespaces:

    > namespacify migrate MyPackage 'My\Package' .

namespacify will convert the entire directory specified by the `directory`
argument (the last one) recursively to use PHP 5.3-style namespaces:

    <?php
    namespace My\Package\Model;

    class User {

        /** @var \My\Package\Model\Group */
        private $group;

        /** @var string */
        private $name;

        public function __construct($name, Group $group=NULL) {
            if ($group === NULL) {
                $group = Group::getNullGroup();
            }

            $this->group = $group;
            $this->name  = $name;
        }
    }

### Reverse, backport real namespaces to pseudo namespaces

This might prove useful when you are stuck in a PHP 5.2 environment (for
whatever reasons) and need to use libraries that are built for PHP 5.3 and
later.

Although, please note that the use of namespaces is typically not the only
incompatibility that you will encounter when using libraries built for PHP 5.3
in PHP 5.2. This means that you will probably not be able to use libraries 
backported by *namespacify* right out-of-the-box, but at least you will be
spared a lot of the grunt work. 

That being said, you can use the following command to reverse the conversion
from the previous example:

    > namespacify migrate --reverse 'My\\Package' 'MyPackage' .

This works exactly as forward migration, with the exception of the `--reverse`
option.

### Generating alias maps

Sometimes, you may want to keep backwards compatibility when renaming classes.
For this, you can use PHP's [class_alias](http://php.net/class_alias) function.
*namespacify* can auto-generate a file with alias definitions for all renamed
classes by specifying the `--alias-file` option:

    > namespacify migrate --alias-file=aliases.php 'MyPackage' 'My\\Package' .

This will generate an alias file that will look like this:

    <?php
    class_alias('My\\Package\\Model\\User', 'MyPackage_Model_User');
    class_alias('My\\Package\\Model\\Group', 'MyPackage_Model_Group');
    // ...
