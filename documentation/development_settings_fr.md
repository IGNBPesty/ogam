# Installation et configuration de l'environnement de développement

## 1. Environnement de Serveur web préconfiguré : MS4W
- Installer MS4W (Apache + Mapserver + Php) par exemple dans "C:\Program Files\ms4w".

**Serveur HTTP : Apache**

- Dans le `httpd.conf` d'Apache, déplacer la partie `<IfModule alias_module> ... </IfModule>` à la fin du fichier.  
**Note:** Ceci n'est pas obligatoire mais évite des warnings lors des tests de syntaxe. 
(Problème d'imbrication de SrciptAlias dans le répertoire cgi-bin)
- Dans le `httpd.conf` d'Apache, Il est recommandé de changer le path des fichiers httpd_*.conf pour un répertoire en dehors 
de ms4w et sauvegardé régulièrement afin d'éviter la perte de vos fichiers de configuration.  
**Exemple :** include `D:/DONNEES/Ms4w/httpd.d/httpd_*.conf`
- Copier `website/config/httpd_ogam.conf` dans votre répertoire httpd.d (adapter les répertoires)
- Vérifiez votre configuration en utlisant la commande `httpd -t`.

**Langage serveur : PHP**

- Changer les limites suivantes dans `php.ini` (`C:\Program Files\ms4w\Apache\cgi-bin`) :
	- post_max_size =100M
	- upload_max_filesize = 100M
	- memory_limit = 128M

**Serveur Cartographique : MAPSERVER**

...

##2. Conteneur de servlets : TOMCAT
- Installer Tomcat
- Vérifier la présence ou ajouter les libs `postgresql-x.x-xxxx.jdbc4.jar` et `mail.jar` dans le répertoire lib de tomcat.

##3. SGBD et extension cartographique : Postgresql / Postgis
##4. Système d'exploitation :
###Windows

- Dans `C:/Windows/system32/drivers/etc/hosts` ajouter les lignes : (x étant une adresse libre et différent de 1)  

	127.0.0.x ogam
	127.0.0.x www.ogam.fr
	127.0.0.x www-1.ogam.fr
	127.0.0.x www-2.ogam.fr
	127.0.0.x www-3.ogam.fr
	127.0.0.x www-4.ogam.fr
	127.0.0.x www-5.ogam.fr
	127.0.0.x www-6.ogam.fr
	127.0.0.x www-7.ogam.fr
	127.0.0.x www-8.ogam.fr
	127.0.0.x www-9.ogam.fr
`
	
###Linux
...

##5. IDE : Eclipse
- Installer Eclipse version php
- Installer les plugins subversion, sencha eclipse plugin.

##6. Système de synchronisation de fichiers : GIT


