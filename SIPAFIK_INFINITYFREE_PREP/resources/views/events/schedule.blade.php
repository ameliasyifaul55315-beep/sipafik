@extends('layouts.dashboard')

@section('title', 'Jadwal Kegiatan')
@section('eyebrow', 'KEGIATAN KAMPUS')
@section('kicker', 'AGENDA')
@section('heading', 'Jadwal Kegiatan')
@section('subheading', 'Urutan kegiatan yang akan berlangsung, dikelompokkan per tanggal.')

@section('content')
    @if($grouped->isEmpty())
        <div class="mod-panel"><div class="mod-empty"><strong>Belum ada jadwal</strong>Belum ada kegiatan mendatang yang dapat ditampilkan.</div></div>
    @else
        <div class="mod-timeline">
            @foreach($grouped as $date => $events)
                @php $d = \Illuminate\Support\Carbon::parse($date); @endphp
                <div class="mod-day">
                    <div class="date"><b>{{ $d->format('d') }}</b><span>{{ $d->translatedFormat('F Y') }}<br>{{ $d->translatedFormat('l') }}</span></div>
                    <div class="items">
                        @foreach($events as $event)
                            <div class="mod-item">
                                <div>
                                    <strong>{{ $event->title }}</strong>
                                    <small>{{ substr($event->start_time, 0, 5) }}–{{ substr($event->end_time, 0, 5) }} · {{ $event->location }} · {{ $event->campus?->name }}</small>
                                </div>
                                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                                    @if(in_array($event->id, $myRegistrations, true))<span class="mod-badge registered">Terdaftar</span>@endif
                                    <span class="mod-badge {{ $event->status }}">{{ $event->status_label }}</span>
                                    <a class="mod-btn ghost sm" href="{{ route('campus.events.show', $event->id) }}">Detail</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
