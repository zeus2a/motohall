<?php
/**
 * Audit du 18/11/2026 — preset B : @PSR12 + declare_strict_types
 * + ordered_imports + no_unused_imports.
 * --dry-run uniquement. Aucun fichier du site n'est modifié.
 *
 * RiskyAllowed est nécessaire : declare_strict_types est une règle « risquée »
 * (elle change le comportement à l'exécution des fichiers qu'elle touche).
 */

$racine = 'C:/zeus_dev/zeus_projets/deepseek-harness/sites-web/moto-hall_laravel/home/capi3439';

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'              => true,
        'declare_strict_types' => true,
        'ordered_imports'     => true,
        'no_unused_imports'   => true,
    ])
    ->setFinder(
        (new PhpCsFixer\Finder())
            ->in([
                $racine . '/public_html/motohall.zeusstack.fr',
                $racine . '/zeusstack/motohall/includes',
                $racine . '/zeusstack/motohall/templates',
            ])
            ->name('*.php')
    )
    ->setUsingCache(false);
