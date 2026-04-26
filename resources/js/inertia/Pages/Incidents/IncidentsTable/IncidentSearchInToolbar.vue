<script setup>
const props = defineProps({
    state: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['reload'])

function search() {
    props.state.page = 1
    emit('reload', 1)
}
</script>

<template>
    <div class="d-flex align-items-center incident-search">
        <a-input-search
            v-model:value="props.state.search_by_number"
            placeholder="Поиск по номеру"
            allow-clear
            @search="search"
        />
    </div>
</template>

<style scoped>
.incident-search {
    flex: 0 0 auto;
}

.incident-search :deep(.ant-input-wrapper) {
    display: flex;
    align-items: center;
}

.incident-search :deep(.ant-input-affix-wrapper) {
    min-width: 0;
    height: 32px !important;
    border-radius: 8px 0 0 8px !important;
}

.incident-search :deep(.ant-input-clear-icon) {
    height: 100%;
}

.incident-search :deep(.ant-input-group-addon) {
    padding: 0 !important;
    width: 48px;
}

.incident-search :deep(.ant-input-search-button) {
    width: 48px !important;
    height: 32px !important;
    border-radius: 0 8px 8px 0 !important;
}
</style>
