<?php

echo "Downloading Composer installer..." . PHP_EOL;
mkdir('build');
file_put_contents("build/composer_installer.php", file_get_contents('http://getcomposer.org/installer'));

echo "Installing composer.phar" . PHP_EOL;
echo shell_exec("php build/composer_installer.php --install-dir build");

echo shell_exec("php build/composer.phar install");