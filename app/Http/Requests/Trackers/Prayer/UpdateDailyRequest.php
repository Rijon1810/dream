<?php

namespace App\Http\Requests\Trackers\Prayer;

use App\Models\PrayerOfferingOption;
use App\Models\PrayerVariation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyRequest extends FormRequest
{
  protected $stopOnFirstFailure = true;
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
   */
  public function rules(): array
  {
    return [
      'date' => ['date_format:Y-m-d', 'after_or_equal:' . config('trackers.prayer.first_date_in_history'), 'before_or_equal:today'],
      'prayer_tracker.*' => [
        'required',
        'integer',
        'min:0'
      ],
      'prayer_tracker' => [
        'required',
        'array',
        'min:1',
        function ($attribute, $value, $fail) {
          foreach ($value as $prayerVariationId => $prayerOfferingOptionId) {
            if (PrayerVariation::where('id', $prayerVariationId)->count() != 1) {
              return $fail('Invalid prayer variation');
            }

            if ($prayerVariationId != '11'
              && PrayerOfferingOption
                  ::where('id', $prayerOfferingOptionId)
                  ->whereJsonContains('applicable_genders', auth()->user()->pseudoName->gender)
                  ->where('prayer_type_id', PrayerVariation::where('id', $prayerVariationId)->value('prayer_type_id'))
                  ->count() != 1
            ) {
              return $fail('Invalid prayer offering option');
            }
          }
        }
      ]
    ];
  }

  protected function prepareForValidation(): void
  {
    $this->merge([
      'date' => request('date'),
    ]);
  }
}
