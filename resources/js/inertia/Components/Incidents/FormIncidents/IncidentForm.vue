<template>
  <a-form
      ref="formRef"
      layout="vertical"
      :model="modelValue.incident"
      :rules="rules"
  >
    <a-tabs v-model:activeKey="activeTab">
      <!-- 1. ОСНОВНЫЕ ДАННЫЕ -->
      <a-tab-pane
          key="main"
          tab="Основные данные"
          force-render
      >
        <div class="row">
          <!-- Дата и время инцидента -->
          <div class="col-md-6">
            <a-form-item
                label="Дата и время инцидента"
                name="datetime_incident"
            >
              <a-date-picker
                  :value="modelValue.incident.datetime_incident"
                  show-time
                  format="DD.MM.YYYY HH:mm"
                  placeholder="Выберите дату и время"
                  style="width: 100%"
                  @change="value => updateIncidentField('datetime_incident', value)"
              />
            </a-form-item>
          </div>

          <!-- Местоположение -->
          <div class="col-md-6">
            <a-form-item
                label="Местоположение"
                name="location"
            >
              <a-select
                  :value="modelValue.incident.location"
                  :options="options.locations ?? []"
                  placeholder="Выберите местоположение"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  @change="value => updateIncidentField('location', value)"
              />
            </a-form-item>
          </div>

          <!-- Объект инфраструктуры -->
          <div class="col-md-12">
            <a-form-item
                label="Объект инфраструктуры"
                name="object_infrastructure_id"
            >
              <a-select
                  class="oi-object-form-select"
                  popup-class-name="oi-object-form-select-popup"
                  :value="modelValue.incident.object_infrastructure_id"
                  allow-clear
                  show-search
                  :filter-option="false"
                  :loading="objectInfrastructureLoading"
                  placeholder="Начните вводить наименование объекта"
                  style="width: 100%"
                  @search="onObjectInfrastructureSearch"
                  @change="handleObjectInfrastructureChange"
              >
                <a-select-option
                    v-for="option in objectInfrastructureOptions"
                    :key="option.value"
                    :value="option.value"
                >
                  <ObjectInfrastructureSelectLabel
                      :label="option.label"
                      :type="option.type"
                      :short-name="option.short_name"
                  />
                </a-select-option>
              </a-select>
            </a-form-item>
          </div>

          <!-- Подразделение -->
          <div class="col-md-6">
            <a-form-item
                label="Подразделение"
                name="division_id"
            >
              <a-select
                  :value="modelValue.incident.division_id"
                  :options="filteredDivisionOptions"
                  placeholder="Выберите подразделение"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  :disabled="!modelValue.incident.object_infrastructure_id"
                  @change="value => updateIncidentField('division_id', value)"
              />
            </a-form-item>
          </div>

          <!-- Сообщил -->
          <div class="col-md-6">
            <a-form-item
                label="Сообщил"
                name="reported_by"
            >
              <a-input
                  :value="modelValue.incident.reported_by"
                  placeholder="Кто сообщил об инциденте"
                  :maxlength="600"
                  @input="event => updateIncidentField('reported_by', event.target.value)"
              />
            </a-form-item>
          </div>

          <!-- Уточнение местоположения -->
          <div class="col-md-12">
            <a-form-item
                label="Уточнение местоположения"
                name="detail_location"
            >
              <a-textarea
                  :value="modelValue.incident.detail_location"
                  :rows="2"
                  placeholder="Пикет/№ помещения"
                  :maxlength="800"
                  show-count
                  @input="event => updateIncidentField('detail_location', event.target.value)"
              />
            </a-form-item>
          </div>

          <!-- Тип инцидента -->
          <div class="col-md-6">
            <a-form-item
                label="Тип инцидента"
                name="incident_type_id"
            >
              <a-select
                  :value="modelValue.incident.incident_type_id"
                  :options="options.incident_types ?? []"
                  placeholder="Выберите тип инцидента"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  @change="handleIncidentTypeChange"
              />
            </a-form-item>
          </div>

          <!-- Классификация -->
          <div class="col-md-3">
            <a-form-item
                label="Классификация"
                name="incident_classification"
            >
              <a-select
                  :value="modelValue.incident.incident_classification"
                  :options="options.incident_classifications ?? []"
                  placeholder="Выберите"
                  @change="handleIncidentClassificationChange"
              />
            </a-form-item>
          </div>

          <!-- Номер ННР -->
          <div class="col-md-3">
            <a-form-item
                label="Номер ННР"
                name="number_nnr"
            >
              <a-input
                  :value="modelValue.incident.number_nnr"
                  placeholder="Номер"
                  :maxlength="255"
                  :disabled="modelValue.incident.incident_classification !== 'ННР'"
                  @input="event => updateIncidentField('number_nnr', event.target.value)"
              />
            </a-form-item>
          </div>

          <!-- Статус устранения -->
          <div class="col-md-6">
            <a-form-item
                label="Статус устранения"
                name="status_resolution"
            >
              <a-select
                  :value="modelValue.incident.status_resolution"
                  :options="options.incident_statuses ?? []"
                  placeholder="Выберите статус"
                  @change="handleStatusResolutionChange"
              />
            </a-form-item>
          </div>

          <!-- Дата вывода из ремонта -->
          <div
              v-if="isRepairDateVisible"
              class="col-md-6"
          >
            <a-form-item
                label="Дата вывода из ремонта"
                name="repair_date"
            >
              <a-date-picker
                  :value="modelValue.incident.repair_date"
                  show-time
                  format="DD.MM.YYYY HH:mm"
                  placeholder="Выберите дату и время"
                  style="width: 100%"
                  @change="value => updateIncidentField('repair_date', value)"
              />
            </a-form-item>
          </div>

          <!-- Уточнение по объекту инцидента -->
          <div class="col-md-6">
            <a-form-item
                label="Уточнение по объекту инцидента"
                name="detail_object_incident"
            >
              <a-input
                  :value="modelValue.incident.detail_object_incident"
                  placeholder="Уточнение"
                  :maxlength="450"
                  @input="event => updateIncidentField('detail_object_incident', event.target.value)"
              />
            </a-form-item>
          </div>

          <!-- Уточнение по инциденту -->
          <div class="col-md-6">
            <a-form-item
                label="Уточнение по инциденту"
                name="detail_incident"
            >
              <a-input
                  :value="modelValue.incident.detail_incident"
                  placeholder="Уточнение"
                  :maxlength="450"
                  @input="event => updateIncidentField('detail_incident', event.target.value)"
              />
            </a-form-item>
          </div>

          <!-- Принятые меры -->
          <div class="col-md-12">
            <a-form-item
                label="Принятые меры"
                name="appropriate_measures"
            >
              <a-textarea
                  :value="modelValue.incident.appropriate_measures"
                  :rows="3"
                  :maxlength="1500"
                  show-count
                  placeholder="Опишите принятые меры"
                  @input="event => updateIncidentField('appropriate_measures', event.target.value)"
              />
            </a-form-item>
          </div>

          <!-- Вид инцидента ИТУ -->
          <div
              v-if="isItuSpecieVisible"
              class="col-md-6"
          >
            <a-form-item
                label="Вид инцидента (ИТУ)"
                name="itu_specie_id"
            >
              <a-select
                  :value="modelValue.incident.itu_specie_id"
                  :options="filteredItuSpecieOptions"
                  placeholder="Выберите вид ИТУ"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  @change="handleItuSpecieChange"
              />
            </a-form-item>
          </div>

          <!-- Характеристика ИТУ -->
          <div
              v-if="isItuCharacteristicVisible"
              class="col-md-6"
          >
            <a-form-item
                label="Характеристики ИТУ"
                name="itu_characteristic_id"
            >
              <a-select
                  :value="modelValue.incident.itu_characteristic_id"
                  :options="filteredItuCharacteristicOptions"
                  placeholder="Выберите характеристику"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  @change="value => updateIncidentField('itu_characteristic_id', value)"
              />
            </a-form-item>
          </div>

          <!-- Объект ИТУ -->
          <div
              v-if="isItuDirectoryObjectVisible"
              class="col-md-6"
          >
            <a-form-item
                label="Объект ИТУ"
                name="itu_directory_object_id"
            >
              <a-select
                  :value="modelValue.incident.itu_directory_object_id"
                  :options="filteredItuDirectoryObjectOptions"
                  placeholder="Выберите объект ИТУ"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  @change="value => updateIncidentField('itu_directory_object_id', value)"
              />
            </a-form-item>
          </div>

          <!-- Неисправность -->
          <div
              v-if="isItuFaultVisible"
              class="col-md-4"
          >
            <a-form-item
                label="Неисправность"
                name="itu_fault_id"
            >
              <a-select
                  :value="modelValue.incident.itu_fault_id"
                  :options="filteredItuFaultOptions"
                  placeholder="Выберите неисправность"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  @change="value => updateIncidentField('itu_fault_id', value)"
              />
            </a-form-item>
          </div>


          <!-- Неисправный элемент -->
          <div
              v-if="isItuElementVisible"
              class="col-md-4"
          >
            <a-form-item
                label="Неисправный элемент"
                name="itu_element_id"
            >

              <a-select
                  :value="modelValue.incident.itu_element_id"
                  :options="filteredItuElementOptions"
                  placeholder="Выберите элемент"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  @change="value => updateIncidentField('itu_element_id', value)"
              />
            </a-form-item>
          </div>


          <!-- Причина неисправности -->
          <div
              v-if="isItuReasonBreakdownVisible"
              class="col-md-4"
          >
            <a-form-item
                label="Причина"
                name="itu_reason_breakdown_id"
            >
              <a-select
                  :value="modelValue.incident.itu_reason_breakdown_id"
                  :options="filteredItuReasonBreakdownOptions"
                  placeholder="Выберите причину"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  @change="value => updateIncidentField('itu_reason_breakdown_id', value)"
              />
            </a-form-item>
          </div>
        </div>
      </a-tab-pane>

      <!-- 2. НАПРАВЛЕНИЕ РАБОТНИКОВ -->
      <a-tab-pane
          key="referral_workers"
          tab="Направление работников"
          force-render
      >
        <a-empty description="Таблицу направленных работников добавим отдельным этапом"/>
      </a-tab-pane>

      <!-- 3. ИНФОРМИРОВАНИЕ РАБОТНИКОВ -->
      <a-tab-pane
          key="information_workers"
          tab="Информирование работников"
          force-render
      >
        <a-empty description="Таблицу информированных работников добавим отдельным этапом"/>
      </a-tab-pane>

      <!-- 4. ХРОНИКА -->
      <a-tab-pane
          key="chronicles"
          tab="Хроника"
          force-render
      >
        <a-empty description="Таблицу хроники добавим отдельным этапом"/>
      </a-tab-pane>

      <!-- 5. ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ -->
      <a-tab-pane
          key="additional"
          tab="Дополнительная информация"
          force-render
      >
        <div class="row">
          <!-- Диспетчерский участок -->
          <div class="col-md-6">
            <a-form-item
                label="Диспетчерский участок"
                name="dispatch_area_id"
            >
              <a-select
                  :value="modelValue.incident.dispatch_area_id"
                  :options="filteredDispatchAreaOptions"
                  placeholder="Выберите диспетчерский участок"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  :disabled="!permissions.can_change_dispatch_area"
                  @change="value => updateIncidentField('dispatch_area_id', value)"
              />
            </a-form-item>

            <div
                v-if="!permissions.can_change_dispatch_area"
                class="text-secondary small mt-n2 mb-3"
            >
              Диспетчерский участок определяется автоматически и недоступен для ручного изменения.
            </div>
          </div>

          <!-- Отражать в сводке -->
          <div class="col-md-6">
            <a-form-item
                label="Отражать инцидент в сводке?"
                name="is_in_report"
            >
              <a-switch
                  :checked="modelValue.incident.is_in_report"
                  checked-children="Да"
                  un-checked-children="Нет"
                  @change="value => updateIncidentField('is_in_report', value)"
              />
            </a-form-item>
          </div>

          <!-- Примечание -->
          <div class="col-md-12">
            <a-form-item
                label="Примечание"
                name="note"
            >
              <a-textarea
                  :value="modelValue.incident.note"
                  :rows="3"
                  :maxlength="1500"
                  show-count
                  placeholder="Дополнительное примечание"
                  @input="event => updateIncidentField('note', event.target.value)"
              />
            </a-form-item>
          </div>
        </div>
      </a-tab-pane>
    </a-tabs>
  </a-form>
</template>

<script setup>
import {computed, ref} from 'vue'
import dayjs from 'dayjs'

import {useObjectInfrastructureSelect} from '@/Support/ObjectInfrastructureSelect/useObjectInfrastructureSelect.js'
import ObjectInfrastructureSelectLabel from '@/Support/VueComponents/ObjectInfrastructureSelectLabel.vue'
import {useIncidentFormValidation} from './composables/useIncidentFormValidation'

const props = defineProps({
  /**
   * Вся форма:
   * {
   *   incident: {},
   *   referral_workers: [],
   *   information_workers: [],
   *   chronicles: []
   * }
   */
  modelValue: {
    type: Object,
    required: true,
  },

  /**
   * Справочники из create-form API.
   */
  options: {
    type: Object,
    default: () => ({}),
  },

  /**
   * Права пользователя.
   */
  permissions: {
    type: Object,
    default: () => ({}),
  },

  /**
   * Ошибки backend-валидации.
   * Подключим позже.
   */
  errors: {
    type: Object,
    default: () => ({}),
  },

  /**
   * Режим формы.
   * Сейчас create, позже добавим edit.
   */
  mode: {
    type: String,
    default: 'create',
  },
})

const emit = defineEmits([
  'update:modelValue',
  'submit',
  'validation-failed',
])

const formRef = ref(null)
const activeTab = ref('main')

/**
 * ref/computed для текущего object_infrastructure_id.
 * Его передаем в уже готовый useObjectInfrastructureSelect.js.
 */
const selectedObjectInfrastructureId = computed(() => {
  return props.modelValue.incident.object_infrastructure_id
})

const {
  options: objectInfrastructureOptions,
  loading: objectInfrastructureLoading,
  onSearch: onObjectInfrastructureSearch,
} = useObjectInfrastructureSelect(selectedObjectInfrastructureId)

/**
 * Выбранная option объекта инфраструктуры.
 * Нужна, чтобы понять group_infrastructure_object_id
 * и отфильтровать подразделения/участки.
 */
const selectedObjectInfrastructureOption = computed(() => {
  return objectInfrastructureOptions.value.find((option) => {
    return String(option.value) === String(props.modelValue.incident.object_infrastructure_id)
  })
})

/**
 * ID группы выбранного объекта инфраструктуры.
 */
const selectedGroupInfrastructureObjectId = computed(() => {
  return getGroupInfrastructureObjectIdFromOption(selectedObjectInfrastructureOption.value)
})

/**
 * Подразделения, доступные для выбранного объекта.
 *
 * Логика:
 * - если объект еще не выбран, показываем все подразделения;
 * - если подразделение без привязки к группе, оно доступно;
 * - если подразделение привязано к группе, группа должна совпадать.
 */
const filteredDivisionOptions = computed(() => {
  const divisions = props.options.divisions ?? []
  const groupId = selectedGroupInfrastructureObjectId.value

  if (!groupId) {
    return divisions
  }

  return divisions.filter((division) => {
    const meta = division.meta ?? {}

    if (!meta.has_group_object) {
      return true
    }

    return String(meta.group_infrastructure_object_id ?? '') === String(groupId)
  })
})

/**
 * Диспетчерские участки, доступные для выбранного объекта.
 */
const filteredDispatchAreaOptions = computed(() => {
  const dispatchAreas = props.options.dispatch_areas ?? []
  const groupId = selectedGroupInfrastructureObjectId.value

  if (!groupId) {
    return dispatchAreas
  }

  return dispatchAreas.filter((dispatchArea) => {
    return isDispatchAreaAvailableForGroup(dispatchArea, groupId)
  })
})

/**
 * Значение статуса InWorking.
 *
 * Пока определяем по value/label.
 * Позже лучше отдать точное значение с backend create-form API.
 */
const inWorkingStatusValue = computed(() => {
  const statuses = props.options.incident_statuses ?? []

  const found = statuses.find((status) => {
    const value = String(status.value ?? '').toLowerCase()
    const label = String(status.label ?? '').toLowerCase()

    return value === 'in_working'
        || value === 'inworking'
        || label.includes('устран')
        || label.includes('закрыт')
  })

  return found?.value ?? 'in_working'
})

/**
 * Инцидент считается устраненным,
 * если выбран статус IncidentStatuses::InWorking.
 */
const isIncidentResolved = computed(() => {
  return props.modelValue.incident.status_resolution === inWorkingStatusValue.value
})

/**
 * Дата вывода из ремонта видна только при InWorking.
 */
const isRepairDateVisible = computed(() => {
  return isIncidentResolved.value
})

/**
 * Выбранный тип инцидента.
 */
const selectedIncidentType = computed(() => {
  return (props.options.incident_types ?? []).find((item) => {
    return String(item.value) === String(props.modelValue.incident.incident_type_id)
  })
})

/**
 * meta выбранного типа инцидента.
 */
const selectedIncidentTypeMeta = computed(() => {
  return selectedIncidentType.value?.meta ?? {}
})

/**
 * Выбранный вид ИТУ.
 */
const selectedItuSpecie = computed(() => {
  return (props.options.itu_species ?? []).find((item) => {
    return String(item.value) === String(props.modelValue.incident.itu_specie_id)
  })
})

/**
 * Показывать ли поле "Вид инцидента (ИТУ)".
 */
const isItuSpecieVisible = computed(() => {
  return Boolean(selectedIncidentTypeMeta.value.has_species)
})

/**
 * Показывать ли поле "Характеристики ИТУ".
 */
const isItuCharacteristicVisible = computed(() => {
  return Boolean(selectedIncidentTypeMeta.value.has_characteristic)
})

/**
 * Показывать ли поле "Объект ИТУ".
 */
const isItuDirectoryObjectVisible = computed(() => {
  return Boolean(selectedItuSpecie.value?.has_directory_objects)
})

/**
 * Показывать ли поле "Неисправность".
 */
const isItuFaultVisible = computed(() => {
  return Boolean(selectedIncidentTypeMeta.value.has_faults)
})

/**
 * Показывать ли поле "Неисправный элемент".
 */
const isItuElementVisible = computed(() => {
  return Boolean(selectedIncidentTypeMeta.value.has_elements)
})

/**
 * Показывать ли поле "Причина".
 */
const isItuReasonBreakdownVisible = computed(() => {
  return Boolean(selectedIncidentTypeMeta.value.has_faults)
})

/**
 * Поле "Уточнение по инциденту" обязательно,
 * если выбран тип инцидента и у него нет неисправностей.
 */
const isDetailIncidentRequired = computed(() => {
  if (!props.modelValue.incident.incident_type_id) {
    return false
  }
  return !Boolean(selectedIncidentTypeMeta.value.has_faults)
})

/**
 * Виды ИТУ только для выбранного типа инцидента.
 */
const filteredItuSpecieOptions = computed(() => {
  return filterByIncidentType(props.options.itu_species ?? [])
})

/**
 * Характеристики только для выбранного типа инцидента.
 */
const filteredItuCharacteristicOptions = computed(() => {
  return filterByIncidentType(props.options.itu_characteristics ?? [])
})

/**
 * Объекты ИТУ:
 * - по выбранному типу инцидента;
 * - если выбран вид ИТУ, дополнительно по виду.
 */
const filteredItuDirectoryObjectOptions = computed(() => {
  const incidentTypeId = props.modelValue.incident.incident_type_id
  const ituSpecieId = props.modelValue.incident.itu_specie_id

  return (props.options.itu_directory_objects ?? []).filter((item) => {
    const sameIncidentType = String(item.incident_type_id ?? '') === String(incidentTypeId ?? '')

    if (!sameIncidentType) {
      return false
    }

    if (!ituSpecieId) {
      return true
    }

    return String(item.itu_specie_id ?? '') === String(ituSpecieId)
  })
})

/**
 * Неисправности только для выбранного типа инцидента.
 */
const filteredItuFaultOptions = computed(() => {
  return filterByIncidentType(props.options.itu_faults ?? [])
})

/**
 * Элементы только для выбранного типа инцидента.
 */
const filteredItuElementOptions = computed(() => {
  return filterByIncidentType(props.options.itu_elements ?? [])
})

/**
 * Причины только для выбранного типа инцидента.
 */
const filteredItuReasonBreakdownOptions = computed(() => {
  return filterByIncidentType(props.options.itu_reason_breakdowns ?? [])
})

/**
 * Текущий incident отдельным computed.
 * Так composable валидации всегда видит актуальную форму.
 */
const incident = computed(() => props.modelValue.incident)

/**
 * Вся frontend-валидация вынесена в отдельный файл,
 * чтобы IncidentForm.vue не разрастался.
 */
const {
  rules,
  requiredState,
  submit,
  resetValidation,
} = useIncidentFormValidation({
  formRef,
  incident,
  activeTab,
  emit,

  isItuSpecieVisible,
  isItuCharacteristicVisible,
  isItuDirectoryObjectVisible,
  isItuFaultVisible,
  isItuElementVisible,
  isItuReasonBreakdownVisible,

  isIncidentResolved,
  isRepairDateVisible,
  isDetailIncidentRequired,
})

/**
 * Обновляем одно поле incident.
 */
function updateIncidentField(field, value) {
  updateIncidentFields({
    [field]: value,
  })
}

/**
 * Обновляем сразу несколько полей incident.
 * Это удобно при выборе объекта или типа инцидента.
 */
function updateIncidentFields(fields) {
  emit('update:modelValue', {
    ...props.modelValue,
    incident: {
      ...props.modelValue.incident,
      ...fields,
    },
  })
}

/**
 * Фильтруем справочник по выбранному типу инцидента.
 */
function filterByIncidentType(items) {
  const incidentTypeId = props.modelValue.incident.incident_type_id

  if (!incidentTypeId) {
    return []
  }

  return items.filter((item) => {
    return String(item.incident_type_id ?? '') === String(incidentTypeId)
  })
}

/**
 * Обработка выбора объекта инфраструктуры.
 *
 * Здесь:
 * - записываем object_infrastructure_id;
 * - определяем группу выбранного объекта;
 * - проверяем, подходит ли текущее подразделение;
 * - если подразделение не подходит — сбрасываем division_id;
 * - автоматически подставляем первый подходящий диспетчерский участок.
 */
function handleObjectInfrastructureChange(value) {
  const selectedOption = objectInfrastructureOptions.value.find((option) => {
    return String(option.value) === String(value)
  })

  const groupId = getGroupInfrastructureObjectIdFromOption(selectedOption)

  const currentDivisionId = props.modelValue.incident.division_id

  const currentDivisionStillAvailable = isDivisionAvailableForGroup(
      currentDivisionId,
      groupId
  )

  const availableDispatchAreas = getDispatchAreasForGroup(groupId)
  updateIncidentFields({
    object_infrastructure_id: value ?? null,

    // Если подразделение больше не подходит выбранному объекту — очищаем.
    division_id: currentDivisionStillAvailable ? currentDivisionId : null,

    // ВАЖНО:
    // Раньше мы подставляли участок только если он один.
    // Но по старой логике Filament нужно брать первый подходящий.
    dispatch_area_id: availableDispatchAreas.length > 0
        ? availableDispatchAreas[0].value
        : null,
  })
}

/**
 * Проверяем, подходит ли подразделение для группы объекта.
 */
function isDivisionAvailableForGroup(divisionId, groupId) {
  if (!divisionId) {
    return true
  }

  if (!groupId) {
    return false
  }

  const division = (props.options.divisions ?? []).find((item) => {
    return String(item.value) === String(divisionId)
  })

  if (!division) {
    return false
  }

  const meta = division.meta ?? {}

  if (!meta.has_group_object) {
    return true
  }

  return String(meta.group_infrastructure_object_id ?? '') === String(groupId)
}

/**
 * Получаем подходящие диспетчерские участки по группе объекта.
 */
function getDispatchAreasForGroup(groupId) {
  const dispatchAreas = props.options.dispatch_areas ?? []

  if (!groupId) {
    return []
  }

  return dispatchAreas.filter((dispatchArea) => {
    return isDispatchAreaAvailableForGroup(dispatchArea, groupId)
  })
}

/**
 * Проверяем, подходит ли диспетчерский участок для группы объекта.
 *
 * Участок может быть связан:
 * - с одной группой: meta.group_infrastructure_object_id
 * - с несколькими группами: meta.group_infrastructure_object_ids
 * - с одной группой на верхнем уровне: group_infrastructure_object_id
 */
function isDispatchAreaAvailableForGroup(dispatchArea, groupId) {
  const meta = dispatchArea.meta ?? {}

  const singleGroupId = meta.group_infrastructure_object_id
      ?? dispatchArea.group_infrastructure_object_id
      ?? null

  const groupIds = meta.group_infrastructure_object_ids ?? []

  if (Array.isArray(groupIds) && groupIds.length > 0) {
    return groupIds.some((item) => {
      return String(item) === String(groupId)
    })
  }

  return String(singleGroupId ?? '') === String(groupId)
}

/**
 * При смене типа инцидента:
 * - записываем новый incident_type_id;
 * - очищаем все зависимые ИТУ-поля;
 * - очищаем reported_by, потому что подсказки "Сообщил" зависят от типа.
 */
function handleIncidentTypeChange(value) {
  updateIncidentFields({
    incident_type_id: value ?? null,

    itu_specie_id: null,
    itu_characteristic_id: null,
    itu_directory_object_id: null,
    itu_fault_id: null,
    itu_element_id: null,
    itu_reason_breakdown_id: null,

    reported_by: null,
  })
}

/**
 * При смене вида инцидента ИТУ:
 * - записываем новый itu_specie_id;
 * - очищаем объект ИТУ;
 * - очищаем характеристику;
 * - очищаем неисправность;
 * - очищаем неисправный элемент;
 * - очищаем причину.
 *
 * Это нужно, потому что эти значения могут больше не подходить
 * к новому выбранному виду инцидента.
 */
function handleItuSpecieChange(value) {
  updateIncidentFields({
    itu_specie_id: value ?? null,
    itu_directory_object_id: null,
    itu_characteristic_id: null,
    itu_fault_id: null,
    itu_element_id: null,
    itu_reason_breakdown_id: null,
  })
}

/**
 * Получаем ID группы объекта инфраструктуры из option.
 *
 * API объекта сейчас отдает:
 * group_infrastructure_object_id: 10
 *
 * Но оставляем fallback на другие варианты,
 * чтобы компонент не сломался, если формат позже изменится.
 */
function getGroupInfrastructureObjectIdFromOption(option) {
  return option?.group_infrastructure_object_id
      ?? option?.group_id
      ?? option?.group?.id
      ?? option?.meta?.group_infrastructure_object_id
      ?? null
}

/**
 * При смене классификации:
 * - если это не ННР, очищаем номер ННР.
 */
function handleIncidentClassificationChange(value) {
  updateIncidentFields({
    incident_classification: value,
    number_nnr: value === 'ННР'
        ? props.modelValue.incident.number_nnr
        : null,
  })
}

/**
 * При смене статуса:
 * - если статус = InWorking, а repair_date еще нет, ставим текущую дату;
 * - если статус другой, очищаем repair_date.
 */
function handleStatusResolutionChange(value) {
  updateIncidentFields({
    status_resolution: value,
    repair_date: value === inWorkingStatusValue.value
        ? (props.modelValue.incident.repair_date ?? dayjs())
        : null,
  })
}

/**
 * Даем родителю доступ к submit/resetValidation.
 *
 * Это нужно, чтобы IncidentCreateModal.vue мог вызвать:
 * incidentFormRef.value?.submit()
 */
defineExpose({
  submit,
  resetValidation,
})
</script>

<style scoped>
/* Select объекта инфраструктуры внутри формы */
.oi-object-form-select :deep(.ant-select-selector) {
  height: auto !important;
  min-height: 40px !important;
  padding-top: 4px !important;
  padding-bottom: 4px !important;
  align-items: center !important;
}

/* Поле поиска внутри select */
.oi-object-form-select :deep(.ant-select-selection-search) {
  display: flex !important;
  align-items: center !important;
  height: 100% !important;
}

/* Input поиска */
.oi-object-form-select :deep(.ant-select-selection-search-input) {
  height: 100% !important;
  line-height: 1.25 !important;
}

/* Placeholder */
.oi-object-form-select :deep(.ant-select-selection-placeholder) {
  display: flex !important;
  align-items: center !important;
  height: 100% !important;
  line-height: 1.25 !important;
}

/* Выбранное значение */
.oi-object-form-select :deep(.ant-select-selection-item) {
  display: block !important;
  white-space: normal !important;
  line-height: 1.25 !important;
  padding-top: 1px !important;
  padding-bottom: 1px !important;
  padding-right: 28px !important;
}

/* Стрелка Ant Design */
.oi-object-form-select :deep(.ant-select-arrow) {
  top: 50% !important;
  right: 11px !important;
  margin-top: 0 !important;
  transform: translateY(-50%) !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  line-height: 1 !important;
}

/* Крестик очистки */
.oi-object-form-select :deep(.ant-select-clear) {
  top: 50% !important;
  right: 11px !important;
  margin-top: 0 !important;
  transform: translateY(-50%) !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  line-height: 1 !important;
}

/* Выпадающий список объекта */
:global(.oi-object-form-select-popup .ant-select-item-option-content) {
  white-space: normal !important;
  line-height: 1.25 !important;
}
</style>