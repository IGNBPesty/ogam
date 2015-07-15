# Installation de l'intrégration continue pour le projet OGAM sur une machine Debian.


### Installation de Java

> sudo apt-get install openjdk-7-jdk

Test 

> java -version

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


	
### Installation de GIT

cf http://www.mon-code.net/article/42/installation-et-configuration-de-git-sur-debian-et-initialisation-dun-depot-git

> sudo apt-get install git
	
	
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


==> PB, la connexion ne se fait toujours pas depuis PgAdmin avec le compte ogam. Pb de firewall ???


### Installation de Mapserver