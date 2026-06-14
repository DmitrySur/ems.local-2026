<script setup>
import {computed, toRef, ref, watch} from 'vue'
import {useObjectInfrastructureSelect} from '@/Support/ObjectInfrastructureSelect/useObjectInfrastructureSelect.js'
import ObjectInfrastructureSelectLabel from '../../../Support/VueComponents/ObjectInfrastructureSelectLabel.vue'

const props = defineProps({
  state: {
    type: Object,
    required: true,
  },
  selectOptions: {
    type: Object,
    default: () => ({}),
  },
})
const emit = defineEmits(['reload'])
const searchByNumberDraft = ref(props.state.search_by_number ?? '')
watch(
    () => props.state.search_by_number,
    (value) => {
      searchByNumberDraft.value = value ?? ''
    }
)

//Названия фильтров для чипсов
const filterLabels = {
  search_by_number: 'Номер инцидента',
  division_id: 'Подразделение',
  incident_type_id: 'Тип',
  object_infrastructure_id: 'Объект',
  preset_filter: 'Быстрый фильтр',
}
//Значение для сброса по умолчанию
const filterDefaults = {
  search_by_number: null,
  division_id: null,
  incident_type_id: null,
  object_infrastructure_id: null,
  preset_filter: 'unresolved_all',
}

const hiddenFilterValues = {
  preset_filter: ['unresolved_all'],
}

function isDefaultFilterValue(key, value) {
  if (isEmpty(value)) {
    return true
  }

  return hiddenFilterValues[key]?.some(
      hiddenValue => String(hiddenValue) === String(value)
  ) ?? false
}

function isEmpty(value) {
  return value === null
      || value === undefined
      || value === ''
      || (Array.isArray(value) && value.length === 0)
}

const selectedObjectInfrastructureRef = toRef(props.state, 'object_infrastructure_id')
const {
  selectedOption: selectedObjectInfrastructureOption,
} = useObjectInfrastructureSelect(selectedObjectInfrastructureRef)

const activeFilters = computed(() => {
  return Object.entries(filterLabels)
      .filter(([key]) => !isDefaultFilterValue(key, props.state[key]))
      .map(([key, label]) => ({
        key,
        label,
        value: getFilterValueLabel(key, props.state[key]),
        objectInfrastructureOption: key === 'object_infrastructure_id'
            ? selectedObjectInfrastructureOption.value
            : null,
      }))
})

function getFilterValueLabel(key, value) {
  const options = props.selectOptions[key]

  if (!options) {
    return value
  }

  return options.find(item => String(item.value) === String(value))?.label ?? value
}

function search() {
  props.state.search_by_number = searchByNumberDraft.value?.trim() || null
  props.state.page = 1
  emit('reload', 1)
}

function removeFilter(key) {
  props.state[key] = Object.prototype.hasOwnProperty.call(filterDefaults, key)
      ? filterDefaults[key]
      : ''

  if (key === 'search_by_number') {
    searchByNumberDraft.value = ''
  }

  props.state.page = 1
  emit('reload', 1)
}

function resetFilters() {
  Object.entries(filterDefaults).forEach(([key, value]) => {
    props.state[key] = value
  })

  searchByNumberDraft.value = ''

  props.state.page = 1
  emit('reload', 1)
}
</script>

<template>
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 w-100">
    <div class="d-flex flex-wrap align-items-center gap-1">
    <span v-if="activeFilters.length" class="text-secondary">
        Активные фильтры:
    </span>

      <a-tag
          v-for="filter in activeFilters"
          :key="filter.key"
          closable
          @close.prevent="removeFilter(filter.key)"
      >
        <span class="text-secondary">{{ filter.label }}:</span>

        <span
            v-if="filter.key === 'object_infrastructure_id'"
            class="active-object-infrastructure-filter"
        >
          <template v-if="filter.objectInfrastructureOption">
            <ObjectInfrastructureSelectLabel
                :label="filter.objectInfrastructureOption.label"
                :type="filter.objectInfrastructureOption.type"
                :short-name="filter.objectInfrastructureOption.short_name"
            />
          </template>

          <template v-else>
            <span class="text-secondary">загрузка...</span>
          </template>
        </span>
        <template v-else>
          {{ filter.value }}
        </template>
      </a-tag>
    </div>

    <div class="d-flex align-items-center incident-search">
      <a-input-search
          v-model:value="searchByNumberDraft"
          placeholder="Поиск по номеру"
          allow-clear
          @search="search"
      />

      <a-button
          class="incident-search__reset m-1"
          danger
          title="Сбросить все фильтры"
          @click="resetFilters"
          v-if="activeFilters.length"
      >
        Сбросить
      </a-button>
    </div>
  </div>
</template>

<style scoped>
.incident-search {
  flex: 0 0 auto;
}

.incident-search :deep(.ant-input-wrapper) {
  display: flex;
  align-items: center;
}

.incident-search :deep(.ant-input-affix-wrapper) {
  min-width: 0;
  height: 32px !important;
  border-radius: 8px 0 0 8px !important;
}

.incident-search :deep(.ant-input-clear-icon) {
  height: 100%;
}

.incident-search :deep(.ant-input-group-addon) {
  padding: 0 !important;
  width: 48px;
}

.incident-search :deep(.ant-input-search-button) {
  width: 48px !important;
  height: 32px !important;
  border-radius: 0 8px 8px 0 !important;
}

.active-object-infrastructure-filter {
  display: inline-flex;
  align-items: center;
  vertical-align: middle;
  line-height: 1;
}

.active-object-infrastructure-filter :deep(.oi-object-label) {
  display: inline-flex;
  align-items: center;
  width: auto;
  line-height: 1;
}

.active-object-infrastructure-filter :deep(.oi-object-label__icon) {
  vertical-align: middle;
  margin-right: 4px;
}

.active-object-infrastructure-filter :deep(.oi-object-label__prefix) {
  display: inline-flex;
  align-items: center;
  margin-right: 4px;
  line-height: 1;
}

.active-object-infrastructure-filter :deep(.oi-object-label__text) {
  display: inline;
  line-height: 1;
  white-space: nowrap;
}
</style>
