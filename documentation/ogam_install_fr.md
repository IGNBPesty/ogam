# Installation et configuration d'OGAM

## 1. Récupération du code source

- Cloner (GIT) le repository d'OGAM dans votre workspace d'éclipse.  
Voir le site `http://del-9323972.ign.fr/` pour les instructions. **FIXME:** Ce n'est pas correcte pour la documentation publique)
-Créer un projet dans eclipse. (import/général/existing project...)

- Checkouter (SVN) le projet `libs_java` dans votre workspace d'éclipse (au même niveau que le projet OGAM).  
`http://ifn-dev/svn/libs%20java/trunk/`

- Créer l'arborescence `Service Java Generation de Rapports\generationRapport` dans votre workspace d'éclipse (au même niveau que le projet OGAM).

- Vérifier ou recréer l'arborescence suivante:

		website/htdocs/custom/public
		website/htdocs/logs/access.log
		website/htdocs/logs/error.log
		website/htdocs/server/ogamServer/sessions
		website/htdocs/server/ogamServer/tmp/database
		website/htdocs/server/ogamServer/tmp/language
		website/htdocs/server/ogamServer/upload


## 2. Création de la base de donnée

Sous windows :
 - Lancer simplement le fichier OGAM\database\GENERATE_DB.bat
Sous linux jouer dans l'ordre :
 - les scripts 0-\*, 1-\*, 2-\*, 3-\*, 4-\*, 5-\* présents dans le répertoire `database`.
 - les scripts communes, legacy_gist, departements et nuts_0 dans le répertoire 'database\Referentiels'.


## 3. Configuration de l'application

**Configurer OGAM**

- Créer si besoin (en cas de modification) et configurer le fichier application.ini dans "website/htdocs/custom/application/configs".
- Configurer les paramètres dans "website.application_parameters".
- Executer la commande "Ant build" sur les build.xml de lib_java et de website.


**Configurer les services**

- Adapter les chemins dans les ppts de services_config/integration et report generation.
- Récupérer le projet libs_java au même niveau que le projet OGAM dans le workspace.
- Sous windows lancer la commande en mode administrateur.
- Jouer la commande "ant build" dans libs_java.
- Jouer la commande "ant deploy" dans OGAM\service_integration.
- Jouer la commande "ant deploy" dans OGAM\service_generation_rapport.
- Si cela n'a pas déjà été fait créer un utilisateur dans le fichier Tomcat 7.0\conf\tomcat-users.xml
  (ex: <user username="tomcat" password="tomcat" roles="manager-gui,admin-gui"/>)
- Démarrer Tomcat si cela n'est pas fait et ouvrir l'url: 'http://localhost:8080/' dans un navigateur.
- Cliquer sur le bouton "Manager App" et renseigner le login/mdp de l'utilisateur ajouté.
- Dans la partie "Deployer" renseigner les trois champs de la partie supérieure pour chaque service nécessaire et cliquer sur "Deployer".
  Note : Le déploiement seul du fichier war (partie inférieure) ne gère pas le déploiement du fichier xml de configuration.
  Exemple:
	- /OGAMIntegrationService
	- file:///C:\Users\sgalopin\Desktop\webapps\OGAMIntegrationService.xml
	- file:///C:\Users\sgalopin\Desktop\webapps\OGAMIntegrationService.war