<?php

namespace App\Services\Incident\Reports\UtilsReports;

use Carbon\Carbon;
use Exception;

class DatePeriodService
{
    private Carbon $startDateTime;
    private Carbon $endDateTime;

    /**
     * Устанавливает дату и время начала и окончания на основе штатного поля Filament.
     *
     * @param string $date Дата в формате 'Y-m-d H:i:s'.
     * @param string $startTime Время начала в формате 'Y-m-d H:i:s'.
     * @param string $endTime Время окончания в формате 'Y-m-d H:i:s'.
     * @throws Exception Если входные данные некорректны.
     */
    public function setFromFilamentFields(string $date, string $startTime, string $endTime): void
    {
        try {
            // Валидация формата входных данных
            foreach (['date' => $date, 'startTime' => $startTime, 'endTime' => $endTime] as $key => $value) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                    throw new Exception("Поле {$key} должно быть в формате Y-m-d H:i:s.");
                }
            }

            // Преобразуем дату в объект Carbon
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $date);

            // Извлекаем только время из $startTime и $endTime
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime)->format('H:i:s');
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime)->format('H:i:s');

            // Устанавливаем дату и время окончания
            $this->endDateTime = $date->copy()->setTimeFromTimeString($endTime);

            // Устанавливаем дату и время начала (дата окончания минус 1 день)
            $this->startDateTime = $this->endDateTime->copy()->subDay()->setTimeFromTimeString($startTime);
        } catch (Exception $e) {
            throw new Exception('Ошибка при обработке данных Filament: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function setFromFilamentRangeAndTimeFields(string $dateRange, string $startTime, string $endTime): void
    {
        try {
            // Валидация формата диапазона дат
            if (!preg_match('/^\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}$/', $dateRange)) {
                throw new Exception('Диапазон дат должен быть в формате DD/MM/YYYY - DD/MM/YYYY.');
            }

            // Разделение диапазона на начальную и конечную даты
            [$startDate, $endDate] = explode(' - ', $dateRange);

            // Преобразование начальной и конечной дат в формат Carbon
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate);
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate);

            // Валидация формата времени
            foreach (['startTime' => $startTime, 'endTime' => $endTime] as $key => $value) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                    throw new Exception("Поле {$key} должно быть в формате Y-m-d H:i:s.");
                }
            }

            // Извлечение времени из $startTime и $endTime
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime)->format('H:i:s');
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime)->format('H:i:s');

            // Установка $startDateTime и $endDateTime
            $this->startDateTime = $startDate->copy()->setTimeFromTimeString($startTime);
            $this->endDateTime = $endDate->copy()->setTimeFromTimeString($endTime);
        } catch (Exception $e) {
            throw new Exception('Ошибка при обработке диапазона дат и времени: ' . $e->getMessage());
        }
    }

    public function getStartDateTime(): Carbon
    {
        return $this->startDateTime;
    }

    public function getEndDateTime(): Carbon
    {
        return $this->endDateTime;
    }

    /**
     * Возвращает строку формата "за период с ... по ...".
     *
     * @return string
     */
    public function getFormattedPeriodForReport(): string
    {
        return sprintf(
            'Период отчета с %s по %s',
            $this->startDateTime->format('d.m.Y H:i'),
            $this->endDateTime->format('d.m.Y H:i')
        );
    }
}
