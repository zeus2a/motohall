<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * DOCUMENTS_VIEW.PHP v5.0 - FRAGMENT AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */
?>

<div class="animate-in fade-in slide-in-from-bottom-8 duration-1000 space-y-8">
    
    <!-- EN-TÊTE -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="font-display text-[1.15rem] font-semibold text-white">Documentation</h2>
            <p class="text-[0.85rem] text-slate-400 mt-1">
                Manuels, procédures et formulaires, classés par service.
            </p>
        </div>
        <button type="button" onclick="window.location.reload()" class="btn btn-neutre" title="Recharger la liste">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            <span>Actualiser</span>
        </button>
    </div>

    <!-- Explorateur de documents -->
    <div class="space-y-8"
         x-data="{
             currentPath: '',
             folders: [],
             files: [],
             loading: false,
             async openFolder(path) {
                 this.loading = true;
                 try {
                     const response = await fetch(`?ajax_data=${encodeURIComponent(path)}`);
                     if (!response.ok) throw new Error('Network Response Fail');
                     const payload = await response.json();
                     if (payload.error) throw new Error(payload.error);
                     this.folders = payload.folders;
                     this.files = payload.files;
                     this.currentPath = path;
                     this.loading = false;
                     setTimeout(() => lucide.createIcons(), 50);
                 } catch(err) {
                     this.loading = false;
                     alert('Erreur d\'accès au dossier : ' + err.message);
                 }
             },
             get breadcrumbs() {
                 if(!this.currentPath) return [];
                 let parts = this.currentPath.split('/');
                 let accu = '';
                 return parts.map(p => {
                     accu += accu ? '/' + p : p;
                     return { name: p, path: accu };
                 });
             },
             getFileIcon(ext) {
                 const icons = { 'pdf':'file-text', 'jpg':'image', 'png':'image', 'jpeg':'image', 'zip':'archive', 'rar':'archive', 'csv':'file-spreadsheet', 'xlsx':'file-spreadsheet' };
                 return icons[ext] || 'file';
             },
             getFileColor(ext) {
                 const colors = { 'pdf':'text-red-400', 'zip':'text-amber-400', 'rar':'text-amber-400', 'csv':'text-emerald-400', 'xlsx':'text-emerald-400' };
                 return colors[ext] || 'text-cyan-400';
             }
         }"
         x-init="<?= isset($_GET['dir']) ? "openFolder('" . htmlspecialchars(strtolower((string)$_GET['dir']), ENT_QUOTES) . "')" : '' ?>">

        <!-- FIL D'ARIANE -->
        <div class="carte flex flex-wrap items-center gap-2 mb-6" x-show="currentPath" style="display:none;">
            <button type="button" @click="currentPath = ''"
                    class="p-2 rounded-[9px] text-slate-400 hover:text-white hover:bg-white/5 transition-colors"
                    title="Revenir à la racine">
                <i data-lucide="home" class="w-4 h-4"></i>
            </button>
            <template x-for="b in breadcrumbs">
                <div class="flex items-center gap-2">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600"></i>
                    <button type="button" @click="openFolder(b.path)"
                            class="text-[0.85rem] text-slate-300 hover:text-[#38bdf8] transition-colors"
                            x-text="b.name"></button>
                </div>
            </template>
        </div>

        <!-- CHARGEMENT -->
        <div x-show="loading" class="py-16 text-center" style="display:none;">
            <i data-lucide="loader-2" class="w-6 h-6 animate-spin text-[#38bdf8] mx-auto mb-3"></i>
            <p class="text-[0.85rem] text-slate-400">Chargement du dossier…</p>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════
             DOSSIERS RACINE
             !! CORRIGÉ LE 20/09/2026 : le chemin envoyé au serveur était écrit
                en MAJUSCULES (« ATELIER »), alors que les dossiers s'appellent
                « atelier » sur le serveur. Sur Windows la casse ne compte pas,
                donc cela fonctionnait sur le poste de Frédéric ; sur le serveur
                Linux, cliquer sur une carte renvoyait « 400 Bad Request »
                (mesuré en ligne). Le CHEMIN est maintenant le vrai nom du
                dossier, et le libellé affiché reste en français.
             ═══════════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-show="!currentPath && !loading">
            <?php
            $dossiersRacine = [
                [
                    'dossier' => 'atelier',
                    'titre'   => 'Atelier',
                    'icone'   => 'wrench',
                    'texte'   => 'Manuels, procédures et fiches techniques.',
                    'roles'   => ['ADMIN', 'DIRECTEUR', 'VENDEUR', 'MECANICIEN'],
                ],
                [
                    'dossier' => 'direction',
                    'titre'   => 'Direction',
                    'icone'   => 'shield-check',
                    'texte'   => 'Documents stratégiques et ressources humaines. Accès restreint.',
                    'roles'   => ['ADMIN', 'DIRECTEUR'],
                ],
                [
                    'dossier' => 'magasin',
                    'titre'   => 'Magasin',
                    'icone'   => 'package',
                    'texte'   => 'Inventaires, fournisseurs et documents commerciaux.',
                    'roles'   => ['ADMIN', 'DIRECTEUR', 'VENDEUR', 'MECANICIEN'],
                ],
            ];
            $roleCourant = strtoupper(getRole());
            foreach ($dossiersRacine as $d):
                if (!in_array($roleCourant, $d['roles'], true)) {
                    continue;
                }
            ?>
            <div @click="openFolder('<?= htmlspecialchars($d['dossier'], ENT_QUOTES, 'UTF-8') ?>')"
                 class="carte cursor-pointer transition-colors duration-200 hover:border-[#38bdf8]/40">
                <div class="w-10 h-10 rounded-[10px] bg-white/5 border border-white/10
                            flex items-center justify-center mb-4">
                    <i data-lucide="<?= htmlspecialchars($d['icone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="w-5 h-5 text-[#38bdf8]"></i>
                </div>
                <h3 class="font-display text-[1rem] font-semibold text-white mb-1">
                    <?= htmlspecialchars($d['titre'], ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <p class="text-[0.85rem] text-slate-400 leading-relaxed">
                    <?= htmlspecialchars($d['texte'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CONTENU D'UN DOSSIER -->
        <div x-show="currentPath && !loading" style="display:none;" class="carte min-h-[260px]">

            <!-- SOUS-DOSSIERS -->
            <template x-if="folders.length > 0">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                    <template x-for="f in folders">
                        <div @click="openFolder(f.path)"
                             class="flex items-center gap-3 px-4 py-3 rounded-[12px] border border-white/10
                                    bg-white/[0.02] hover:bg-white/[0.05] hover:border-[#38bdf8]/40
                                    cursor-pointer transition-colors duration-200">
                            <i data-lucide="folder" class="w-5 h-5 text-[#38bdf8] shrink-0"></i>
                            <span class="text-[0.88rem] text-slate-200 truncate" x-text="f.name"></span>
                        </div>
                    </template>
                </div>
            </template>

            <!-- FICHIERS -->
            <template x-if="files.length > 0">
                <div class="space-y-2" :class="folders.length > 0 ? 'border-t border-white/10 pt-4' : ''">
                    <template x-for="f in files">
                        <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-[12px]
                                    border border-white/10 bg-white/[0.02] hover:bg-white/[0.05]
                                    transition-colors duration-200">
                            <div class="flex items-center gap-3 min-w-0 cursor-pointer flex-1"
                                 @click="$dispatch('open-viewer', { url: f.url, type: f.ext, name: f.name })">
                                <i :data-lucide="getFileIcon(f.ext)" class="w-5 h-5 shrink-0" :class="getFileColor(f.ext)"></i>
                                <div class="min-w-0">
                                    <p class="text-[0.88rem] text-slate-200 truncate" x-text="f.name"></p>
                                    <p class="text-[0.72rem] text-slate-500 uppercase" x-text="f.ext"></p>
                                </div>
                            </div>
                            <a :href="f.url" download
                               class="btn btn-neutre !px-3 shrink-0" title="Télécharger ce fichier">
                                <i data-lucide="download" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </template>
                </div>
            </template>

            <!-- DOSSIER VIDE -->
            <template x-if="folders.length === 0 && files.length === 0">
                <div class="py-16 text-center">
                    <i data-lucide="folder-open" class="w-6 h-6 text-slate-600 mx-auto mb-3"></i>
                    <p class="text-[0.88rem] text-slate-400">Ce dossier ne contient aucun document.</p>
                </div>
            </template>
        </div>
    </div>

</div>

    <!-- VISIONNEUSE DE DOCUMENTS (MODAL) -->
    <template x-teleport="body">
        <div x-show="viewer.show" 
             x-data="{ 
                viewer: { show: false, url: '', type: '', name: '' } 
             }"
             @open-viewer.window="viewer = $event.detail; viewer.show = true;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-10 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-10 scale-95"
             class="fixed inset-0 z-[9999] bg-[#030712]/95 backdrop-blur-2xl flex flex-col p-4 sm:p-8">
             
            <!-- VIEWER HEADER -->
            <div class="flex items-center justify-between mb-6 glass p-4 rounded-3xl border border-white/5 shadow-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20 shadow-[0_0_20px_rgba(6,182,212,0.2)]">
                        <i data-lucide="eye" class="w-6 h-6 text-cyan-400"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-display font-black uppercase tracking-widest flex items-center gap-3">
                            <span x-text="viewer.name"></span>
                        </h4>
                        <p class="text-[10px] text-cyan-400/50 uppercase tracking-[0.4em] font-bold mt-1">Visionneuse de documents</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a :href="viewer.url" download class="px-6 py-4 rounded-xl bg-white/5 hover:bg-emerald-500/20 border border-white/5 hover:border-emerald-500/40 text-emerald-500/50 hover:text-emerald-400 transition-all font-black uppercase tracking-widest text-[10px] flex items-center gap-3">
                        <i data-lucide="hard-drive-download" class="w-4 h-4"></i> Exporter
                    </a>
                    <button @click="viewer.show = false" class="px-6 py-4 rounded-xl bg-red-500 hover:bg-red-400 text-black shadow-lg shadow-red-500/20 transition-all font-black uppercase tracking-widest text-[10px] flex items-center gap-3">
                        <i data-lucide="x" class="w-4 h-4"></i> Fermer
                    </button>
                </div>
            </div>

            <!-- VIEWER CONTENT ENGINE -->
            <div class="flex-1 glass rounded-[3rem] border border-white/5 overflow-hidden relative flex items-center justify-center shadow-inner">
                <!-- Background Decoration -->
                <i data-lucide="shield" class="absolute w-64 h-64 text-white/[0.02] animate-pulse-slow pointer-events-none"></i>
                
                <!-- TYPE: PDF / TXT -->
                <template x-if="['pdf', 'txt'].includes(viewer.type)">
                    <iframe :src="viewer.url" class="absolute inset-0 w-full h-full border-0 bg-white shadow-2xl relative z-10" title="Visionneuse de documents"></iframe>
                </template>
                
                <!-- TYPE: IMAGE -->
                <template x-if="['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(viewer.type)">
                    <img :src="viewer.url" class="max-w-full max-h-[90vh] object-contain relative z-10 shadow-2xl rounded-2xl" alt="Visuel Sécurisé">
                </template>
                
                <!-- TYPE: INCONNU / EXTERNE (WORD, EXCEL, CSV, ZIP) -->
                <template x-if="!['pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'webp'].includes(viewer.type)">
                    <div class="relative z-10 text-center p-12 max-w-2xl bg-black/40 rounded-[3rem] border border-white/5 shadow-2xl backdrop-blur-md">
                        <div class="w-32 h-32 bg-amber-500/10 border border-amber-500/20 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-amber-400 shadow-[0_0_80px_rgba(245,158,11,0.2)]">
                            <i data-lucide="file-warning" class="w-16 h-16"></i>
                        </div>
                        <h3 class="text-3xl font-display font-black text-white uppercase tracking-widest mb-6">Format Non-Visualisable</h3>
                        <p class="text-[11px] text-white/40 leading-loose font-bold mb-10 w-4/5 mx-auto uppercase tracking-widest">Le système interne ne dispose pas des décodeurs pour ce type de fichier. L'ouverture via l'application de bureau dédiée (Excel, Word, Winrar) est obligatoire.</p>
                        <a :href="viewer.url" download class="inline-flex glass px-10 py-5 rounded-[2rem] border-white/10 hover:border-amber-500 hover:bg-amber-500 hover:text-black font-display font-black uppercase text-xs tracking-[0.3em] transition-all gap-4 shadow-xl hover:shadow-[0_0_50px_rgba(245,158,11,0.4)]">
                            <i data-lucide="download-cloud" class="w-5 h-5"></i> Extraire le Fichier
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </template>


