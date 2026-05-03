<script setup>
import {computed} from 'vue'

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

//Названия фильтров для чипсов
const filterLabels = {
    search_by_number: 'Номер инцидента',
    division_id: 'Подразделение',
    incident_type_id: 'Тип',
}
//Значение для сброса по умолчанию
const filterDefaults = {
    search_by_number: null,
    division_id: null,
}

function isEmpty(value) {
    return value === null
        || value === undefined
        || value === ''
        || (Array.isArray(value) && value.length === 0)
}

const activeFilters = computed(() => {
    return Object.entries(filterLabels)
        .filter(([key]) => !isEmpty(props.state[key]))
        .map(([key, label]) => ({
            key,
            label,
            value: getFilterValueLabel(key, props.state[key]),
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
    props.state.page = 1
    emit('reload', 1)
}

function removeFilter(key) {
    props.state[key] = filterDefaults[key] ?? ''
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
                {{ filter.value }}
            </a-tag>
        </div>

        <div class="d-flex align-items-center incident-search">
            <a-input-search
                v-model:value="props.state.search_by_number"
                placeholder="Поиск по номеру"
                allow-clear
                @search="search"
            />
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
</style>
