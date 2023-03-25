@php
  use Carbon\Carbon;
@endphp

<x-layout>
  <div class="prayer-leaderboard-container">
    <div class="row flex-column flex-md-row align-items-center my-3">
      <div class="col-12 col-md-8 mb-2 mb-md-0">
        <h5 class="page-title text-uppercase fw-semibold mb-0 text-center text-md-start">
          Salah Leaderboard
          <span data-bs-toggle="tooltip" data-bs-placement="top" title="Based on the statistics from {{ Carbon::parse($startDate)->toFormattedDateString() }} to {{ Carbon::parse($endDate)->toFormattedDateString() }} prioritizing fard success rate, sunnah success rate, other rak'ats count and subscription duration in sequence" class="cursor-pointer"><i class="bi bi-info-circle-fill ms-2"></i></span>
        </h5>
      </div>
      <div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-end">
        <input type="text" class="form-control w-50 text-center text-md-start" value="{{ auth()->user()->pseudoName->gender }} only" disabled>
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        @forelse ($leaders as $key => $leader)
          @if ($key != 0)
            <hr>
          @endif

          <div class="row align-items-end">
            <div class="col-12 mb-2 mb-sm-0 col-sm-2 text-center d-flex justify-content-center justify-content-sm-start">
              <div class="position-relative">
                <img class="rounded-circle {{ $leader->user_id == auth()->id() ? 'border border-2 border-primary' : '' }}" width="64" src="{{ asset('storage/images/' . strtolower($leader->gender) . '.png') }}" alt="Header Avatar">
                <span class="serial-badge badge rounded-pill bg-primary position-absolute">{{ $key + 1 }}</span>
              </div>
            </div>
            <div class="col-12 mb-2 mb-sm-0 col-sm-4">
              <h6>Fard Success Rate:</h6>
              <div class="progress">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $leader->fard_success_rate }}%;">{{ $leader->fard_success_rate }}%</div>
              </div>
            </div>
            <div class="col-12 mb-2 mb-sm-0 col-sm-4">
              <h6>Sunnah Success Rate:</h6>
              <div class="progress">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $leader->sunnah_success_rate }}%;">{{ $leader->sunnah_success_rate }}%</div>
              </div>
            </div>
            <div class="col-12 mb-2 mb-sm-0 col-sm-2">
              <span class="fw-medium">Others:</span>
              <span>{{ $leader->others_rakats_count ?? 0 }} rak'ats</span>
            </div>
          </div>
        @empty
          <p class="text-center mb-0">No leaders found</p>
        @endforelse
      </div>
    </div>
  </div>
</x-layout>


