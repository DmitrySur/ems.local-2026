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
            Контент страницы
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
                    @change="reload"
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
        sort: state.sort,
    }
    const filter = {}
    // быстрый поиск
    //if (state.search) filter.search = state.search
    // сегмент
    //if (state.type) filter.type = state.type
    // модалка
    //if (state.place) filter.place = state.place
    if (Object.keys(filter).length) {
        query.filter = filter
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
    state.sort = '-occurred_at'
    state.per_page = 15
    state.page = 1
    reload(1)
}
</script>
