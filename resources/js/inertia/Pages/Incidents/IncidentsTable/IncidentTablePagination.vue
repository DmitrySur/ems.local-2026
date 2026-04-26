<script setup>
import {DoubleLeftOutlined, DoubleRightOutlined} from '@ant-design/icons-vue';

defineProps({
    meta: {
        type: Object,
        required: true,
    },
})


const emit = defineEmits(['page-change', 'page-size-change'])

function onPageChange(page, pageSize) {
    emit('page-change', page)
}

function onShowSizeChange(current, size) {
    emit('page-size-change', size)
}
</script>

<template>
    <div class="flex justify-end">
        <a-pagination
            :current="meta.current_page"
            :page-size="meta.per_page"
            :total="meta.total"
            :show-size-changer="true"
            :page-size-options="['10', '15', '25', '50', '100']"
            :locale="{ items_per_page: '/ стр.' }"
            @change="onPageChange"
            @showSizeChange="onShowSizeChange"
        >
            <template #itemRender="{ type, originalElement }">
                <!-- Кнопка "Вперед на 5 страниц" -->
                <template v-if="['jump-next', 'jump-prev'].includes(type)">
                    <span class="custom-jump">...</span>
                </template>
                <!-- Все остальные элементы (цифры, стрелки влево/вправо) -->
                <template v-else>
                    <component :is="originalElement"/>
                </template>
            </template>
        </a-pagination>
    </div>
</template>


