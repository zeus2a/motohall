<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * DASHBOARD_VIEW.PHP — TABLEAU DE BORD DE LA DIRECTION
 * Refonte du 20/09/2026.
 *
 * AVANT : quatre compteurs dont trois inventés, un « journal d'activité » avec
 *         des heures écrites en dur, des halos de néon et des textes de 9 px.
 * APRÈS : quatre indicateurs réellement mesurés, les prochains rendez-vous de
 *         l'atelier (la donnée la plus utile au quotidien), et les accès rapides.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$indicateurs = [
    [
        'valeur'  => (string)$docCount,
        'libelle' => 'Documents classés',
        'detail'  => 'Atelier ' . $docsParService['atelier']
                   . ' · Magasin ' . $docsParService['magasin']
                   . ' · Direction ' . $docsParService['direction'],
        'icone'   => 'folder-open',
    ],
    [
        'valeur'  => (string)$agentsActifs,
        'libelle' => 'Membres actifs',
        'detail'  => $agentsTotal > $agentsActifs
                     ? ($agentsTotal - $agentsActifs) . ' compte' . (($agentsTotal - $agentsActifs) > 1 ? 's' : '') . ' désactivé' . (($agentsTotal - $agentsActifs) > 1 ? 's' : '')
                     : 'Tous les comptes sont actifs',
        'icone'   => 'users',
    ],
    [
        'valeur'  => (string)$rdvAujourdhui,
        'libelle' => "Rendez-vous aujourd’hui",
        'detail'  => 'Atelier et comptoir',
        'icone'   => 'calendar-clock',
    ],
    [
        'valeur'  => (string)$rdvSemaine,
        'libelle' => 'Rendez-vous cette semaine',
        'detail'  => 'Du lundi au dimanche',
        'icone'   => 'calendar-days',
    ],
];

$etats = [
    'PENDING'     => ['libelle' => 'À faire',  'classe' => 'badge-alerte'],
    'IN_PROGRESS' => ['libelle' => 'En cours', 'classe' => 'badge-accent'],
    'DONE'        => ['libelle' => 'Terminé',  'classe' => 'badge-succes'],
    'CANCELED'    => ['libelle' => 'Annulé',   'classe' => 'badge-danger'],
];
?>

<div class="apparition space-y-6">

    <!-- ─── INDICATEURS ────────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($indicateurs as $indicateur): ?>
        <div class="carte">
            <div class="flex items-start justify-between mb-3">
                <div class="w-9 h-9 rounded-[10px] bg-white/5 border border-white/10
                            flex items-center justify-center">
                    <i data-lucide="<?= htmlspecialchars($indicateur['icone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="w-4 h-4 text-[#38bdf8]"></i>
                </div>
            </div>
            <p class="kpi-valeur"><?= htmlspecialchars($indicateur['valeur'], ENT_QUOTES, 'UTF-8') ?></p>
            <p class="kpi-libelle"><?= htmlspecialchars($indicateur['libelle'], ENT_QUOTES, 'UTF-8') ?></p>
            <p class="mt-2 text-[0.75rem] text-slate-500"><?= htmlspecialchars($indicateur['detail'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ─── PROCHAINS RENDEZ-VOUS ──────────────────────────────────── -->
        <div class="carte lg:col-span-2">
            <div class="carte-entete">
                <h2 class="carte-titre">Prochains rendez-vous</h2>
                <a href="agenda.php" @click.prevent="navigate('agenda.php')"
                   class="text-[0.8rem] font-semibold text-[#38bdf8] inline-flex items-center gap-1.5">
                    Ouvrir l’agenda
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <?php if (!$prochainsRdv): ?>
                <div class="py-10 text-center">
                    <i data-lucide="calendar-off" class="w-6 h-6 text-slate-600 mx-auto mb-3"></i>
                    <p class="text-[0.85rem] text-slate-400">Aucun rendez-vous à venir.</p>
                    <p class="text-[0.78rem] text-slate-500 mt-1">
                        Les rendez-vous se créent depuis l’agenda de l’atelier.
                    </p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="tableau">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Titre</th>
                                <th>Client</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prochainsRdv as $rdv): ?>
                            <?php $etat = $etats[$rdv['status']] ?? ['libelle' => $rdv['status'], 'classe' => 'badge']; ?>
                            <tr>
                                <td class="whitespace-nowrap text-slate-300">
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($rdv['event_date'])), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="whitespace-nowrap tabulaire text-slate-300">
                                    <?= htmlspecialchars(substr($rdv['start_time'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="text-slate-200">
                                    <?= htmlspecialchars($rdv['title'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($rdv['client_name'] ?: '—', ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($rdv['plate_number'])): ?>
                                        <span class="ml-2 badge"><?= htmlspecialchars($rdv['plate_number'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= htmlspecialchars($etat['classe'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($etat['libelle'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- ─── ACCÈS RAPIDES ──────────────────────────────────────────── -->
        <div class="carte">
            <div class="carte-entete">
                <h2 class="carte-titre">Accès rapides</h2>
            </div>

            <div class="space-y-2">
                <a href="admin-users.php" @click.prevent="navigate('admin-users.php')"
                   class="nav-lien">
                    <span class="nav-vignette"><i data-lucide="users"></i></span>
                    <span>Membres du personnel</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto opacity-40"></i>
                </a>

                <a href="documents.php" @click.prevent="navigate('documents.php')"
                   class="nav-lien">
                    <span class="nav-vignette"><i data-lucide="folder-open"></i></span>
                    <span>Documentation</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto opacity-40"></i>
                </a>

                <a href="comptoir.php" @click.prevent="navigate('comptoir.php')"
                   class="nav-lien">
                    <span class="nav-vignette"><i data-lucide="monitor"></i></span>
                    <span>Comptoir</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto opacity-40"></i>
                </a>

                <a href="atelier.php" @click.prevent="navigate('atelier.php')"
                   class="nav-lien">
                    <span class="nav-vignette"><i data-lucide="wrench"></i></span>
                    <span>Atelier</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto opacity-40"></i>
                </a>
            </div>
        </div>
    </div>
</div>
