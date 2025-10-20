pour demarer le serveur local de symfony:

```
symfony serve -d
```

poru stoper le serveur local de symfony:

```
symfony server:stop
```

poru effacer les cache : (à chaque fois, on change quelque ligne dans le config (yaml))

```
php bin/console cahce:clear
```

pour actualiser l'autoloade de composer : (à chaque fois, on change le namespace)

```
composer dump-autoload
```

pour crée une Entity :

```
php bin/console make:entity Admin\\PersonnelUser\\Personnel
```

pour une relalation :

```
What class should this entity be related to?:
 > App\Entity\Dom\SousTypeDocument

```

pour la migration : (creation de fichier de migration )

```
php bin/console make:migration
```

pour creation ou modification table dans le base de donnée :

```
php bin/console doctrine:migrations:migrate
```
