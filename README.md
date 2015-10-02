# OGAM

OGAM is a generic system based on metadata allowing to build a web site to collect data, display them on a map, request the data, ... 
 
OGAM is under GPL license.


# Documentation

The documentation, installation and configuration procedures, etc is in the "documentation" folder.


# Gradle

Gradle is used to launch the build of the components of the project.

To use gradle :
* install [Gradle](https://gradle.org/)
* `gradle tasks`    To get the list of available tasks


# Vagrant

Vagrant is used to instanciate a virtual machine with the project components (Apache, Tomcat, Mapserver, ...).

To use vagrant : 
* install [VirtualBox](https://www.virtualbox.org/)
* install [Vagrant](https://www.vagrantup.com/)
* `vagrant up`   To start the VM

To connect the VM :
* use VirtualBox interface
* (or) connect with SSH on localhost on port 2222 with the login vagrant/vagrant  