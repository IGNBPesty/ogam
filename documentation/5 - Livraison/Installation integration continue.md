# Installation de l'intégration continue pour le projet OGAM sur une machine Debian.

VM Debian 8.0 nue avec un compte "admin" ayant les droits root via sudo.

## Configuration de l'intégration continue

### Installation de Java

> sudo apt-get install openjdk-7-jdk

Test : java -version

### Installation de Jenkins

cf [https://wiki.jenkins-ci.org/display/JENKINS/Installing+Jenkins+on+Ubuntu](https://wiki.jenkins-ci.org/display/JENKINS/Installing+Jenkins+on+Ubuntu)

Note :
Pour une machine dans le réseau interne IGN il faut déclarer le proxy IGN au niveau du wget 
dans le fichier /etc/wgetrc 

> use_proxy=on	
> https_proxy = https://proxy.ign.fr:3128/   
> http_proxy = http://proxy.ign.fr:3128/	

Dans /etc/apt/apt.conf.d/proxy mettre : 

> Acquire::http::Proxy "http://proxy.ign.fr:3128";	
> Acquire::ftp::Proxy "http://proxy.ign.fr:3128";	
	
Dans /etc/environment mettre :
> HTTP_PROXY="http://proxy.ign.fr:3128/";	
> HTTPS_PROXY="https://proxy.ign.fr:3128/";		
> FTP_PROXY="http://proxy.ign.fr:3128/";	

Ajout du paquet jenkins à apt-get

> wget -q -O - https://jenkins-ci.org/debian/jenkins-ci.org.key | sudo apt-key add -	
> sudo sh -c 'echo deb http://pkg.jenkins-ci.org/debian binary/ > /etc/apt/sources.list.d/jenkins.list'		
> sudo apt-get update	
> sudo apt-get install jenkins	


Dans /etc/default/jenkins il faut aussi ajouter le proxy sinon Jenkins ne voit pas ses plugins.

> JAVA_ARGS="-Dhttp.proxyHost=proxy.ign.fr -Dhttp.proxyPort=3128"


Installation de ANT

> sudo apt-get install ant

	
### Installation de GIT

cf http://www.mon-code.net/article/42/installation-et-configuration-de-git-sur-debian-et-initialisation-dun-depot-git

> sudo apt-get install git

Création d'un nouveau compte "ogam-ci" dans GitLab.




### Configuration de Jenkins

Dans la partie "Administrer Jenkins" du site, on ajoute les plugins Git 

Cf la config sur le site : http://ogam-integration.ign.fr:8080/job/OGAM_Website/configure



Ajout des librairies nécessaires "en dur" dans le workspace (en attendant Maven ou gradle).

> cd /var/lib/jenkins/workspace/OGAM_Website/
> sudo mkdir libraries
> sudo chown jenkins:jenkins libraries/
> sudo chmod 775 libraries/

Ajout de l'utilisateur admin dans le groupe "jenkins"
> sudo usermod -G jenkins -a admin

Recopie de "libs_php" et "libs_java" dans ce répertoire.

> sudo chown -R jenkins:jenkins libraries/
> sudo chmod -R 775 libraries/


	
	
### Installation de Apache

> sudo apt-get install apache2

Test : Afficher la page "http://ogam-integration.ign.fr/" dans un navigateur

Ajout de l'utilisateur admin dans le groupe "wwww-data"
> sudo usermod -G www-data -a admin

Vérif que ça a fonctionné
> id admin

On donne les droits sur le répertoire /var/www/html à admin et à Apache.
> cd /var/www/	
> sudo chgrp www-data /var/www/html/	
> sudo chmod 775 html/	

Test : Copier un fichier dans /var/www/html

### Installation de PHP

> sudo apt-get install php5	
> sudo /etc/init.d/apache2 restart	

Test : 
Copier un fichier "info.php" dans /var/www/html, avec la commande php_info();
Afficher la page "http://ogam-integration.ign.fr/info.php"

Ajout du driver postgresql pour PHP

> sudo apt-get install php5-pgsql

Ajout de XDebug pour avoir le coverage sur les tests unitaires

> sudo apt-get install php5-xdebug

### Installation de PostgreSQL

cf https://wiki.debian.org/PostgreSql

> sudo apt-get install postgresql


Edition du pg_hba.conf
> sudo nano /etc/postgresql/9.4/main/pg_hba.conf

pour autoriser les connexions en local
local   all             all                                  trust	
pour l'utilisateur ogam 	
host    ogam     ogam                   0.0.0.0/0       md5	


Edition du postgresql.conf
> sudo nano /etc/postgresql/9.4/main/postgresql.conf	

listen_addresses = '*' 

> sudo /etc/init.d/postgresql reload	


Connection à la base pour créer un utilisateur
> sudo -u postgres psql	

> CREATE USER ogam WITH PASSWORD 'ogam';	
> CREATE DATABASE ogam OWNER ogam;	


==> Pb, la connexion ne se fait toujours pas depuis PgAdmin avec le compte ogam. Pb de firewall ???



### Installation de Gradle

> sudo apt-get install gradle

==> Pb : c'est la version 1.5 qui est sur le repository debian par défaut, il nous faut une 2.x.

> sudo apt-get install software-properties-common


Ajout d'un repositoy PPA
> sudo su
> export https_proxy=https://proxy.ign.fr:3128/
> add-apt-repository ppa:cwchien/gradle

==> Ne fonctionne pas à cause du proxy


## Installation d'un site OGAM de démo



### Installation de Mapserver

> sudo apt-get install cgi-mapserver mapserver-bin
> sudo apt-get install libapache2-mod-fcgid

Test : /usr/lib/cgi-bin/mapserv -v

Utilisation de Fast CGI
> sudo a2enmod cgi
> sudo cp /usr/lib/cgi-bin/mapserv /usr/lib/cgi-bin/mapserv.fcgid
> sudo /etc/init.d/apache2 restart

Test : Appeler l'URL http://ogam-integration.ign.fr/cgi-bin/mapserv.fcgid?
