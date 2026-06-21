<template>
  <a-modal
      :open="open"
      title="Создание инцидента"
      width="1200px"
      :footer="null"
      destroy-on-close
      @cancel="closeModal"
  >
    <a-spin :spinning="loading">
      <!-- Ошибка загрузки справочников -->
      <a-alert
          v-if="error"
          type="error"
          show-icon
          :message="error"
          class="mb-3"
      />

      <!-- Пока данные грузятся -->
      <div v-if="loading" class="py-4">
        Загрузка данных формы...
      </div>

      <!-- Данные успешно загружены, показываем форму -->
      <template v-else-if="loaded">
        <IncidentForm
            ref="incidentFormRef"
            v-model="form"
            :options="options"
            :permissions="permissions"
            mode="create"
            @submit="handleFormSubmit"
            @validation-failed="handleValidationFailed"
        />

        <div class="d-flex justify-content-end gap-2 mt-4">
          <a-button @click="closeModal">
            Отмена
          </a-button>

          <a-button
              type="primary"
              @click="submitForm"
          >
            Сохранить
          </a-button>
        </div>
      </template>

      <!-- Состояние до загрузки -->
      <div v-else class="py-4 text-secondary">
        Данные формы еще не загружены.
      </div>
    </a-spin>
  </a-modal>
</template>

<script setup>
import { watch, ref } from 'vue'
import { message } from 'ant-design-vue'

import IncidentForm from './IncidentForm.vue'
import { useIncidentForm } from './composables/useIncidentForm'
import { useIncidentFormOptions } from './composables/useIncidentFormOptions'

const props = defineProps({
  open: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:open',
  // Используем позже после успешного создания.
  'created',
])

const {
  form,
  initForm,
  resetForm,
} = useIncidentForm()

const {
  loading,
  loaded,
  error,
  options,
  permissions,
  loadCreateForm,
  resetIncidentFormOptions,
} = useIncidentFormOptions()

const incidentFormRef = ref(null)

/**
 * Следим за открытием модального окна.
 *
 * Как только open стал true:
 * - сбрасываем старые справочники;
 * - сбрасываем старую форму;
 * - загружаем данные create-form API;
 * - инициализируем форму defaults-значениями.
 */
watch(
    () => props.open,
    async (isOpen) => {
      if (!isOpen) {
        return
      }

      resetForm()
      resetIncidentFormOptions()

      try {
        const data = await loadCreateForm()

        initForm(data.defaults)
      } catch (e) {
        message.error('Не удалось загрузить форму создания инцидента')
      }
    }
)

/**
 * Закрытие модального окна.
 *
 * Dirty state добавим отдельным этапом.
 */
function closeModal() {
  emit('update:open', false)
}

function submitForm() {
  incidentFormRef.value?.submit()
}

function handleFormSubmit() {
  message.success('Frontend-валидация прошла успешно. Следующим этапом добавим сохранение.')
}

function handleValidationFailed() {
  message.error('Заполните обязательные поля формы')
}
</script>