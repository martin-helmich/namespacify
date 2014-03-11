namespacify
===========

<a href="https://travis-ci.org/martin-helmich/namespacify" target="_blank">
  <img src="https://travis-ci.org/martin-helmich/namespacify.png"/>
</a>

`namespacify` converts PHP 5.2-style pseudo namespaces into real PHP namespaces.

Example
-------

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

    > namespacify --source-namespace MyPackage --target-namespace 'My\Package' --directory .

namespacify will convert the entire directory specified by the `--directory` parameter recursively to use PHP 5.3-style
namespaces:

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