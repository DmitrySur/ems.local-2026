<?php

namespace App\Inertia\Incidents\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncidentIndexRequest extends FormRequest
{
    /**
     * Простые integer фильтры (scalar)
     */
    private const INTEGER_FILTERS = [
        'search_by_number',
        'division_id',
        'incident_type_id',
        'object_infrastructure_id'
    ];


    /**
     * Простые строковые фильтры (scalar)
     */
    private const STRING_FILTERS = [
        // 'status_incident',
    ];

    /**
     * Фильтры типа IN (массив значений)
     * пример: division_ids[]=1&division_ids[]=2
     */
    private const INTEGER_ARRAY_FILTERS = [
        // 'division_ids',
    ];

    /**
     * Фильтры диапазонов (даты и т.п.)
     * пример:
     * filter[datetime_incident][from]=...
     * filter[datetime_incident][to]=...
     */
    private const DATE_RANGE_FILTERS = [
        // 'datetime_incident',
    ];

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', 'string', 'in:datetime_incident,-datetime_incident'],
            'filter' => ['nullable', 'array'],
            'filter.search_by_number' => ['nullable', 'integer', 'min:1'],
            'filter.division_id' => ['nullable', 'integer', 'min:1'],
            'filter.incident_type_id' => ['nullable', 'integer', 'min:1'],
            'filter.object_infrastructure_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Для GET-запроса берём параметры именно из query-строки.
        $filter = (array)$this->query('filter', []);

        // Собираем список разрешённых фильтров.
        $allowedFilters = array_merge(
            self::INTEGER_FILTERS,
            self::STRING_FILTERS,
            self::INTEGER_ARRAY_FILTERS,
            self::DATE_RANGE_FILTERS,
        );

        // Удаляем все фильтры, которые не описаны в константах выше.
        $filter = array_intersect_key($filter, array_flip($allowedFilters));

        foreach ($filter as $key => $value) {
            if (in_array($key, self::INTEGER_FILTERS, true)) {
                $filter[$key] = $this->normalizeInteger($value);
            } elseif (in_array($key, self::STRING_FILTERS, true)) {
                $filter[$key] = $this->normalizeString($value);
            } elseif (in_array($key, self::INTEGER_ARRAY_FILTERS, true)) {
                $filter[$key] = $this->normalizeIntegerArray($value);
            } elseif (in_array($key, self::DATE_RANGE_FILTERS, true)) {
                $filter[$key] = $this->normalizeDateRange($value);
            }

            // Если после нормализации значение пустое — убираем фильтр.
            if ($filter[$key] === null || $filter[$key] === []) {
                unset($filter[$key]);
            }
        }

        // Важно: перезаписываем query, чтобы QueryBuilder не получил старые вложенные массивы.
        $query = $this->query();
        $query['filter'] = $filter;
        $this->query->replace($query);

        // Синхронизируем input, чтобы validated() вернул уже очищенные данные.
        $this->merge([
            'filter' => $filter,
        ]);
    }

    /**
     * Нормализует одиночный integer-фильтр.
     */
    private function normalizeInteger(mixed $value): ?int
    {
        $value = $this->extractScalarValue($value);

        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string)$value);

        return ctype_digit($value) ? (int)$value : null;
    }

    /**
     * Нормализует одиночный string-фильтр.
     */
    private function normalizeString(mixed $value): ?string
    {
        $value = $this->extractScalarValue($value);

        if ($value === null) {
            return null;
        }

        $value = trim((string)$value);

        return $value !== '' ? $value : null;
    }

    /**
     * Нормализует IN-фильтр с массивом целых чисел.
     */
    private function normalizeIntegerArray(mixed $value): array
    {
        if (!is_array($value)) {
            $value = explode(',', (string)$value);
        }

        return collect($value)
            ->map(fn(mixed $item): mixed => $this->extractScalarValue($item))
            ->filter(fn(mixed $item): bool => $item !== null && $item !== '')
            ->map(fn(mixed $item): string => trim((string)$item))
            ->filter(fn(string $item): bool => ctype_digit($item))
            ->map(fn(string $item): int => (int)$item)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Нормализует фильтр диапазона дат.
     */
    private function normalizeDateRange(mixed $value): ?array
    {
        if (!is_array($value)) {
            return null;
        }

        $from = $value['from'] ?? $value[0] ?? null;
        $to = $value['to'] ?? $value[1] ?? null;

        $from = is_string($from) ? trim($from) : null;
        $to = is_string($to) ? trim($to) : null;

        return array_filter([
            'from' => $from,
            'to' => $to,
        ], fn(?string $date): bool => $date !== null && $date !== '');
    }

    /**
     * Достаёт простое значение из обычного значения или массива Ant Design.
     */
    private function extractScalarValue(mixed $value): mixed
    {
        if (is_array($value)) {
            $value = $value['value'] ?? $value[0] ?? null;
        }

        return is_array($value) ? null : $value;
    }
}
