<script setup>
import {computed, markRaw} from 'vue'
import * as TablerIcons from '@tabler/icons-vue'

import {getObjectInfrastructureColor} from '@/Support/ObjectInfrastructureColorsAndIcons/objectInfrastructureColors'
import {getObjectInfrastructureIconConfig} from '@/Support/ObjectInfrastructureColorsAndIcons/objectInfrastructureIcons'
import {getObjectInfrastructurePrefix} from '@/Support/ObjectInfrastructureColorsAndIcons/objectInfrastructurePrefixes'

const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    type: {
        type: [String, null],
        default: null,
    },
    shortName: {
        type: [String, null],
        default: null,
    },
})

const iconConfig = computed(() => getObjectInfrastructureIconConfig(props.type))

const iconComponent = computed(() => {
    return markRaw(TablerIcons[iconConfig.value.name] ?? TablerIcons.IconMapPin)
})

const prefix = computed(() => getObjectInfrastructurePrefix(props.type))

const iconStyle = computed(() => ({
    color: getObjectInfrastructureColor(props.shortName),
    width: '18px',
    height: '18px',
    minWidth: '18px',
    transform: `rotate(${iconConfig.value.rotate}deg)`,
}))
</script>

<template>
    <span class="oi-object-label">
        <component
            :is="iconComponent"
            :size="18"
            :stroke-width="2"
            :style="iconStyle"
            class="oi-object-label__icon"
        />

        <span
            v-if="prefix"
            class="oi-object-label__prefix"
        >
            {{ prefix }}
        </span>

        <span class="oi-object-label__text">
            {{ label }}
        </span>
    </span>
</template>

<style scoped>
.oi-object-label {
    display: inline;
    white-space: normal;
    line-height: 1.25;
}

.oi-object-label__icon {
    display: inline-block;
    vertical-align: -4px;
    margin-right: 6px;
}

.oi-object-label__prefix {
    display: inline;
    margin-right: 6px;
    white-space: nowrap;
    color: #6c757d;
}

.oi-object-label__text {
    display: inline;
    white-space: normal;
    overflow-wrap: anywhere;
}
</style>
