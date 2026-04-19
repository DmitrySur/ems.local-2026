<script setup>
import {DoubleLeftOutlined, DoubleRightOutlined} from '@ant-design/icons-vue';

defineProps({
    meta: {
        type: Object,
        required: true,
    },
})

defineEmits(['change'])
</script>

<template>
    <div class="flex justify-end">
        <a-pagination
            :current="meta.current_page"
            :page-size="meta.per_page"
            :total="meta.total"
            :show-size-changer="true"
            :locale="{ items_per_page: '/ стр.' }"
            @change="$emit('change', $event)"
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



