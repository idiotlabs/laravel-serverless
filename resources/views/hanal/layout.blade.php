<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title')</title>
<meta name="description" content="@yield('desc')">
<link rel="canonical" href="@yield('canonical', 'https://idiotlabs.kr/hanal')">
<meta property="og:type" content="@yield('ogtype', 'website')">
<meta property="og:title" content="@yield('title')">
<meta property="og:description" content="@yield('desc')">
<meta property="og:url" content="@yield('canonical', 'https://idiotlabs.kr/hanal')">
<meta property="og:site_name" content="한알">
<meta property="og:locale" content="ko_KR">
<meta name="twitter:card" content="summary">
@yield('head')
<style>
/* 라이트 고정 — 이 포트폴리오의 테마 규칙(테마 토글 없음). */
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --ink:#22201d; --ink-2:#5b544c; --ink-3:#8b8279;
  --bg:#fbf9f5; --card:#fff; --line:#eae4da; --accent:#7a6a52;
}
html{-webkit-text-size-adjust:100%}
body{
  background:var(--bg); color:var(--ink);
  font-family:-apple-system,BlinkMacSystemFont,"Apple SD Gothic Neo","Pretendard","Noto Sans KR",
    "Malgun Gothic",sans-serif;
  line-height:1.75; letter-spacing:-.01em;
  padding:0 20px; word-break:keep-all; overflow-wrap:break-word;
}
.wrap{max-width:640px;margin:0 auto;padding:48px 0 72px}
a{color:var(--accent)}
header.top{display:flex;align-items:baseline;gap:10px;margin-bottom:40px}
header.top .logo{font-size:20px;font-weight:700;letter-spacing:-.03em;color:var(--ink);text-decoration:none}
header.top .tag{font-size:13px;color:var(--ink-3)}
.lede{font-size:15px;color:var(--ink-2);margin-bottom:32px}
.card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:28px 24px;margin-bottom:28px}
.ctx{font-size:14px;color:var(--ink-3);margin-bottom:14px}
.card h1,.card h2{font-size:22px;font-weight:700;letter-spacing:-.03em;line-height:1.45;margin-bottom:18px}
blockquote{border-left:3px solid var(--line);padding-left:16px;margin:0 0 14px;font-size:17px;line-height:1.85}
.ref{font-size:13px;color:var(--ink-3);margin-bottom:22px}
.body p{margin-bottom:14px;font-size:15.5px;color:var(--ink-2)}
.prayer{margin-top:22px;padding-top:20px;border-top:1px solid var(--line);font-size:15px;color:var(--ink-2)}
.prayer b{display:block;font-size:12px;letter-spacing:.06em;color:var(--ink-3);margin-bottom:8px;font-weight:600}
.tags{margin-top:20px;font-size:12.5px;color:var(--ink-3)}
.cta{display:block;text-align:center;background:var(--ink);color:#fff;text-decoration:none;
  padding:15px;border-radius:11px;font-size:15px;font-weight:600;margin:8px 0 12px}
.cta-note{text-align:center;font-size:12.5px;color:var(--ink-3);margin-bottom:36px}
h2.sec{font-size:13px;letter-spacing:.06em;color:var(--ink-3);font-weight:600;margin:0 0 12px}
ul.days{list-style:none}
ul.days li{border-bottom:1px solid var(--line)}
ul.days li:last-child{border-bottom:0}
ul.days a{display:flex;gap:12px;align-items:baseline;padding:12px 2px;text-decoration:none;color:var(--ink)}
ul.days a:hover{color:var(--accent)}
ul.days time{font-size:12.5px;color:var(--ink-3);flex:0 0 74px;font-variant-numeric:tabular-nums}
ul.days .t{font-size:14.5px;flex:1}
ul.days .r{font-size:12px;color:var(--ink-3)}
nav.pager{display:flex;justify-content:space-between;gap:12px;margin-bottom:32px;font-size:14px}
nav.pager a{text-decoration:none}
nav.pager span{color:var(--ink-3)}
footer{margin-top:56px;padding-top:24px;border-top:1px solid var(--line);font-size:12.5px;color:var(--ink-3)}
footer a{color:var(--ink-3)}
footer p{margin-bottom:8px}
@media (max-width:480px){
  .wrap{padding:32px 0 56px}
  .card{padding:24px 18px}
  .card h1,.card h2{font-size:20px}
  ul.days time{flex-basis:64px}
}
</style>
</head>
<body>
<div class="wrap">
  <header class="top">
    <a class="logo" href="/hanal">한알</a>
    <span class="tag">오늘 같은 날, 이 말씀</span>
  </header>

  @yield('content')

  <footer>
    <p>한알은 매일 성경 한 구절과 짧은 묵상을 건네는 앱입니다. 개역한글을 씁니다.</p>
    <p>
      <a href="/hanal/privacy">개인정보처리방침</a> ·
      <a href="{{ $play }}">Google Play</a> ·
      <a href="https://idiotlabs.kr">idiotlabs.kr</a>
    </p>
  </footer>
</div>
</body>
</html>
