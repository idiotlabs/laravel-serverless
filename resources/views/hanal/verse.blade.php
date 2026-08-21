{{--
  /hanal/verse/{date} — 그날의 말씀. 앱 공유 링크의 착지점.
  카피 규칙은 home.blade.php 주석과 동일(D038).
--}}
@extends('hanal.layout')

@section('title', $m->title . ' — ' . $m->verse_ref . ' | 한알')
@section('desc', $m->context_line . ' ' . mb_substr($m->verse_text, 0, 80))
@section('canonical', 'https://idiotlabs.kr/hanal/verse/' . $m->date->toDateString())
@section('ogtype', 'article')

@section('head')
<meta property="article:published_time" content="{{ $m->date->toDateString() }}">
@endsection

@section('content')

<article class="card">
  <p class="ctx">{{ $m->context_line }}</p>
  <h1>{{ $m->title }}</h1>
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

<nav class="pager">
  @if ($prev)
    <a href="/hanal/verse/{{ $prev->date->toDateString() }}">← {{ $prev->date->toDateString() }}</a>
  @else
    <span></span>
  @endif
  @if ($next)
    <a href="/hanal/verse/{{ $next->date->toDateString() }}">{{ $next->date->toDateString() }} →</a>
  @else
    <span></span>
  @endif
</nav>

<a class="cta" href="{{ $play }}">매일 받아보기 — Google Play</a>
<p class="cta-note">무료 · 광고 없음 · 로그인 없음</p>

@if (count($archive))
  <h2 class="sec">다른 날의 말씀</h2>
  <ul class="days">
    @foreach ($archive->take(20) as $d)
      <li>
        <a href="/hanal/verse/{{ $d->date->toDateString() }}">
          <time datetime="{{ $d->date->toDateString() }}">{{ $d->date->toDateString() }}</time>
          <span class="t">{{ $d->title }}</span>
          <span class="r">{{ $d->verse_ref }}</span>
        </a>
      </li>
    @endforeach
  </ul>
  <p style="margin-top:16px;font-size:14px"><a href="/hanal">전체 보기 →</a></p>
@endif

@endsection
