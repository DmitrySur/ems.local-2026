<!--suppress VueUnrecognizedSlot -->
<script setup>
import {computed} from 'vue'
import {CalendarOutlined, ClockCircleOutlined, WarningFilled} from '@ant-design/icons-vue';

const props = defineProps({
    rows: {
        type: Array,
        required: true,
    },
    currentSort: {
        type: [String, null],
        required: true,
    },
})

const emit = defineEmits(['sort-change'])

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
        width: 120
    },
    {
        title: 'Место',
        key: 'object_infrastructure_template',
    },
    {
        title: 'Тип инцидента',
        key: 'incident_type_template',
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
    </a-table>
</template>
