<?php
/**
 * Audit du 18/11/2026 — preset A : @PSR12 seul.
 * --dry-run uniquement. Aucun fichier du site n'est modifié.
 */

$racine = 'C:/zeus_dev/zeus_projets/deepseek-harness/sites-web/moto-hall_laravel/home/capi3439';

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
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
