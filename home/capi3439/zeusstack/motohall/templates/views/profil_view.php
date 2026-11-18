<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * PROFIL_VIEW.PHP — MON COMPTE
 * Créé le 20/09/2026 : chacun voit son profil et change son mot de passe.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$role = strtoupper(getRole());
$rolesLisibles = [
    'ADMIN'      => 'Administrateur',
    'DIRECTEUR'  => 'Direction',
    'VENDEUR'    => 'Vendeur',
    'MECANICIEN' => 'Mécanicien',
];
$nomComplet = trim((string)($monCompte['prenom'] ?? '') . ' ' . (string)($monCompte['nom'] ?? ''));
?>

<div class="apparition space-y-6">

    <!-- ─── MESSAGES ───────────────────────────────────────────────────── -->
    <?php if ($message): ?>
        <div class="carte flex items-center gap-3 border-emerald-500/30">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <p class="text-[0.88rem] text-emerald-300"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <div class="carte flex items-center gap-3 border-red-500/30">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 shrink-0"></i>
            <p class="text-[0.88rem] text-red-300"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- ─── MA FICHE ───────────────────────────────────────────────── -->
        <div class="carte">
            <div class="carte-entete">
                <h2 class="carte-titre">Mes informations</h2>
            </div>

            <div class="flex items-center gap-4 mb-5">
                <div class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center
                            justify-center text-[1rem] font-semibold text-slate-300 shrink-0">
                    <?= htmlspecialchars(strtoupper(substr((string)($_SESSION['username'] ?? 'U'), 0, 2)), ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="min-w-0">
                    <p class="text-[1rem] font-semibold text-white truncate">
                        <?= htmlspecialchars($nomComplet !== '' ? $nomComplet : (string)$_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="text-[0.82rem] text-slate-500 truncate">
                        <?= htmlspecialchars((string)($_SESSION['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>

            <dl class="space-y-3 text-[0.88rem]">
                <div class="flex items-center justify-between gap-4 py-2 border-b border-white/5">
                    <dt class="text-slate-500">Rôle</dt>
                    <dd><span class="badge badge-accent"><?= htmlspecialchars($rolesLisibles[$role] ?? $role, ENT_QUOTES, 'UTF-8') ?></span></dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-2 border-b border-white/5">
                    <dt class="text-slate-500">Adresse e-mail</dt>
                    <dd class="text-slate-300 truncate">
                        <?= htmlspecialchars((string)($monCompte['email'] ?? '') !== '' ? (string)$monCompte['email'] : '—', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-2 border-b border-white/5">
                    <dt class="text-slate-500">Compte créé le</dt>
                    <dd class="text-slate-300">
                        <?= htmlspecialchars(
                            !empty($monCompte['date_creation']) ? date('d/m/Y', strtotime((string)$monCompte['date_creation'])) : '—',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-2">
                    <dt class="text-slate-500">État du compte</dt>
                    <dd>
                        <?php if (!empty($monCompte['actif'])): ?>
                            <span class="badge badge-succes">Actif</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Désactivé</span>
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>

            <?php if (in_array($role, ['ADMIN', 'DIRECTEUR'], true)): ?>
            <p class="mt-5 text-[0.8rem] text-slate-500">
                Pour modifier les informations d’un autre membre, utilisez
                <a href="admin-users.php" class="text-[#38bdf8] hover:underline">Membres du personnel</a>.
            </p>
            <?php endif; ?>
        </div>

        <!-- ─── CHANGER MON MOT DE PASSE ───────────────────────────────── -->
        <div class="carte">
            <div class="carte-entete">
                <h2 class="carte-titre">Changer mon mot de passe</h2>
            </div>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf(), ENT_QUOTES, 'UTF-8') ?>">

                <div>
                    <label class="etiquette" for="actuel">Mot de passe actuel</label>
                    <input type="password" id="actuel" name="actuel" required
                           class="champ" autocomplete="current-password">
                </div>

                <div>
                    <label class="etiquette" for="nouveau">Nouveau mot de passe</label>
                    <input type="password" id="nouveau" name="nouveau" required minlength="8"
                           class="champ" autocomplete="new-password">
                    <p class="mt-1.5 text-[0.75rem] text-slate-500">Au moins 8 caractères.</p>
                </div>

                <div>
                    <label class="etiquette" for="confirme">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirme" name="confirme" required minlength="8"
                           class="champ" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-accent btn-bloc">
                    <i data-lucide="key-round" class="w-4 h-4"></i>
                    <span>Enregistrer le nouveau mot de passe</span>
                </button>
            </form>

            <p class="mt-4 text-[0.78rem] text-slate-500">
                Votre mot de passe est enregistré sous forme hachée&nbsp;: personne — pas même la
                Direction — ne peut le relire.
            </p>
        </div>
    </div>
</div>
