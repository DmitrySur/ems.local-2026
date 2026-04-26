<!--suppress JSUnresolvedReference -->
<template>
    <Head title="Инциденты"/>

    <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
        <div>
            <h2 class="page-title mb-0">Инциденты</h2>
            <div class="text-secondary">
                Управление инцидентами
            </div>
        </div>

        <button class="btn btn-primary">
            Создать
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between">
                <div>Левый контент</div>
                <SearchInToolbar
                    :state="state"
                    @reload="reload"
                />
            </div>
        </div>
    </div>
    <div class="card mt-3 mb-3">
        <div class="card-body p-0">
            <IncidentTable
                :rows="table.data"
                :current-sort="state.sort"
                @sort-change="onSortChange"
            />
            <div class="card-footer d-flex align-items-center justify-content-center">
                <IncidentTablePagination
                    :meta="table.meta"
                    @page-change="reload"
                    @page-size-change="onPerPageChange"
                />
            </div>

        </div>
    </div>
</template>

<script setup>
import {Head, router} from '@inertiajs/vue3'
import {route} from 'ziggy-js';
import {reactive, watch} from "vue";
import IncidentTable from "@/Pages/Incidents/IncidentsTable/IncidentTable.vue";
import IncidentTablePagination from "@/Pages/Incidents/IncidentsTable/IncidentTablePagination.vue";
import SearchInToolbar from "@/Pages/Incidents/IncidentsTable/IncidentSearchInToolbar.vue";

const props = defineProps({
    table: {type: Object, required: true},
    filters: {type: Object, required: true},
    options: {type: Object, required: true},
})

// Единое состояние страницы

const state = reactive({
    sort: props.filters.sort ?? 'datetime_incident',
    per_page: props.filters.per_page ?? 15,
    page: props.filters.page ?? 1,
    search_by_number: props.filters.filter?.search_by_number ?? '',
})

/**
 * КРИТИЧНО:
 * синхронизация state с props от Inertia
 *
 * Зачем:
 * - пользователь поменял URL руками
 * - нажал back/forward
 * - пришёл новый response с сервера
 *
 * Без этого будет рассинхрон UI
 */
watch(
    () => props.filters,
    (filters) => {
        state.sort = filters.sort ?? ''
        state.per_page = filters.per_page ?? 15
        state.page = filters.page ?? 1
        state.search_by_number = filters.filter?.search_by_number ?? ''
    },
    {deep: true, immediate: true}
)

/**
 * Собираем query для backend (Spatie Query Builder)
 */
function buildQuery(page = 1) {
    const query = {
        page,
        per_page: state.per_page,
    }

    if (state.sort) {
        query.sort = state.sort
    }

    if (state.search_by_number) {
        query.filter = {
            search_by_number: state.search_by_number,
        }
    }

    return query
}

/**
 * Перезагрузка страницы через Inertia
 */
function reload(page = 1) {
    state.page = page

    router.get(route('app.incidents.index'), buildQuery(page), {
        preserveState: true,
        preserveScroll: true,
        replace: true,

        // оптимизация - не перерисовываем всё
        only: ['table', 'filters', 'errors'],
    })
}

/**
 * Сортировка из таблицы (стрелки колонок)
 */
function onSortChange(sort) {
    state.sort = sort
    state.page = 1
    reload(1)
}

/**
 * Смена количества записей
 */
function onPerPageChange(value) {
    state.per_page = value
    state.page = 1
    reload(1)
}

/**
 * Сброс всех фильтров
 */
function resetFilters() {
    state.sort = ''
    state.per_page = 15
    state.page = 1
    state.search_by_number = ''
    reload(1)
}
</script>
