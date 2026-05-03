<!--suppress VueUnrecognizedSlot -->
<script setup>
import {computed, toRef} from 'vue'
import {CalendarOutlined, ClockCircleOutlined, WarningFilled} from '@ant-design/icons-vue';
import {useObjectInfrastructureSelect} from '@/Support/ObjectInfrastructureSelect/useObjectInfrastructureSelect.js'


const props = defineProps({
    rows: {
        type: Array,
        required: true,
    },
    currentSort: {
        type: [String, null],
        required: true,
    },
    divisionOptions: {
        type: Array,
        required: true,
    },
    selectedDivision: {
        type: [Number, String, null],
        default: null,
    },
    incidentTypesOptions: {
        type: Array,
        required: true,
    },
    selectedIncidentType: {
        type: [Number, String, null],
        default: null,
    },
    selectedObjectInfrastructure: {
        type: [Number, String, null],
        default: null,
    },
})

const emit = defineEmits(['sort-change', 'filter-change'])

const selectedObjectInfrastructureRef = toRef(props, 'selectedObjectInfrastructure')

const {
    options: objectInfrastructureOptions,
    loading: objectInfrastructureLoading,
    onSearch: onObjectInfrastructureSearch,
    getIconComponent: getObjectInfrastructureIconComponent,
    iconStyle: objectInfrastructureIconStyle,
    getPrefix: getObjectInfrastructurePrefix,
} = useObjectInfrastructureSelect(selectedObjectInfrastructureRef)

// Возвращаем sortOrder для конкретной колонки.
// Это нужно для controlled-режима сортировки Ant Design.
function getSortOrder(columnKey) {
    if (props.currentSort === columnKey) {
        return 'ascend'
    }

    if (props.currentSort === `-${columnKey}`) {
        return 'descend'
    }

    return null
}

// Обрабатываем клик по стрелкам сортировки в заголовке таблицы.
function handleTableChange(pagination, filters, sorter) {
    const currentSorter = Array.isArray(sorter) ? sorter[0] : sorter
    // Если пользователь снял сортировку (третье нажатие),
    // сбрасываем сортировку.
    if (!currentSorter || !currentSorter.order) {
        emit('sort-change', null); // null означает отсутствие сортировки
        return;
    }
    // Берём поле сортировки из описания колонки
    const sortField =
        currentSorter.column?.sortField ||
        currentSorter.field ||
        currentSorter.columnKey

    if (!sortField) {
        emit('sort-change', '-datetime_incident')
        return
    }

    if (currentSorter.order === 'ascend') {
        emit('sort-change', sortField)
        return
    }

    if (currentSorter.order === 'descend') {
        emit('sort-change', `-${sortField}`)
    }
}

// Колонки вынесены в computed,
// потому что они зависят от текущей сортировки.
const columns = computed(() => [
    {
        title: 'Дата и время',
        key: 'date_and_time_template',
        sorter: true,
        sortField: 'datetime_incident',
        sortOrder: getSortOrder('datetime_incident'),
        width: 220,
    },
    {
        title: 'ЭМЧ',
        dataIndex: 'division_short_name',
        key: 'division_short_name',
        width: 120,
        customFilterDropdown: true,
        filteredValue: props.selectedDivision ? [props.selectedDivision] : null,
    },
    {
        title: 'Место',
        key: 'object_infrastructure_template',
        customFilterDropdown: true,
        filteredValue: props.selectedObjectInfrastructure
            ? [props.selectedObjectInfrastructure]
            : null,
    },
    {
        title: 'Тип инцидента',
        key: 'incident_type_template',
        customFilterDropdown: true,
        filteredValue: props.selectedIncidentType ? [props.selectedIncidentType] : null,
    },
    {
        title: 'Описание',
        key: 'description_template',
    },
    {
        title: 'Неисправность',
        key: 'fault_element_reason_template',
    },
])


</script>

<template>
    <a-table
        :columns="columns"
        :data-source="rows"
        :pagination="false"
        :row-key="record => record.id"
        @change="handleTableChange">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'date_and_time_template'">
                <a-popover>
                    <!-- Контент внутри всплывающего окна -->
                    <template #content>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless m-0 text-nowrap">
                                <tbody>
                                <tr v-if="record.reported_by">
                                    <td class="px-1 py-0 text-muted">Сообщил:</td>
                                    <td class="px-1 py-0 fw-medium">{{ record.reported_by }}</td>
                                </tr>
                                <tr v-if="record.created_at">
                                    <td class="px-1 py-0 text-muted">Дата создания:</td>
                                    <td class="px-1 py-0 fw-medium">{{ record.created_at }}</td>
                                </tr>
                                <tr v-if="record.creator_name">
                                    <td class="px-1 py-0 text-muted">Создал:</td>
                                    <td class="px-1 py-0 fw-medium">{{ record.creator_name }}</td>
                                </tr>
                                <tr v-if="record.updated_at && record.updated_at !== record.created_at">
                                    <td class="px-1 py-0 text-muted">Дата изменения:</td>
                                    <td class="px-1 py-0 fw-medium">{{ record.updated_at }}</td>
                                </tr>
                                <tr v-if="record.updater_name && record.updated_at !== record.created_at">
                                    <td class="px-1 py-0 text-muted">Изменил:</td>
                                    <td class="px-1 py-0 fw-medium">{{ record.updater_name }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <div class="d-inline-flex flex-column gap-1">
                        <div
                            :class="{ 'bg-yellow-lt text-dark': record.status_resolution === 'in_repair' }"
                            class="d-inline-flex flex-wrap gap-1 lh-sm  border p-1 rounded-2"
                        >
                    <span class="d-inline-flex align-items-center text-nowrap gap-1">
                        <CalendarOutlined/> {{ record.incident_date }}
                    </span>
                            <span class="d-inline-flex align-items-center text-nowrap gap-1">
                        <ClockCircleOutlined/>{{ record.incident_time }}
                    </span>
                        </div>
                        <div class="d-inline-flex flex-wrap gap-1 lh-sm">
                            <small class="d-inline-flex align-items-center text-nowrap gap-1 text-secondary">
                                № {{ record.id }}
                            </small>
                            <small
                                v-if="record.incident_classification === 'ННР'"
                                class="d-inline-flex align-items-center text-nowrap gap-1 text-danger">
                                <WarningFilled/>
                                ННР<span v-if="record.number_nnr"> {{ record.number_nnr }}</span>
                            </small>
                        </div>
                    </div>
                </a-popover>
            </template>
            <template v-if="column.key === 'object_infrastructure_template'">
                <div class="d-flex flex-column gap-1 lh-sm">
                    <div class="text-wrap">
                        {{ record.object_infrastructure }}
                    </div>
                    <div v-if="record.location || record.detail_location" class="text-secondary small">
                        № {{ [record.location, record.detail_location].filter(Boolean).join(' | ') }}
                    </div>
                </div>
            </template>
            <template v-if="column.key === 'incident_type_template'">
                <div class="d-flex flex-column gap-1 lh-sm">
                    <div class="text-wrap">
                        {{ record.incident_type }}
                    </div>
                    <div class="text-secondary small">
                        {{ record.is_has_directory_objects ? '' : (record.itu_specie_title ?? '') }}
                    </div>
                </div>
            </template>
            <template v-if="column.key === 'description_template'">
                <div class="d-flex flex-column gap-1 lh-sm">
                    <div class="text-wrap">
                        {{
                            record.is_has_directory_objects
                                ? [record.directory_specie_title, record.directory_title].filter(Boolean).join('-')
                                : (record.itu_characteristic_title ?? '')
                        }}
                    </div>
                    <div class="text-secondary small">
                        {{
                            record.is_has_directory_objects
                                ? [record.itu_characteristic_title, record.detail_object_incident].filter(Boolean).join(' | ')
                                : (record.detail_object_incident ?? '')
                        }}
                    </div>
                </div>
            </template>
            <template v-if="column.key === 'fault_element_reason_template'">
                <a-popover>
                    <template #content>
                        {{ record.detail_incident ?? '-' }}
                    </template>
                    <ul
                        v-if="record.itu_fault_title || record.itu_element_title || record.itu_reason_breakdown_title"
                        class="small text-wrap">
                        <li v-if="record.itu_fault_title ">
                            {{ record.itu_fault_title }}
                        </li>
                        <li v-if="record.itu_element_title ">
                            {{ record.itu_element_title }}
                        </li>
                        <li v-if="record.itu_reason_breakdown_title ">
                            {{ record.itu_reason_breakdown_title }}
                        </li>
                    </ul>
                </a-popover>
                <div
                    class="text-wrap"
                    v-if="!record.itu_fault_title && !record.itu_element_title && !record.itu_reason_breakdown_title &&  record.detail_incident">
                    {{ record.detail_incident }}
                </div>
            </template>
        </template>
        <!-- ФИЛЬТРЫ В КОЛОНКАХ -->

        <template #customFilterDropdown="{ column, confirm }">
            <!-- ФИЛЬТР В КОЛОНКЕ "ЭМЧ" -->
            <div
                v-if="column.key === 'division_short_name'"
                style="padding: 8px; width: 240px"
            >
                <div class="text-secondary small lh-base mb-1">
                    Фильтр по ЭМЧ:
                </div>
                <a-select
                    :value="selectedDivision"
                    allow-clear
                    show-search
                    placeholder="Выберите ЭМЧ"
                    style="width: 100%"
                    :options="divisionOptions"
                    option-filter-prop="label"
                    @change="value => {
                emit('filter-change', 'division_id', value)
                confirm()
            }"
                />
            </div>
            <!-- ФИЛЬТР В КОЛОНКЕ "ТИП ИНЦИДЕНТА" -->
            <div
                v-if="column.key === 'incident_type_template'"
                style="padding: 8px; width: 240px"
            >
                <div class="text-secondary small lh-base mb-1">
                    Фильтр по типу инцидента:
                </div>
                <a-select
                    :value="selectedIncidentType"
                    allow-clear
                    show-search
                    placeholder="Выберите тип"
                    style="width: 100%"
                    :options="incidentTypesOptions"
                    option-filter-prop="label"
                    @change="value => {
                emit('filter-change', 'incident_type_id', value)
                confirm()
            }"
                />
            </div>
            <!-- ФИЛЬТР В КОЛОНКЕ "МЕСТО" -->
            <div
                v-if="column.key === 'object_infrastructure_template'"
                style="padding: 8px; width: 360px"
            >
                <div class="text-secondary small lh-base mb-1">
                    Фильтр по объекту:
                </div>

                <a-select
                    :value="selectedObjectInfrastructure"
                    allow-clear
                    show-search
                    :filter-option="false"
                    :loading="objectInfrastructureLoading"
                    :options="objectInfrastructureOptions"
                    placeholder="Начните вводить наименование"
                    style="width: 100%"
                    @search="onObjectInfrastructureSearch"
                    @change="value => {
            emit('filter-change', 'object_infrastructure_id', value)
            confirm()
        }"
                >
                    <template #option="{ label, type, short_name }">
    <span class="oi-select__row">
        <span class="oi-select__head">
            <component
                :is="getObjectInfrastructureIconComponent(type)"
                :size="18"
                :stroke-width="2"
                :style="objectInfrastructureIconStyle(type, short_name)"
                class="oi-select__icon"
            />

            <span class="oi-select__prefix text-secondary">
                {{ getObjectInfrastructurePrefix(type) }}
            </span>

            <span class="oi-select__label">
                {{ label }}
            </span>
        </span>
    </span>
                    </template>
                    <template #labelRender="{ label, option }">
    <span class="oi-select__row">
        <span class="oi-select__head">
            <component
                :is="getObjectInfrastructureIconComponent(option?.type)"
                :size="18"
                :stroke-width="2"
                :style="objectInfrastructureIconStyle(option?.type, option?.short_name)"
                class="oi-select__icon"
            />

            <span class="oi-select__prefix text-secondary">
                {{ getObjectInfrastructurePrefix(option?.type) }}
            </span>

            <span class="oi-select__label">
                {{ label }}
            </span>
        </span>
    </span>
                    </template>
                </a-select>
            </div>
        </template>
    </a-table>
</template>
<style scoped>
.oi-select__row {
    display: block;
    width: 100%;
    white-space: normal;
    line-height: 1.25;
}

.oi-select__head {
    display: inline;
    white-space: normal;
}

.oi-select__icon {
    display: inline-block;
    vertical-align: -4px;
    margin-right: 6px;
    flex: 0 0 auto;
}

.oi-select__prefix {
    display: inline;
    margin-right: 6px;
    white-space: nowrap;
}

.oi-select__label {
    display: inline;
    white-space: normal;
    word-break: break-word;
}
</style>
