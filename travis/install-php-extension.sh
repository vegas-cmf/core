#!/usr/bin/env bash

git clone -q --depth=1 https://github.com/phalcon/cphalcon.git -b $1
(cd cphalcon/build; bash install &>/dev/null)
phpenv config-add cphalcon/tests/_ci/phalcon.ini &> /dev/null
