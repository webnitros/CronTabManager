<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 15.05.2022
 * Time: 11:10
 */

namespace Webnitros\CronTabManager\AutoPause;


use Carbon\Carbon;
use CronTabManagerAutoPause;
use CronTabManagerTask;

class Builder
{
    /* @var CronTabManagerTask $task */
    private $task;
    /* @var Carbon $Current */
    private $Current;

    private $timezone = 'Europe/Moscow';

    public function __construct(CronTabManagerTask $Task)
    {
        $this->task = $Task;

        $this->Current = Carbon::now();
        $this->Current->setTimezone('4');

    }

    /**
     * Вернет обьект  с паузой
     * @return CronTabManagerAutoPause|null
     */
    public function getAutoPause()
    {
        /* @var CronTabManagerAutoPause[] $AutoPauses */
        $AutoPauses = $this->task->getMany('AutoPause');
        if (count($AutoPauses) > 0) {
            foreach ($AutoPauses as $item) {
                if ($this->compore($item)) {
                    return $item;
                }
            }
        }
        return null;
    }

    public function dateTime(string $time, int $week)
    {
        // Создаем вчерашнюю дату
        $yesterday = Carbon::yesterday($this->timezone); // Вчера
        $Carbon = $yesterday->next($week);
        list($hour, $minutes) = explode(':', $time);
        $Carbon->setTime($hour, $minutes);
        return $Carbon;
    }

    public function compore(CronTabManagerAutoPause $Pause)
    {
        $when = $Pause->get('when');

        /*
            every_day с 10:30 до 12:30
            weekdays - в будни дни
            weekends - в выходные
            monday
            tuesday
            Wednesday
            Thursday
            Friday
            Saturday
            Sunday
        */

        $weeksMap = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
        ];


        $HitDate = false;
        $week = null;
        switch ($when) {
            case 'every_day': // Каждый день
                $week = $this->Current->dayOfWeek;
                break;
            case 'weekdays': // По будням
                if ($this->Current->isWeekday()) {
                    $week = $this->Current->dayOfWeek;
                }
                break;
            case 'weekends': //  Если выходной
                if ($this->Current->isWeekend()) {
                    $week = $this->Current->dayOfWeek;
                }
                break;
            case 'monday':
            case 'tuesday':
            case 'wednesday':
            case 'thursday':
            case 'friday':
            case 'saturday':
            case 'sunday':
                $week = $weeksMap[$when];
                break;
            default:
                break;
        }

        if (!$HitDate && is_numeric($week)) {
            $From = $this->dateTime($Pause->get('from'), $week);
            $To = $this->dateTime($Pause->get('to'), $week);
            $HitDate = $this->current()->between($From, $To);
        }
        return $HitDate;
    }

    public function current()
    {
        return $this->Current;
    }
}
