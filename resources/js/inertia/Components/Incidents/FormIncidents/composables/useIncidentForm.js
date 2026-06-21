import { ref } from 'vue'
import dayjs from 'dayjs'

/**
 * Composable для состояния формы инцидента.
 *
 * Здесь храним:
 * - основную форму incident
 * - локальные строки таблиц referral_workers
 * - локальные строки information_workers
 * - локальные строки chronicles
 *
 * Пока работаем только с режимом создания.
 */
export function useIncidentForm() {
    const form = ref(createEmptyForm())

    /**
     * Создаем полностью пустую форму.
     * Это нужно, чтобы форма всегда имела одинаковую структуру.
     */
    function createEmptyForm() {
        return {
            incident: {
                datetime_incident: null,

                object_infrastructure_id: null,
                location: null,
                detail_location: null,
                reported_by: null,
                division_id: null,

                incident_type_id: null,
                itu_specie_id: null,
                itu_characteristic_id: null,
                itu_directory_object_id: null,
                itu_fault_id: null,
                itu_element_id: null,
                itu_reason_breakdown_id: null,

                detail_object_incident: null,
                detail_incident: null,

                incident_classification: 'ННР',
                number_nnr: null,

                appropriate_measures: null,
                status_resolution: null,
                status_incident: 'opened',
                repair_date: null,

                dispatch_area_id: null,
                is_in_report: true,
                note: null,
            },

            referral_workers: [],
            information_workers: [],
            chronicles: [],
        }
    }

    /**
     * Инициализируем форму данными, которые пришли с backend create-form API.
     */
    function initForm(defaults) {
        const incident = defaults?.incident ?? {}

        form.value = {
            incident: {
                ...createEmptyForm().incident,
                ...incident,

                // Ant Design Vue DatePicker работает с dayjs-объектом.
                datetime_incident: toDayjs(incident.datetime_incident),
                repair_date: toDayjs(incident.repair_date),
            },

            referral_workers: defaults?.referral_workers ?? [],
            information_workers: defaults?.information_workers ?? [],
            chronicles: defaults?.chronicles ?? [],
        }
    }

    /**
     * Полностью сбрасываем форму.
     */
    function resetForm() {
        form.value = createEmptyForm()
    }

    /**
     * Обновляем одно поле внутри form.incident.
     */
    function updateIncidentField(field, value) {
        form.value = {
            ...form.value,
            incident: {
                ...form.value.incident,
                [field]: value,
            },
        }
    }

    /**
     * Обновляем всю форму целиком.
     * Пригодится для v-model между IncidentCreateModal и IncidentForm.
     */
    function setForm(value) {
        form.value = value
    }

    /**
     * Безопасно преобразуем дату из backend в dayjs.
     */
    function toDayjs(value) {
        if (!value) {
            return null
        }

        const date = dayjs(value)

        return date.isValid() ? date : null
    }

    return {
        form,
        initForm,
        resetForm,
        setForm,
        updateIncidentField,
    }
}