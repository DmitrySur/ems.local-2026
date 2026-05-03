import {markRaw, onMounted, ref} from 'vue'
import axios from 'axios'
import {route} from 'ziggy-js'
import * as TablerIcons from '@tabler/icons-vue'

import {getObjectInfrastructureColor} from '@/Support/ObjectInfrastructureColorsAndIcons/objectInfrastructureColors'
import {getObjectInfrastructureIconConfig} from '@/Support/ObjectInfrastructureColorsAndIcons/objectInfrastructureIcons'
import {getObjectInfrastructurePrefix} from '@/Support/ObjectInfrastructureColorsAndIcons/objectInfrastructurePrefixes'

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

            // Чтобы select не ломал таблицу
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

    function getIconComponent(type) {
        const config = getObjectInfrastructureIconConfig(type)

        return markRaw(
            TablerIcons[config.name] ?? TablerIcons.IconMapPin
        )
    }

    function iconStyle(type, shortName) {
        const config = getObjectInfrastructureIconConfig(type)

        return {
            color: getObjectInfrastructureColor(shortName),
            width: '18px',
            height: '18px',
            minWidth: '18px',
            flex: '0 0 18px',
            transform: `rotate(${config.rotate}deg)`,
        }
    }

    function getPrefix(type) {
        return getObjectInfrastructurePrefix(type)
    }

    onMounted(() => {
        if (selectedValueRef.value) {
            void loadOptions('', selectedValueRef.value)
            return
        }

        void loadOptions()
    })

    return {
        options,
        loading,
        onSearch,
        loadOptions,
        getIconComponent,
        iconStyle,
        getPrefix
    }
}
