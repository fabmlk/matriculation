# Matriculation
Cette librairie permet de vérifier et de formater les immatriculations (FNI et SIV) françaises.

## Installation
```bash
composer require tms/matriculation
```

## Fonctionnalités
### Formatage
* `__toString()` : renvoi d'immatriculation formatée si valide, sinon juste en majuscule.

### SIV
* `isMatriculationSIV()` : indique si l'immatriculation est valide pour le SIV
* `isMatriculationSIVNormal()` : SIV série 'normale'
* `isMatriculationSIVWGarage()` : SIV série 'W garage'
* `isMatriculationSIVWW()` : SIV série 'provisoire'
* `isMatriculationSIVCyclo()` : SIV série 'cyclomoteur' (avant 01/07/2015)

### FNI
* `isMatriculationFNI()` : indique si l'immatriculation est valide pour le FNI
* `isMatriculationFNINormal()` : FNI série 'normale'
* `isMatriculationFNIWgarage()` : FNI série 'W garage'
* `isMatriculationFNIWW()` :  FNI série 'provisoire'

## Utilisation
```php
use Tms\Matriculation;

$immat = new Matriculation('ab-123-cd');

echo 'Immatriculation correctement formatée : '.$immat;

if ($immat->isMatriculationSIV()) {
    echo 'Cette immatriculation est au format SIV.';
} elseif ($immat->isMatriculationFNI()) {
    echo 'Cette immatriculation est au format FNI.';
} else {
    echo 'Cette immatriculation est incorrecte.';
}
```

## Versions
### v0.1 (30/08/2018)
* Première version
