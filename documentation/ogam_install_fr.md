# Installation et configuration d'OGAM

## 1. Récupération du code source
- Cloner (GIT) le repository d'OGAM dans votre workspace d'éclipse.  
Voir le site `http://del-9323972.ign.fr/` pour les instructions. **FIXME:** Ce n'est pas correcte pour la documentation publique)

- Checkouter (SVN) le projet `libs_java` dans votre workspace d'éclipse (au même niveau que le projet OGAM).  
`http://ifn-dev/svn/libs%20java/trunk/`

- Créer l'arborescence `Service Java Generation de Rapports\generationRapport` dans votre workspace d'éclipse (au même niveau que le projet OGAM).

- Vérifier ou recréer l'arborescence suivante:

		website/htdocs/custom/public
		website/htdocs/logs/access.log
		website/htdocs/logs/error.log
		website/htdocs/sessions
		website/htdocs/tmp/database
		website/htdocs/tmp/language
		website/htdocs/upload


## 2. Création de la base de donnée
Jouer les scripts 0-\*, 1-\*, 2-\*, 3-\*, 4-\*, 5-\* présents dans le répertoire `Metadata`.


## 3. Configuration de l'application

**Configurer OGAM**

- Créer si besoin (en cas de modification) et configurer le fichier application.ini dans "website/htdocs/custom/application/configs".

- Configurer les paramètres dans "website.application_parameters".

- Executer la commande "Ant build" sur les build.xml de lib_java et de website.


**Configurer les services**

- adapter les chemins dans les ppts de services_config/integration et report generation

- Ant build sur service_integration et service_generation_rapport