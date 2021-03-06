require_recipe "php"
require_recipe "php::module_mysql"
require_recipe "php::module_apc"
require_recipe "php::module_memcache"
require_recipe "php::module_curl"

package "php-pear" do
  action :install
end

php_pear_channel "pear.phpunit.de" do
  action :discover
end

php_pear_channel "components.ez.no" do
  action :discover
end

php_pear_channel "pear.symfony-project.com" do
  action :discover
end

php_pear_channel "pear.phpmd.org" do
  action :discover
end

php_pear_channel "pear.pdepend.org" do
  action :discover
end

php_pear_channel "pear.docblox-project.org" do
  action :discover
end

php_pear_channel "pear.michelf.com" do
  action :discover
end

# XSL needed by DocBlox
package "php5-xsl" do
  action :install
end

# Graphviz needed by DocBlox
package "graphviz" do
  action :install
end

# Sqlite needed by PHD (Docbook)
package "php5-sqlite" do
  action :install
end

# Using PEAR installer

execute "PEAR: upgrade all packages" do
  command "pear upgrade-all"
end

execute "PEAR: install phpunit/PHPUnit" do
  command "pear install -f phpunit/PHPUnit"
  creates "/usr/bin/phpunit"
end

execute "PEAR: install phpmd/PHP_PMD" do
  command "pear install -f phpmd/PHP_PMD"
  creates "/usr/bin/phpmd"
end

execute "PEAR: install pdepend/PHP_Depend" do
  command "pear install -f pdepend/PHP_Depend"
  creates "/usr/bin/pdepend"
end

execute "PEAR: install PHP_CodeSniffer-1.3.0" do
  command "pear install -f PHP_CodeSniffer-1.3.0"
  creates "/usr/bin/phpcs"
end

execute "PEAR: install phploc-1.5.0" do
  command "pear install -f phpunit/phploc"
  creates "/usr/bin/phploc"
end

execute "PECL: install xdebug" do
  command "pecl install -f xdebug"
  creates "/usr/lib/php5/20090626/xdebug.so"
end

execute "PEAR: install phpcpd" do
  command "pear install -f phpunit/phpcpd"
  creates "/usr/bin/phpcpd"
end

execute "PEAR: install docblox" do
  command "pear install -f docblox/DocBlox"
  creates "/usr/bin/docblox"
end

execute "PEAR: install phd" do
  command "pear install -f --alldeps doc.php.net/phd"
  creates "/usr/bin/phd"
end

execute "PEAR: install CodeCoverage" do
  command "pear install pear.phpunit.de/PHP_CodeCoverage"
  creates "/usr/share/php/PHP/CodeCoverage.php"
end

# Install xDebug
php_pear "xdebug" do
  # Specify that xdebug.so must be loaded as a zend extension
  zend_extensions ['xdebug.so']
  action :install
end

# Install the php packages we need
## Upgrade existing packages
execute "PEAR: upgrade all packages" do
  command "pear upgrade-all"
end