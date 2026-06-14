<script setup>
import {computed} from 'vue'

const props = defineProps({
  value: {
    type: [String, Number, null],
    default: null,
  },
  options: {
    type: Array,
    default: () => [],
  },
  defaultValue: {
    type: [String, Number],
    default: 'unresolved_all',
  },
  total: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['change'])

const segmentedOptions = computed(() => {
  return props.options.map(option => ({
    value: option.value,
    payload: {
      title: option.label,
    },
  }))
})

const segmentedValue = computed({
  get() {
    return props.value ?? props.defaultValue
  },
  set(value) {
    emit('change', value)
  },
})

function isSelected(value) {
  return String(value) === String(segmentedValue.value)
}
</script>

<template>
  <div class="incident-preset-filters card bg-white border rounded-3 shadow-sm px-3 py-2 mx-auto mb-2">
    <a-segmented
        v-model:value="segmentedValue"
        :options="segmentedOptions"
        size="large"
    >
      <template #label="{ value: val, payload = {} }">
        <span class="incident-preset-filters__option">
        <span class="incident-preset-filters__title">
            {{ payload.title }}
        </span>
          <sup
              v-if="isSelected(val)"
              class="incident-preset-filters__count text-red"
          >
            {{ total }}
          </sup>
        </span>
      </template>
    </a-segmented>
  </div>
</template>

<style scoped>
.incident-preset-filters {
  width: fit-content;
  max-width: 100%;
}

/* Ant segmented wrapper */
.incident-preset-filters :deep(.ant-segmented) {
  max-width: 100%;
  padding: 5px;
}

/* перенос строк, если сегменты не помещаются */
.incident-preset-filters :deep(.ant-segmented-group) {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

/* каждый пункт держит ширину по содержимому */
.incident-preset-filters :deep(.ant-segmented-item) {
  flex: 0 0 auto;
  width: auto !important;
}

/*
 * Важно для кастомного template:
 * Ant кладет slot внутрь .ant-segmented-item-label,
 * поэтому вертикальное выравнивание нужно делать именно здесь.
 */
.incident-preset-filters :deep(.ant-segmented-item-label) {
  display: flex !important;
  align-items: center !important;
  justify-content: center;
  width: auto !important;
  min-width: max-content;
  white-space: nowrap;
  overflow: visible;
  line-height: 1 !important;
}

/* thumb при переносе строк может съезжать */
.incident-preset-filters :deep(.ant-segmented-thumb) {
  display: none;
}

/* активный текст */
.incident-preset-filters :deep(.ant-segmented-item-selected .ant-segmented-item-label) {
  color: #066fd1;
}

/* кастомный label */
.incident-preset-filters__option {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  white-space: nowrap;
  line-height: 1;
}

/* текст сегмента */
.incident-preset-filters__title {
  display: inline-flex;
  align-items: center;
  line-height: 1;
}

/* Tabler badge */
.incident-preset-filters__count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 auto;
  line-height: 1;
  vertical-align: middle;
  margin-top: 1px;
}
</style>
