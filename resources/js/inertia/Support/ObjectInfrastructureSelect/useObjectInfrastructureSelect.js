import {computed, onMounted, ref, watch} from 'vue'
import axios from 'axios'
import {route} from 'ziggy-js'

const forbiddenNameRegexp = /[^А-Яа-яЁё0-9 .-]/gu

export function useObjectInfrastructureSelect(selectedValueRef) {
    const options = ref([])
    const loading = ref(false)

    let searchTimer = null

    function normalizeName(value) {
        return String(value ?? '')
            .replace(forbiddenNameRegexp, '')
            .slice(0, 100)
    }

    async function loadOptions(name = '', selectedId = null) {
        loading.value = true

        try {
            const response = await axios.get(route('app.api.object-infrastructures.select'), {
                params: {
                    filter: {
                        object_infrastructure_name: normalizeName(name),
                        object_infrastructure_id: selectedId || null,
                    },
                },
            })

            options.value = response.data.data ?? []
        } catch (error) {
            console.error('Ошибка загрузки объектов инфраструктуры:', error)
            options.value = []
        } finally {
            loading.value = false
        }
    }

    function onSearch(value) {
        clearTimeout(searchTimer)

        searchTimer = setTimeout(() => {
            void loadOptions(value, null)
        }, 350)
    }

    const selectedOption = computed(() => {
        return options.value.find((item) => {
            return String(item.value) === String(selectedValueRef.value)
        })
    })

    onMounted(() => {
        if (selectedValueRef.value) {
            void loadOptions('', selectedValueRef.value)
            return
        }

        void loadOptions()
    })

    watch(
        selectedValueRef,
        (value, oldValue) => {
            if (String(value ?? '') === String(oldValue ?? '')) {
                return
            }

            if (value) {
                void loadOptions('', value)
                return
            }

            options.value = []
        }
    )

    return {
        options,
        loading,
        onSearch,
        loadOptions,
        selectedOption,
    }
}
