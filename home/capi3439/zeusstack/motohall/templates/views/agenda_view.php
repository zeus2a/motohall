<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * AGENDA_VIEW.PHP v6.0 - LE MOTEUR NEXUS PLANNING AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */
?>

<div class="animate-in fade-in slide-in-from-bottom-8 duration-1000 h-full flex flex-col"
     x-data="{
         viewDate: new Date(),
         events: [],
         loading: false,
         showModal: false,
         formData: { title:'', client_name:'', plate_number:'', event_date:'', start_time:'08:00', end_time:'09:00', mechanic_name:'' },
         
         get currentWeekStart() {
             let d = new Date(this.viewDate);
             let day = d.getDay();
             let diff = d.getDate() - day + (day === 0 ? -6 : 1);
             return new Date(d.setDate(diff));
         },
         
         get weekDays() {
             let start = this.currentWeekStart;
             let days = [];
             for(let i=0; i<7; i++) {
                 let d = new Date(start);
                 d.setDate(start.getDate() + i);
                 days.push(d);
             }
             return days;
         },
         
         formatDateSQL(d) {
             return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
         },
         
         formatDayName(d) {
             return d.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'short' });
         },
         
         async loadEvents() {
             this.loading = true;
             let startSql = this.formatDateSQL(this.weekDays[0]);
             let endSql = this.formatDateSQL(this.weekDays[6]);
             
             try {
                 let res = await fetch(`agenda.php?ajax_data=1&start=${startSql}&end=${endSql}&_=${Date.now()}`);
                 this.events = await res.json();
             } catch(e) {
                 console.error(e);
             }
             this.loading = false;
         },
         
         getEventsForDay(d) {
             let sqlDate = this.formatDateSQL(d);
             return this.events.filter(e => e.event_date === sqlDate);
         },
         
         async submitEvent() {
             let fd = new FormData();
             fd.append('action', 'add_event');
             for(let k in this.formData) fd.append(k, this.formData[k]);
             
             try {
                 let res = await fetch('agenda.php', { method: 'POST', body: fd });
                 let data = await res.json();
                 if(data.success) {
                     this.showModal = false;
                     this.formData.title = '';
                     this.formData.client_name = '';
                     this.formData.plate_number = '';
                     this.loadEvents();
                 } else {
                     alert(data.error);
                 }
             } catch(e) {
                 alert('Erreur: ' + e);
             }
         },
         
         init() {
             this.loadEvents();
             this.$watch('events', () => {
                 setTimeout(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
             });
         },
         
         prevWeek() {
             let d = new Date(this.viewDate);
             d.setDate(d.getDate() - 7);
             this.viewDate = d;
             this.loadEvents();
         },
         
         nextWeek() {
             let d = new Date(this.viewDate);
             d.setDate(d.getDate() + 7);
             this.viewDate = d;
             this.loadEvents();
         }
     }"
      x-init="init()">

    <!-- EN-TÊTE -->
    <div class="carte flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-[10px] bg-white/5 border border-white/10 flex items-center justify-center shrink-0">
                <i data-lucide="calendar-clock" class="w-5 h-5 text-[#38bdf8]"></i>
            </div>
            <div>
                <h2 class="font-display text-[1.15rem] font-semibold text-white">Planning de l'atelier</h2>
                <p class="text-[0.82rem] text-slate-400 mt-0.5">
                    Vue semaine — les rendez-vous se créent avec le bouton à droite.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-1 rounded-[12px] border border-white/10 bg-white/[0.03] px-1 py-1">
                <button type="button" @click="prevWeek()"
                        class="p-2 rounded-[9px] text-slate-400 hover:text-white hover:bg-white/5 transition-colors"
                        title="Semaine précédente">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
                <div class="px-3 text-center text-[0.85rem] text-slate-200 min-w-[210px] tabulaire"
                     x-text="weekDays[0].toLocaleDateString('fr-FR', {day:'numeric', month:'long'}) + ' — ' + weekDays[6].toLocaleDateString('fr-FR', {day:'numeric', month:'long', year:'numeric'})"></div>
                <button type="button" @click="nextWeek()"
                        class="p-2 rounded-[9px] text-slate-400 hover:text-white hover:bg-white/5 transition-colors"
                        title="Semaine suivante">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>

            <button type="button" @click="showModal = true; formData.event_date = formatDateSQL(new Date())"
                    class="btn btn-accent shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Nouveau rendez-vous</span>
            </button>
        </div>
    </div>

    <!-- ─── GRILLE DE LA SEMAINE (LUNDI → DIMANCHE) ────────────────────── -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-7 gap-3">
        <template x-for="(day, index) in weekDays" :key="day">
            <div class="carte flex flex-col min-h-[300px] !p-0 overflow-hidden"
                 :class="day.getDay() === 0 ? 'opacity-50' : ''">

                <div class="px-3 py-2.5 border-b border-white/10 flex items-center justify-between gap-2">
                    <p class="text-[0.78rem] font-semibold text-slate-300" x-text="formatDayName(day)"></p>
                    <template x-if="day.getDay() === 0">
                        <span class="badge">Fermé</span>
                    </template>
                </div>

                <div class="flex-1 p-2 space-y-2 overflow-y-auto">

                    <template x-for="evt in getEventsForDay(day)" :key="evt.id">
                        <div class="rounded-[10px] border border-white/10 bg-white/[0.03] p-2.5
                                    cursor-pointer transition-colors duration-200 hover:border-[#38bdf8]/40">
                            <p class="text-[0.72rem] text-[#38bdf8] tabulaire"
                               x-text="evt.start_time.substring(0,5) + ' – ' + evt.end_time.substring(0,5)"></p>

                            <p class="text-[0.82rem] text-slate-200 leading-snug mt-1"
                               x-text="evt.title"></p>

                            <template x-if="evt.plate_number">
                                <span class="badge mt-2" x-text="evt.plate_number"></span>
                            </template>

                            <template x-if="evt.mechanic_name">
                                <p class="text-[0.72rem] text-slate-500 mt-2 flex items-center gap-1.5">
                                    <i data-lucide="tool" class="w-3 h-3"></i>
                                    <span x-text="evt.mechanic_name"></span>
                                </p>
                            </template>
                        </div>
                    </template>

                    <template x-if="getEventsForDay(day).length === 0">
                        <p class="text-center text-[0.75rem] text-slate-600 py-8">Aucun rendez-vous</p>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- ─── FENÊTRE : NOUVEAU RENDEZ-VOUS ──────────────────────────────── -->
    <template x-teleport="body">
        <div x-show="showModal" style="display:none;" x-transition.opacity
             class="fixed inset-0 z-[9999] bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">

            <div class="carte w-full max-w-xl my-8 apparition" @click.outside="showModal = false">

                <div class="carte-entete">
                    <h3 class="carte-titre">Nouveau rendez-vous</h3>
                    <button type="button" @click="showModal = false" class="text-slate-500 hover:text-slate-300">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form @submit.prevent="submitEvent" class="space-y-4">

                    <div>
                        <label class="etiquette" for="evt-title">Objet de l'intervention</label>
                        <input type="text" id="evt-title" x-model="formData.title" required
                               class="champ" placeholder="Ex. : remplacement pneu arrière">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="etiquette" for="evt-client">Nom du client</label>
                            <input type="text" id="evt-client" x-model="formData.client_name" class="champ">
                        </div>
                        <div>
                            <label class="etiquette" for="evt-plate">Immatriculation</label>
                            <input type="text" id="evt-plate" x-model="formData.plate_number"
                                   class="champ" placeholder="AB-123-CD">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="etiquette" for="evt-date">Date prévue</label>
                            <input type="date" id="evt-date" x-model="formData.event_date" required
                                   class="champ css-date-fixed">
                        </div>
                        <div>
                            <label class="etiquette" for="evt-pont">Poste de travail</label>
                            <select id="evt-pont" x-model="formData.mechanic_name" class="champ">
                                <option value="">Non affecté</option>
                                <option value="Pont 1 (Service Rapide)">Pont 1 — service rapide</option>
                                <option value="Pont 2 (Lourd)">Pont 2 — interventions lourdes</option>
                                <option value="Diagnostic Valise">Diagnostic valise</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="etiquette" for="evt-debut">Heure de début</label>
                            <input type="time" id="evt-debut" x-model="formData.start_time" required class="champ">
                        </div>
                        <div>
                            <label class="etiquette" for="evt-fin">Heure de fin</label>
                            <input type="time" id="evt-fin" x-model="formData.end_time" required class="champ">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showModal = false" class="btn btn-neutre flex-1">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-accent flex-1">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Inscrire au planning</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    
    <style>
        .css-date-fixed::-webkit-calendar-picker-indicator { filter: invert(1) brightness(1.5); cursor:pointer; opacity:0.5; }
        .css-date-fixed::-webkit-calendar-picker-indicator:hover { opacity:1; }
    </style>
</div>

<script>
    setTimeout(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
</script>
