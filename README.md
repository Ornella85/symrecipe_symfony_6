### TIPS

bootswatch

gitmoji.dev



### Bundles :
composer require knplabs/knp-paginator-bundle

composer require fakerphp/faker --dev,
composer require doctrine/doctrine-fixtures-bundle --dev

composer require karser/karser-recaptcha3-bundle

composer require easycorp/easyadmin-bundle

composer require friendsofsymfony/ckeditor-bundle

composer require --dev symfony/test-pack

### ligne de commande

#### Faker - générer les fixtures : php bin/console --env=dev doctrine:fixtures:load ou php bin/console doctrine:fixtures:load 
#### EasyAdmin - si le css EasyAdmin pose problème : php bin/console assets:install --symlink
#### CkEditor - install :  php bin/console ckeditor:install
#### CkEditor - asset sans webpack :  php bin/console assets:install public
#### phpUnit - pour les tests : php bin/phpunit , php bin/console make:test , php bin/console d:d:c --env=test



### Liens
#### Goggle Recaptcha : https://developers.google.com/recaptcha/docs/v3?hl=fr
#### doc EasyAdmin : https://symfony.com/bundles/EasyAdminBundle/current/index.html