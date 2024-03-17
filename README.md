# checklist
1 CONTEXTE
2 INSTALLATION
3 FONCTIONNALITES
4 VERSIONS



1) CONTEXTE
Un technicien de maintenance est envoyé sur différent d'une entreprise pour y effectuer diverses opérations de maintenance (tâches).
Son manager lui attribue une checklist (ensemble de tâches).

2) INSTALLATION
Il vous faut Docker, Git et PHP.
Créez un dossier et lancez 'git clone https://github.com/LOLIVE-Mikael/checklist/ .'.
Lancez 'docker-compose up --build'.
Allez dans le dossier app et lancez 'composer install'.
Installer ensuite NPM en lancant la commande 'npm install' puis 'npm run dev'.
Pour créer la base de données, entrez dans le container docker en tapant " docker exec -it php /bin/bash" (remplacer php par l'id de votre container docker si besoin)
une fois dans le container, lancez la commande 'php bin/console doctrine:schema:update --force'.
Pour insérer des données de test dans la base de données, lancez la commande 'symfony console doctrine:fixtures:load'.

Le projet est visualisable à cette adresse : http://127.0.0.1:8080/
La base de donnée est visualisable http://127.0.0.1:8899/

3) FONCTIONNALITES
Le technicien peut sélectionner une checklist et visionner les tâches associées à cette checklist.

Le manager peut sélectionner une checklist et visionner les tâches associées à cette checklist.
Il peut ajouter/supprimer une tâche à la checklist.
Il peut ajouter à la checklist une tâche encore non existante.
