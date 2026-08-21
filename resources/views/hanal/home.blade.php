{{--
  /hanal 허브.

  ⛔ 카피 규칙(D038): 발화자는 편집자다. «건넨다/골랐다» 만 쓰고 «하나님이 주시는» 계시
  어조를 쓰지 않는다. 「레마」·「뉴스」 를 쓰지 않는다. 개인화(«내 감정에 딱 맞는»)를
  약속하지 않고 day-level(«오늘 같은 날») 로만 말한다 — 그게 앱이 실제로 하는 일이다.
--}}
@extends('hanal.layout')

@section('title', '한알 — 오늘 같은 날, 이 말씀')
@section('desc', '매일 성경 한 구절과 짧은 묵상을 건넵니다. 오늘이라는 날에 어울리는 말씀 하나, 그리고 왜 오늘 그 구절인지 한 줄.')
@section('canonical', 'https://idiotlabs.kr/hanal')

@section('content')

<p class="lede">
  매일 성경 한 구절을 골라 짧은 묵상과 함께 건넵니다.<br>
  오늘이라는 날에 어울리는 한 구절, 그리고 왜 오늘 그 구절인지 한 줄을 같이 적습니다.
</p>

@if ($m)
  <article class="card">
    <p class="ctx">{{ $m->context_line }}</p>
    <h1><a href="/hanal/verse/{{ $m->date->toDateString() }}" style="color:inherit;text-decoration:none">{{ $m->title }}</a></h1>
    <blockquote>{{ $m->verse_text }}</blockquote>
    <p class="ref">{{ $m->verse_ref }} · 개역한글 · <time datetime="{{ $m->date->toDateString() }}">{{ $m->date->toDateString() }}</time></p>
    <div class="body">
      @foreach ((array) $m->body as $p)
        <p>{{ $p }}</p>
      @endforeach
    </div>
    @if ($m->prayer)
      <div class="prayer"><b>기도</b>{{ $m->prayer }}</div>
    @endif
  </article>
@else
  {{-- 코퍼스가 비어 있는 경우. 조용히 빈 페이지를 주지 않는다 — 무엇이 없는지 말한다. --}}
  <article class="card">
    <p class="ctx">오늘의 말씀을 아직 준비하지 못했습니다.</p>
    <p style="font-size:15px;color:var(--ink-2)">앱에서는 준비된 말씀으로 대신 보여드립니다.</p>
  </article>
@endif

<a class="cta" href="{{ $play }}">Google Play에서 한알 받기</a>
<p class="cta-note">무료 · 광고 없음 · 로그인 없음</p>

@if (count($archive))
  <h2 class="sec">지난 말씀</h2>
  <ul class="days">
    @foreach ($archive as $d)
      <li>
        <a href="/hanal/verse/{{ $d->date->toDateString() }}">
          <time datetime="{{ $d->date->toDateString() }}">{{ $d->date->toDateString() }}</time>
          <span class="t">{{ $d->title }}</span>
          <span class="r">{{ $d->verse_ref }}</span>
        </a>
      </li>
    @endforeach
  </ul>
@endif

@endsection
