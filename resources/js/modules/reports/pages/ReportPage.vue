<template>
    <section class="report-page">
        <h2>Reportes de Usuarios</h2>

        <form @submit.prevent="fetchReport" class="filters">
            <label>
                Fecha desde
                <input type="date" v-model="filters.date_from" />
            </label>

            <label>
                Fecha hasta
                <input type="date" v-model="filters.date_to" />
            </label>

            <label>
                Rol
                <input type="text" v-model="filters.role" placeholder="Ej: Recepcionista" />
            </label>

            <div class="actions">
                <button type="submit">Filtrar</button>
                <button type="button" @click="exportCsv">Exportar CSV</button>
            </div>
        </form>

        <table v-if="users.length">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Tenant</th>
                    <th>Creado</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="u in users" :key="u.id">
                    <td>{{ u.id }}</td>
                    <td>{{ u.name }}</td>
                    <td>{{ u.email }}</td>
                    <td>{{ u.roles.map(r => r.name).join(', ') }}</td>
                    <td>{{ u.tenant?.name }}</td>
                    <td>{{ u.created_at }}</td>
                </tr>
            </tbody>
        </table>

        <p v-else>No hay resultados.</p>
    </section>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/plugins/axios';

const filters = ref({ date_from: '', date_to: '', role: '' });
const users = ref([]);

async function fetchReport() {
    const params = {};
    if (filters.value.date_from) params.date_from = filters.value.date_from;
    if (filters.value.date_to) params.date_to = filters.value.date_to;
    if (filters.value.role) params.role = filters.value.role;

    try {
        const res = await api.get('/reports', { params });
        users.value = res.data.data || [];
    } catch (e) {
        console.error(e);
        users.value = [];
    }
}

function exportCsv() {
    const params = new URLSearchParams();
    if (filters.value.date_from) params.append('date_from', filters.value.date_from);
    if (filters.value.date_to) params.append('date_to', filters.value.date_to);
    if (filters.value.role) params.append('role', filters.value.role);
    params.append('export', 'csv');

    // Trigger file download (browser will include auth headers from axios plugin when using XHR; for simplicity use location)
    const base = import.meta.env.VITE_API_URL ?? '/api/v1';
    window.location = `${base}/reports?${params.toString()}`;
}

// fetch initial
fetchReport();
</script>

<style scoped>
.report-page { max-width: 960px; }
.filters { display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1rem }
.filters label { display:flex; flex-direction:column; font-size:0.9rem }
.actions { display:flex; gap:0.5rem; align-items:flex-end }
table { width:100%; border-collapse:collapse }
th, td { padding:0.5rem; border:1px solid #e5e7eb }
</style>
