import { ref } from 'vue'
import axios from 'axios'
import { route } from 'ziggy-js'

/**
 * Загружает данные, необходимые для формы создания инцидента.
 *
 * Здесь мы получаем:
 * - defaults — значения по умолчанию
 * - options — справочники для select
 * - permissions — права пользователя для формы
 *
 * Важно:
 * object_infrastructure_id здесь НЕ загружаем.
 * Он будет работать отдельно через useObjectInfrastructureSelect.js.
 */
export function useIncidentFormOptions() {
    const loading = ref(false)
    const loaded = ref(false)
    const error = ref(null)

    const defaults = ref(null)
    const options = ref({})
    const permissions = ref({})

    /**
     * Загружаем данные формы создания с backend.
     */
    async function loadCreateForm() {
        loading.value = true
        loaded.value = false
        error.value = null

        try {
            const response = await axios.get(route('app.api.incidents.create-form'))

            const data = response.data?.data ?? {}

            defaults.value = data.defaults ?? null
            options.value = data.options ?? {}
            permissions.value = data.permissions ?? {}

            loaded.value = true

            return data
        } catch (e) {
            console.error('Ошибка загрузки формы создания инцидента:', e)

            error.value = 'Не удалось загрузить данные формы создания инцидента'
            loaded.value = false

            throw e
        } finally {
            loading.value = false
        }
    }

    /**
     * Сбрасываем состояние.
     * Это нужно, чтобы при повторном открытии модалки не показывались старые данные.
     */
    function resetIncidentFormOptions() {
        loading.value = false
        loaded.value = false
        error.value = null

        defaults.value = null
        options.value = {}
        permissions.value = {}
    }

    return {
        loading,
        loaded,
        error,
        defaults,
        options,
        permissions,
        loadCreateForm,
        resetIncidentFormOptions,
    }
}