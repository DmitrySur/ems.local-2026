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

  <div class="">
    <!-- Сегменты для пресетов фильтров -->
    <IncidentPresetFiltersSegmented
        :value="state.preset_filter"
        :options="presetFilterOptions"
        :default-value="DEFAULT_PRESET_FILTER"
        :total="table.meta?.total ?? 0"
        @change="value => onFilterChange('preset_filter', value)"
    />
  </div>

  <div class="card">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between">
        <SearchInToolbar
            :state="state"
            :select-options="{
                    division_id: options.divisions,
                    incident_type_id: options.incident_types,
                    preset_filter: presetFilterOptions
                    }"
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
          :division-options="options.divisions"
          :selected-division="state.division_id"
          :incident-types-options="options.incident_types"
          :selected-incident-type="state.incident_type_id"
          :selected-object-infrastructure="state.object_infrastructure_id"
          @filter-change="onFilterChange"
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
import IncidentPresetFiltersSegmented from "@/Pages/Incidents/IncidentsTable/IncidentPresetFiltersSegmented.vue";

const props = defineProps({
  table: {type: Object, required: true},
  filters: {type: Object, required: true},
  options: {type: Object, required: true},
})

const DEFAULT_PRESET_FILTER = 'unresolved_all'

//Фильтры для пресетов
const presetFilterOptions = [
  {label: 'Все не устраненные', value: DEFAULT_PRESET_FILTER},
  {label: 'Все не устраненные за сутки', value: 'unresolved_day'},
  {label: 'ННР все', value: 'nnr_all'},
  {label: 'ННР не устраненные', value: 'nnr_unresolved'},
  {label: 'Все за сутки', value: 'day_all'},
  {label: 'Все', value: 'all'},
]

// Единое состояние страницы

const state = reactive({
  sort: props.filters.sort ?? 'datetime_incident',
  per_page: props.filters.per_page ?? 15,
  page: props.filters.page ?? 1,
  search_by_number: props.filters.filter?.search_by_number ?? null,
  division_id: props.filters.filter?.division_id ?? null,
  incident_type_id: props.filters.filter?.incident_type_id ?? null,
  object_infrastructure_id: props.filters.filter?.object_infrastructure_id ?? null,
  preset_filter: props.filters.filter?.preset_filter ?? DEFAULT_PRESET_FILTER,
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
      state.search_by_number = filters.filter?.search_by_number ?? null
      state.division_id = filters.filter?.division_id ?? null
      state.incident_type_id = filters.filter?.incident_type_id ?? null
      state.object_infrastructure_id = filters.filter?.object_infrastructure_id ?? null
      state.preset_filter = filters.filter?.preset_filter ?? DEFAULT_PRESET_FILTER
    },
    {deep: true, immediate: true}
)

function onFilterChange(key, value) {
  state[key] = value ?? null
  state.page = 1
  reload(1)
}

/**
 * Собираем query для backend (Spatie Query Builder)
 */
function buildQuery(page = state.page) {
  const query = {
    page,
    per_page: state.per_page,
  }

  if (state.sort) {
    query.sort = state.sort
  }

  const filters = {}

  if (state.search_by_number) {
    filters.search_by_number = state.search_by_number
  }

  if (state.division_id) {
    filters.division_id = state.division_id
  }

  if (state.incident_type_id) {
    filters.incident_type_id = state.incident_type_id
  }

  if (state.object_infrastructure_id) {
    filters.object_infrastructure_id = state.object_infrastructure_id
  }

  if (state.preset_filter) {
    filters.preset_filter = state.preset_filter
  }

  if (Object.keys(filters).length) {
    query.filter = filters
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
  state.search_by_number = null
  state.division_id = null
  state.incident_type_id = null
  state.object_infrastructure_id = null
  state.preset_filter = DEFAULT_PRESET_FILTER
  reload(1)
}
</script>
