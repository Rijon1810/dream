<?php
namespace App\Actions\Trackers\Prayer;

use App\Models\PrayerTracker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GetLeaders
{
  protected const MAX_DAYS = 30;
  protected const FARD_PRAYER_ID = 1;
  protected const SUNNAH_PRAYER_ID = 2;
  protected const TOTAL_PRAYER_TIMES = 5;
  protected const MAX_PRAYER_POINTS = [
    'FARD' => [
      'MALE' => 1000,
      'FEMALE' => 700,
    ],
    'SUNNAH' => [
      'MALE' => 10,
      'FEMALE' => 10,
    ]
  ];


  private function getIntervalDates() {
    $endDate = Carbon::yesterday();

    $trackerStartDate = Carbon::parse(config('trackers.prayer.first_date_in_history'));
    $startDate = Carbon::today()->subtract(self::MAX_DAYS, 'day');

    if($trackerStartDate->gt($startDate)) {
      $startDate = $trackerStartDate;
    }

    return [$startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $startDate->diffInDays($endDate) + 1];
  }

  private function getTotalMaxPrayerPoints($dateDiff) {
    return [
      'FARD' => [
        'MALE' => self::MAX_PRAYER_POINTS['FARD']['MALE'] * $dateDiff * self::TOTAL_PRAYER_TIMES,
        'FEMALE' => self::MAX_PRAYER_POINTS['FARD']['FEMALE'] * $dateDiff * self::TOTAL_PRAYER_TIMES,
      ],
      'SUNNAH' => [
        'MALE' => self::MAX_PRAYER_POINTS['SUNNAH']['MALE'] * $dateDiff * self::TOTAL_PRAYER_TIMES,
        'FEMALE' => self::MAX_PRAYER_POINTS['SUNNAH']['FEMALE'] * $dateDiff * self::TOTAL_PRAYER_TIMES,
      ],
    ];
  }

  public function execute() {
    [$startDate, $endDate, $dateDiff] = $this->getIntervalDates();
    $totalMaxPrayerPoints = $this->getTotalMaxPrayerPoints($dateDiff);

    DB::statement("SET SQL_MODE=''");

    return PrayerTracker::query()
      ->leftJoin('prayer_offering_options', 'prayer_trackers.prayer_offering_option_id', '=', 'prayer_offering_options.id')
      ->join('users', 'prayer_trackers.user_id', '=', 'users.id')
      ->join('pseudo_names', 'users.pseudo_name_id', '=', 'pseudo_names.id')
      ->select(
        'prayer_trackers.user_id',
        'pseudo_names.gender',
        DB::raw('ROUND((SUM(case when prayer_offering_options.prayer_type_id = ' . self::FARD_PRAYER_ID . ' and instr(prayer_offering_options.special_genders, pseudo_names.gender) > 0 then prayer_offering_options.special_points when prayer_offering_options.prayer_type_id = ' . self::FARD_PRAYER_ID . ' then prayer_offering_options.points else 0 end) / (case when pseudo_names.gender = \'Male\' then ' . $totalMaxPrayerPoints['FARD']['MALE'] . ' else ' . $totalMaxPrayerPoints['FARD']['FEMALE'] . ' end) * 100), 2) as fard_success_rate'),
        DB::raw('ROUND((SUM(case when prayer_offering_options.prayer_type_id = ' . self::SUNNAH_PRAYER_ID . ' and instr(prayer_offering_options.special_genders, pseudo_names.gender) > 0 then prayer_offering_options.special_points when prayer_offering_options.prayer_type_id = ' . self::SUNNAH_PRAYER_ID . ' then prayer_offering_options.points else 0 end) / (case when pseudo_names.gender = \'Male\' then ' . $totalMaxPrayerPoints['SUNNAH']['MALE'] . ' else ' . $totalMaxPrayerPoints['SUNNAH']['FEMALE'] . ' end) * 100), 2) as sunnah_success_rate'),
        DB::raw('SUM(prayer_trackers.rakats_cnt) as others_rakats_count')
      )
      ->whereBetween('prayer_trackers.date', [$startDate, $endDate])
      ->groupBy('prayer_trackers.user_id')
      ->orderBy('fard_success_rate', 'DESC')
      ->orderBy('sunnah_success_rate', 'DESC')
      ->orderBy('others_rakats_count', 'DESC')
      ->get();
  }
}
