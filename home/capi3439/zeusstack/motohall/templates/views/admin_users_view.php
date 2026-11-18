<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ADMIN_USERS_VIEW.PHP — MEMBRES DU STAFF
 * Refonte du 20/09/2026.
 *
 * !! DEUX CORRECTIONS DE FOND, EN PLUS DU VISUEL :
 *    1. SÉCURITÉ : « <?= $success ?> » et « <?= $error ?> » étaient affichés
 *       SANS échappement. Le message peut contenir un nom saisi ou le texte
 *       d'une erreur de base de données. Ils passent maintenant par
 *       htmlspecialchars().
 *    2. Les valeurs injectées dans les attributs Alpine (@click) passent par
 *       json_encode() : c'est la seule façon correcte d'y placer du texte
 *       arbitraire (addslashes ne protège pas un attribut HTML).
 * ═══════════════════════════════════════════════════════════════════════════
 */

$rolesAffiches = [
    'ADMIN'      => ['libelle' => 'Administrateur', 'classe' => 'badge-danger'],
    'DIRECTEUR'  => ['libelle' => 'Direction',      'classe' => 'badge-accent'],
    'VENDEUR'    => ['libelle' => 'Vendeur',        'classe' => 'badge-succes'],
    'MECANICIEN' => ['libelle' => 'Mécanicien',     'classe' => 'badge-alerte'],
];

/** Échappe une valeur pour l'afficher dans la page. */
function vu($texte): string
{
    return htmlspecialchars((string)$texte, ENT_QUOTES, 'UTF-8');
}

/** Prépare une valeur pour un attribut contenant du JavaScript (Alpine). */
function vuJs($texte): string
{
    return htmlspecialchars(json_encode((string)$texte, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
}
?>

<div class="apparition space-y-6">

    <!-- ─── EN-TÊTE ────────────────────────────────────────────────────── -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="font-display text-[1.15rem] font-semibold text-white">Membres du personnel</h2>
            <p class="text-[0.85rem] text-slate-400 mt-1">
                Créez les comptes, ajustez les droits et activez ou désactivez un accès.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="badge"><?= count($users) ?> compte<?= count($users) > 1 ? 's' : '' ?></span>
            <button type="button" @click="$dispatch('open-modal-add')" class="btn btn-accent">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Nouveau membre</span>
            </button>
        </div>
    </div>

    <!-- ─── MESSAGES ───────────────────────────────────────────────────── -->
    <?php if ($success): ?>
        <div class="carte flex items-center gap-3 border-emerald-500/30">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <p class="text-[0.88rem] text-emerald-300"><?= vu($success) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="carte flex items-center gap-3 border-red-500/30">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 shrink-0"></i>
            <p class="text-[0.88rem] text-red-300"><?= vu($error) ?></p>
        </div>
    <?php endif; ?>

    <!-- ─── FENÊTRE : NOUVEAU MEMBRE ───────────────────────────────────── -->
    <template x-teleport="body">
        <div x-show="modalAdd" x-data="{ modalAdd: false }"
             @open-modal-add.window="modalAdd = true"
             x-transition.opacity
             class="fixed inset-0 z-[999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-6 overflow-y-auto">

            <div @click.away="modalAdd = false" class="carte w-full max-w-lg my-8 apparition">
                <div class="carte-entete">
                    <h3 class="carte-titre">Nouveau membre</h3>
                    <button type="button" @click="modalAdd = false" class="text-slate-500 hover:text-slate-300"
                            aria-label="Fermer la fenêtre">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?= vu(csrf()) ?>">

                    <div>
                        <label class="etiquette" for="add-username">Identifiant</label>
                        <input type="text" id="add-username" name="username" required
                               class="champ" placeholder="prenom.nom" autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="etiquette" for="add-prenom">Prénom</label>
                            <input type="text" id="add-prenom" name="prenom" required class="champ">
                        </div>
                        <div>
                            <label class="etiquette" for="add-nom">Nom</label>
                            <input type="text" id="add-nom" name="nom" required class="champ">
                        </div>
                    </div>

                    <div>
                        <label class="etiquette" for="add-email">Adresse e-mail <span class="faible">(facultatif)</span></label>
                        <input type="email" id="add-email" name="email" class="champ" placeholder="prenom@motohall.fr">
                    </div>

                    <div>
                        <label class="etiquette" for="add-password">Mot de passe</label>
                        <input type="password" id="add-password" name="password" required
                               class="champ" autocomplete="new-password">
                        <p class="mt-1.5 text-[0.75rem] text-slate-500">
                            Il sera enregistré sous forme hachée&nbsp;: personne ne pourra le relire.
                        </p>
                    </div>

                    <div>
                        <label class="etiquette" for="add-role">Rôle</label>
                        <select id="add-role" name="role" required class="champ">
                            <option value="VENDEUR">Vendeur</option>
                            <option value="MECANICIEN">Mécanicien</option>
                            <option value="DIRECTEUR">Direction</option>
                            <option value="ADMIN">Administrateur</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="modalAdd = false" class="btn btn-neutre flex-1">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-accent flex-1">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Créer le compte</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ─── FENÊTRE : MODIFIER UN MEMBRE ───────────────────────────────── -->
    <template x-teleport="body">
        <div x-show="modalEdit"
             x-data="{ modalEdit: false,
                       u: { id: '', username: '', prenom: '', nom: '', email: '', role: '' } }"
             @open-modal-edit.window="modalEdit = true; u = $event.detail"
             x-transition.opacity
             class="fixed inset-0 z-[999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-6 overflow-y-auto">

            <div @click.away="modalEdit = false" class="carte w-full max-w-lg my-8 apparition">
                <div class="carte-entete">
                    <h3 class="carte-titre">Modifier le membre</h3>
                    <button type="button" @click="modalEdit = false" class="text-slate-500 hover:text-slate-300"
                            aria-label="Fermer la fenêtre">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="csrf_token" value="<?= vu(csrf()) ?>">
                    <input type="hidden" name="user_id" :value="u.id">

                    <div>
                        <label class="etiquette" for="edit-username">Identifiant</label>
                        <input type="text" id="edit-username" name="username" required class="champ" :value="u.username">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="etiquette" for="edit-prenom">Prénom</label>
                            <input type="text" id="edit-prenom" name="prenom" class="champ" :value="u.prenom">
                        </div>
                        <div>
                            <label class="etiquette" for="edit-nom">Nom</label>
                            <input type="text" id="edit-nom" name="nom" class="champ" :value="u.nom">
                        </div>
                    </div>

                    <div>
                        <label class="etiquette" for="edit-email">Adresse e-mail <span class="faible">(facultatif)</span></label>
                        <input type="email" id="edit-email" name="email" class="champ" :value="u.email">
                    </div>

                    <div>
                        <label class="etiquette" for="edit-password">Nouveau mot de passe <span class="faible">(laisser vide pour ne pas changer)</span></label>
                        <input type="password" id="edit-password" name="password" class="champ" autocomplete="new-password">
                    </div>

                    <div>
                        <label class="etiquette" for="edit-role">Rôle</label>
                        <select id="edit-role" name="role" required class="champ" :value="u.role">
                            <option value="VENDEUR">Vendeur</option>
                            <option value="MECANICIEN">Mécanicien</option>
                            <option value="DIRECTEUR">Direction</option>
                            <option value="ADMIN">Administrateur</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="modalEdit = false" class="btn btn-neutre flex-1">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-accent flex-1">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Enregistrer</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ─── LISTE DES MEMBRES ──────────────────────────────────────────── -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php foreach ($users as $u): ?>
        <?php
            $role       = strtoupper((string)$u['role']);
            $infosRole  = $rolesAffiches[$role] ?? ['libelle' => $role, 'classe' => 'badge'];
            $nomComplet = trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''));
            $initiales  = strtoupper(substr((string)($u['prenom'] ?: $u['username']), 0, 1)
                                   . substr((string)($u['nom'] ?? ''), 0, 1));
        ?>
        <div class="carte flex flex-col">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center
                                justify-center text-[0.85rem] font-semibold text-slate-300 shrink-0">
                        <?= vu($initiales) ?>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[0.95rem] font-semibold text-white truncate">
                            <?= vu($nomComplet !== '' ? $nomComplet : $u['username']) ?>
                        </p>
                        <p class="text-[0.78rem] text-slate-500 truncate"><?= vu($u['username']) ?></p>
                    </div>
                </div>

                <span class="badge <?= vu($infosRole['classe']) ?> shrink-0"><?= vu($infosRole['libelle']) ?></span>
            </div>

            <div class="space-y-1.5 text-[0.82rem] mb-4">
                <p class="flex items-center gap-2 text-slate-400">
                    <i data-lucide="mail" class="w-3.5 h-3.5 shrink-0"></i>
                    <span class="truncate"><?= vu($u['email'] ?: 'aucune adresse enregistrée') ?></span>
                </p>
                <p class="flex items-center gap-2 text-slate-400">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 shrink-0"></i>
                    <span>Compte créé le <?= vu(date('d/m/Y', strtotime((string)$u['date_creation']))) ?></span>
                </p>
                <p class="flex items-center gap-2">
                    <?php if ($u['actif']): ?>
                        <span class="badge badge-succes">Actif</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Désactivé</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="flex items-center justify-between gap-2 pt-4 mt-auto border-t border-white/10">
                <span class="text-[0.72rem] text-slate-600">Compte n° <?= vu(str_pad((string)$u['id'], 3, '0', STR_PAD_LEFT)) ?></span>

                <div class="flex gap-2">
                    <button type="button"
                            @click="showConfirm('<?= $u['actif'] ? 'Désactiver' : 'Réactiver' ?> ce compte\u00A0?', () => { window.location.href = '?toggle=<?= (int)$u['id'] ?>&csrf_token=<?= vu(csrf()) ?>' })"
                            class="btn btn-neutre !px-3"
                            title="<?= $u['actif'] ? 'Désactiver ce compte' : 'Réactiver ce compte' ?>">
                        <i data-lucide="<?= $u['actif'] ? 'user-x' : 'user-check' ?>" class="w-4 h-4"></i>
                    </button>

                    <button type="button"
                            @click="$dispatch('open-modal-edit', {
                                id: '<?= (int)$u['id'] ?>',
                                username: <?= vuJs($u['username']) ?>,
                                prenom: <?= vuJs($u['prenom'] ?? '') ?>,
                                nom: <?= vuJs($u['nom'] ?? '') ?>,
                                email: <?= vuJs($u['email'] ?? '') ?>,
                                role: <?= vuJs($u['role']) ?>
                            })"
                            class="btn btn-neutre !px-3" title="Modifier ce membre">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
