import {computed, nextTick, watch} from 'vue'
import dayjs from 'dayjs'

/**
 * Валидация формы инцидента.
 *
 * Здесь живут:
 * - динамические required;
 * - rules для Ant Design Vue;
 * - submit формы;
 * - resetValidation;
 * - открытие вкладки с первой ошибкой.
 */
export function useIncidentFormValidation({
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
                                          }) {
    /**
     * Динамическая обязательность полей.
     *
     * Главное правило:
     * required = поле видно + бизнес-условие.
     */
    const requiredState = computed(() => {
        return {
            // Всегда обязательные поля
            datetime_incident: true,
            object_infrastructure_id: true,
            location: true,
            division_id: true,
            incident_type_id: true,
            incident_classification: true,
            status_resolution: true,
            dispatch_area_id: true,

            // Обязательные, если поле видно
            itu_specie_id: isItuSpecieVisible.value,
            itu_characteristic_id: isItuCharacteristicVisible.value,
            itu_directory_object_id: isItuDirectoryObjectVisible.value,

            // Обязательные только если:
            // 1. статус = IncidentStatuses::InWorking
            // 2. поле реально видно
            itu_fault_id: isIncidentResolved.value && isItuFaultVisible.value,
            itu_element_id: isIncidentResolved.value && isItuElementVisible.value,
            itu_reason_breakdown_id: isIncidentResolved.value && isItuReasonBreakdownVisible.value,

            // Поле всегда видно, но обязательно только при InWorking
            appropriate_measures: isIncidentResolved.value,

            // Поле показывается только при InWorking
            repair_date: isRepairDateVisible.value,

            // Обязательное, если у выбранного типа нет неисправностей
            detail_incident: isDetailIncidentRequired.value,
        }
    })

    /**
     * Rules для Ant Design Vue.
     */
    const rules = computed(() => {
        return {
            datetime_incident: requiredRule(requiredState.value.datetime_incident, 'Укажите дату и время инцидента'),
            object_infrastructure_id: requiredRule(requiredState.value.object_infrastructure_id, 'Выберите объект инфраструктуры'),
            location: requiredRule(requiredState.value.location, 'Выберите местоположение'),
            division_id: requiredRule(requiredState.value.division_id, 'Выберите подразделение'),
            incident_type_id: requiredRule(requiredState.value.incident_type_id, 'Выберите тип инцидента'),
            incident_classification: requiredRule(requiredState.value.incident_classification, 'Выберите классификацию'),
            status_resolution: requiredRule(requiredState.value.status_resolution, 'Выберите статус устранения'),
            dispatch_area_id: requiredRule(requiredState.value.dispatch_area_id, 'Выберите диспетчерский участок'),
            itu_specie_id: requiredRule(requiredState.value.itu_specie_id, 'Выберите вид ИТУ'),
            itu_characteristic_id: requiredRule(requiredState.value.itu_characteristic_id, 'Выберите характеристику ИТУ'),
            itu_directory_object_id: requiredRule(requiredState.value.itu_directory_object_id, 'Выберите объект ИТУ'),
            itu_fault_id: requiredRule(requiredState.value.itu_fault_id, 'Выберите неисправность'),
            itu_element_id: requiredRule(requiredState.value.itu_element_id, 'Выберите неисправный элемент'),
            itu_reason_breakdown_id: requiredRule(requiredState.value.itu_reason_breakdown_id, 'Выберите причину неисправности'),
            detail_incident: [...requiredRule(requiredState.value.detail_incident, 'Укажите описание инцидента/неисправности'), {
                max: 450, message: 'Максимум 450 символов', trigger: 'blur',
            },],
            detail_object_incident: [{
                max: 450, message: 'Максимум 450 символов', trigger: 'blur',
            },],
            detail_location: [{
                max: 800, message: 'Максимум 800 символов', trigger: 'blur',
            },],
            reported_by: [{
                max: 600, message: 'Максимум 600 символов', trigger: 'blur',
            },],
            number_nnr: [{
                max: 255, message: 'Максимум 255 символов', trigger: 'blur',
            },],
            appropriate_measures: [...requiredRule(requiredState.value.appropriate_measures, 'Укажите принятые меры'), {
                max: 1500, message: 'Максимум 1500 символов', trigger: 'blur',
            },],
            repair_date: [...requiredRule(requiredState.value.repair_date, 'Укажите дату вывода из ремонта'), {
                validator: validateRepairDate, trigger: 'change',
            },],
            note: [{
                max: 1500, message: 'Максимум 1500 символов', trigger: 'blur',
            },],
        }
    })

    /**
     * Когда меняется динамическая обязательность,
     * очищаем старые ошибки скрытых/необязательных полей.
     */
    watch(() => requiredState.value, async () => {
        await nextTick()
        formRef.value?.clearValidate(['itu_specie_id', 'itu_characteristic_id', 'itu_directory_object_id', 'itu_fault_id', 'itu_element_id', 'itu_reason_breakdown_id', 'detail_incident', 'appropriate_measures', 'repair_date',])
    }, {
        deep: true,
    })

    /**
     * Запуск frontend-валидации.
     */
    async function submit() {
        try {
            await formRef.value?.validate()
            emit('submit')
        } catch (error) {
            const firstField = getFirstErrorField(error)
            if (firstField) {
                openTabByField(firstField)
            }
            emit('validation-failed', error)
        }
    }

    /**
     * Очистить визуальные ошибки Ant Design Vue.
     */
    function resetValidation() {
        formRef.value?.clearValidate()
    }

    /**
     * Создаем required-правило только если поле реально обязательное.
     */
    function requiredRule(isRequired, message, trigger = 'change') {
        if (!isRequired) {
            return []
        }

        return [
            {
                required: true,
                message,
                trigger,
            },
        ]
    }

    /**
     * Проверяем, что дата вывода из ремонта
     * не раньше даты инцидента.
     */
    function validateRepairDate(_rule, value) {
        const currentIncident = incident.value ?? {}

        if (!value || !currentIncident.datetime_incident) {
            return Promise.resolve()
        }
        const repairDate = dayjs(value)
        const incidentDate = dayjs(currentIncident.datetime_incident)
        if (repairDate.isBefore(incidentDate)) {
            return Promise.reject(new Error('Дата вывода из ремонта не может быть раньше даты инцидента'))
        }
        return Promise.resolve()
    }

    /**
     * Достаем первое поле с ошибкой.
     */
    function getFirstErrorField(error) {
        return error?.errorFields?.[0]?.name?.[0] ?? null
    }

    /**
     * Открываем вкладку с первой ошибкой.
     *
     * Сейчас все основные/ИТУ-поля находятся во вкладке "Основные данные".
     */
    function openTabByField(field) {
        const map = {
            datetime_incident: 'main',
            object_infrastructure_id: 'main',
            location: 'main',
            division_id: 'main',
            reported_by: 'main',
            detail_location: 'main',
            incident_type_id: 'main',
            itu_specie_id: 'main',
            itu_characteristic_id: 'main',
            itu_directory_object_id: 'main',
            itu_fault_id: 'main',
            itu_element_id: 'main',
            itu_reason_breakdown_id: 'main',
            detail_object_incident: 'main',
            detail_incident: 'main',
            incident_classification: 'main',
            number_nnr: 'main',
            appropriate_measures: 'main',
            status_resolution: 'main',
            repair_date: 'main',
            dispatch_area_id: 'additional',
            is_in_report: 'additional',
            note: 'additional',
        }
        activeTab.value = map[field] ?? 'main'
    }

    /**
     * После изменения поля синхронизируем визуальное состояние Ant Design validation.
     *
     * Почему так:
     * emit('update:modelValue') обновляет props не мгновенно.
     * Поэтому validateFields нужно запускать после nextTick и после завершения
     * внутреннего change-цикла Ant Design Vue.
     */
    async function syncChangedFieldsValidation(fields) {
        const fieldNames = Array.isArray(fields)
            ? fields
            : [fields]

        await nextTick()

        setTimeout(() => {
            fieldNames.forEach((field) => {
                if (!field || !formRef.value) {
                    return
                }

                const value = incident.value?.[field]

                /**
                 * Если поле сейчас не обязательно — просто очищаем ошибку.
                 */
                if (!isFieldRequired(field)) {
                    formRef.value.clearValidate([field])
                    return
                }

                /**
                 * Если поле обязательное, но значение уже есть —
                 * сразу снимаем ошибку, не ждем blur.
                 */
                if (!isEmptyValue(value)) {
                    formRef.value.clearValidate([field])
                    return
                }

                /**
                 * Если поле обязательное и значение пустое —
                 * показываем required-ошибку.
                 */
                formRef.value.validateFields([field]).catch(() => {})
            })
        }, 0)
    }

    function isFieldRequired(field) {
        return Boolean(requiredState.value[field])
    }

    function isEmptyValue(value) {
        return value === null
            || value === undefined
            || value === ''
    }

    return {
        rules, requiredState, submit, resetValidation, syncChangedFieldsValidation
    }
}