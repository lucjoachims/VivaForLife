<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>À table pour Viva for Life · Réservation du repas</title>
<meta name="description" content="Réserve ton repas pour la soirée caritative au profit de Viva for Life : pâtes bolognaise, 4 fromages ou carbonara, sur place ou à emporter.">
<link rel="icon" href="logo-soiree.png" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<style>
  /* ====================  TOKENS  ==================== */
  :root{
    --cream:#FFF7EC;       /* fond de page */
    --paper:#FFFFFF;       /* cartes */
    --sand:#F1E3CB;        /* lignes / séparations */
    --navy:#1E2A5A;        /* texte principal, bandeaux */
    --navy-2:#141D42;
    --orange:#F26B1D;      /* action principale */
    --orange-2:#D95A12;
    --red:#E3431C;
    --gold:#F7B733;
    --green:#2E9E6B;
    --muted:#6B6F86;       /* texte secondaire */
    --r:20px;
    --shadow:0 12px 30px -18px rgba(30,42,90,.35);
  }
  *{box-sizing:border-box}
  html{scroll-behavior:smooth}
  body{
    margin:0; background:var(--cream); color:var(--navy);
    font-family:"Nunito",system-ui,sans-serif; font-size:17px; line-height:1.55;
    -webkit-font-smoothing:antialiased; overflow-x:hidden;
  }
  h1,h2,h3,h4{font-family:"Fredoka",sans-serif; font-weight:700; line-height:1.1; margin:0; letter-spacing:-.01em}
  a{color:inherit}
  p{margin:0 0 1em}
  .wrap{max-width:1100px; margin:0 auto; padding:0 20px}
  .eyebrow{display:inline-block; font-weight:800; font-size:.78rem; letter-spacing:.14em; text-transform:uppercase; color:var(--orange); margin-bottom:10px}
  button{font-family:inherit; cursor:pointer; border:none; color:inherit}
  .btn{
    display:inline-flex; align-items:center; gap:.5em; justify-content:center;
    background:var(--orange); color:#fff; font-weight:800; font-size:1rem;
    padding:.85em 1.5em; border-radius:999px; text-decoration:none;
    box-shadow:0 8px 20px -10px rgba(242,107,29,.8); transition:transform .12s ease, background .15s ease;
  }
  .btn:hover{transform:translateY(-2px); background:var(--orange-2)}
  .btn:active{transform:translateY(0)}
  .btn.ghost{background:transparent; color:var(--navy); box-shadow:inset 0 0 0 2px var(--navy)}
  .btn.ghost:hover{background:rgba(30,42,90,.06)}
  .btn.navy{background:var(--navy); box-shadow:0 8px 20px -10px rgba(30,42,90,.8)}
  .btn.navy:hover{background:var(--navy-2)}
  .btn:disabled{opacity:.45; cursor:not-allowed; transform:none}
  .btn.sm{font-size:.85rem; padding:.55em 1.1em}
  :focus-visible{outline:3px solid var(--gold); outline-offset:2px; border-radius:8px}
  .card{background:var(--paper); border:1px solid var(--sand); border-radius:var(--r); box-shadow:var(--shadow)}
  .sr{position:absolute; left:-9999px}

  /* ====================  TOPBAR  ==================== */
  .topbar{position:sticky; top:0; z-index:30; background:rgba(255,247,236,.92); backdrop-filter:blur(8px); border-bottom:1px solid var(--sand)}
  .topbar .wrap{display:flex; align-items:center; justify-content:space-between; gap:14px; height:66px}
  .brand{display:flex; align-items:center; gap:10px; font-family:"Fredoka"; font-weight:700; font-size:1.05rem; text-decoration:none}
  .brand img{height:42px; width:auto}
  .nav{display:flex; gap:22px; font-weight:700; font-size:.95rem}
  .nav a{text-decoration:none; opacity:.8}
  .nav a:hover{opacity:1; color:var(--orange)}

  /* ====================  HERO  ==================== */
  .hero{padding:56px 0 40px; position:relative; overflow:hidden}
  .hero::before{content:""; position:absolute; right:-140px; top:-120px; width:520px; height:520px; border-radius:50%;
    background:radial-gradient(circle, rgba(247,183,51,.35), rgba(242,107,29,.12) 55%, transparent 72%); pointer-events:none}
  .hero-grid{display:grid; grid-template-columns:1.1fr .9fr; gap:40px; align-items:center}
  .hero h1{font-size:clamp(2.3rem,5.4vw,3.9rem)}
  .hero h1 em{font-style:normal; color:var(--orange)}
  .hero .lead{font-size:1.15rem; color:#3E4667; margin:18px 0 0; max-width:46ch}
  .meta-row{display:flex; flex-wrap:wrap; gap:10px; margin-top:22px}
  .chip{display:inline-flex; align-items:center; gap:.5em; background:var(--paper); border:1px solid var(--sand);
    padding:.5em .95em; border-radius:999px; font-size:.92rem; font-weight:700}
  .hero-cta{display:flex; gap:12px; flex-wrap:wrap; margin-top:28px}
  .hero-logo{display:flex; justify-content:center; position:relative; z-index:1}
  .hero-logo img{width:100%; max-width:400px; height:auto; filter:drop-shadow(0 18px 30px rgba(30,42,90,.25)); animation:float 6s ease-in-out infinite}
  @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}

  /* steps strip */
  .steps{display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:44px}
  .step{display:flex; gap:12px; align-items:flex-start; background:var(--paper); border:1px solid var(--sand); border-radius:16px; padding:14px 16px}
  .step .n{flex:none; width:32px; height:32px; border-radius:50%; background:var(--navy); color:#fff; display:grid; place-items:center; font-family:"Fredoka"; font-weight:700}
  .step b{display:block; font-size:.98rem}
  .step span{font-size:.85rem; color:var(--muted)}

  /* ====================  SECTIONS  ==================== */
  section{padding:60px 0}
  .sec-head{margin-bottom:28px; max-width:60ch}
  .sec-head h2{font-size:clamp(1.8rem,4vw,2.5rem); margin-bottom:8px}
  .sec-head p{color:var(--muted); margin:0}
  .band{background:var(--paper); border-top:1px solid var(--sand); border-bottom:1px solid var(--sand)}
  .band .card{box-shadow:none; background:var(--cream)}

  /* ====================  MENU  ==================== */
  .dishes{display:grid; grid-template-columns:repeat(3,1fr); gap:18px}
  .dish{padding:24px 22px; display:flex; flex-direction:column; gap:8px; position:relative; overflow:hidden}
  .dish .emoji{font-size:2.6rem; line-height:1}
  .dish h3{font-size:1.35rem}
  .dish p{color:var(--muted); font-size:.95rem; margin:0 0 6px; flex:1}
  .prices{display:flex; gap:10px; flex-wrap:wrap}
  .pr{flex:1; min-width:110px; background:var(--cream); border-radius:14px; padding:10px 12px; border:1px solid var(--sand)}
  .pr small{display:block; font-size:.75rem; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); font-weight:800}
  .pr b{font-family:"Fredoka"; font-size:1.45rem; font-weight:700}
  .pr.child{background:#FFF1E6; border-color:#FAD3B6}
  .included{margin-top:22px; display:flex; align-items:center; gap:16px; padding:18px 22px; border-radius:var(--r);
    background:var(--navy); color:#fff}
  .included .ico{font-size:2rem; flex:none}
  .included b{font-family:"Fredoka"; font-size:1.1rem; display:block}
  .included span{color:#D7DCF0; font-size:.95rem}
  .avail-row{display:flex; gap:12px; flex-wrap:wrap; margin-top:18px}
  .avail{display:inline-flex; align-items:center; gap:.6em; padding:.55em 1em; border-radius:999px; background:var(--paper); border:1px solid var(--sand); font-weight:700; font-size:.92rem}
  .avail i{width:10px; height:10px; border-radius:50%; background:var(--green); flex:none}
  .avail.low i{background:var(--gold)} .avail.full i{background:var(--red)}

  /* ====================  RÉSERVATION  ==================== */
  .book-grid{display:grid; grid-template-columns:1.15fr .85fr; gap:26px; align-items:start}
  .stepbox{padding:24px; margin-bottom:18px}
  .stepbox h3{display:flex; align-items:center; gap:10px; font-size:1.2rem; margin-bottom:14px}
  .stepbox h3 .n{width:30px; height:30px; border-radius:50%; background:var(--orange); color:#fff; display:grid; place-items:center; font-size:.95rem; flex:none}
  .modes{display:grid; grid-template-columns:1fr 1fr; gap:12px}
  .mode{position:relative; display:block; cursor:pointer; padding:16px 16px 14px; border-radius:16px; border:2px solid var(--sand); background:var(--cream); transition:border-color .15s, background .15s}
  .mode input{position:absolute; opacity:0; inset:0; cursor:pointer}
  .mode .ico{font-size:1.7rem}
  .mode b{display:block; font-family:"Fredoka"; font-size:1.1rem; margin-top:4px}
  .mode span{display:block; font-size:.85rem; color:var(--muted); margin-top:2px}
  .mode.on{border-color:var(--orange); background:#FFF1E6}
  .mode.off{opacity:.55; cursor:not-allowed}
  .mode .tick{position:absolute; top:12px; right:12px; width:22px; height:22px; border-radius:50%; border:2px solid var(--sand); background:#fff}
  .mode.on .tick{border-color:var(--orange); background:var(--orange); box-shadow:inset 0 0 0 4px #fff}
  .take-note{margin:12px 0 0; font-size:.9rem; color:var(--muted); background:var(--cream); border-radius:12px; padding:10px 14px; border:1px dashed var(--sand)}

  .qty-table{display:flex; flex-direction:column; gap:10px}
  .qrow{display:grid; grid-template-columns:1.2fr 1fr 1fr; gap:10px; align-items:center; background:var(--cream); border:1px solid var(--sand); border-radius:14px; padding:12px 14px}
  .qrow .dn{font-weight:800; display:flex; align-items:center; gap:8px}
  .qrow .dn .e{font-size:1.4rem}
  .qcell{display:flex; align-items:center; justify-content:space-between; gap:8px; background:#fff; border-radius:12px; padding:6px 8px 6px 10px; border:1px solid var(--sand)}
  .qcell .lab{font-size:.78rem; line-height:1.15}
  .qcell .lab b{display:block; font-size:.82rem}
  .qcell .lab span{color:var(--muted)}
  .stepper{display:flex; align-items:center; flex:none}
  .stepper button{width:30px; height:30px; border-radius:9px; background:var(--navy); color:#fff; font-size:1.1rem; font-weight:800; line-height:1}
  .stepper button:disabled{opacity:.25; cursor:not-allowed}
  .stepper input{width:36px; text-align:center; background:transparent; border:none; font-family:"Fredoka"; font-size:1.05rem; font-weight:700; color:var(--navy); -moz-appearance:textfield}
  .stepper input::-webkit-outer-spin-button,.stepper input::-webkit-inner-spin-button{-webkit-appearance:none}
  .qhead{display:grid; grid-template-columns:1.2fr 1fr 1fr; gap:10px; padding:0 14px; font-size:.75rem; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); font-weight:800}
  .qhead span:nth-child(n+2){text-align:center}

  .field{margin-bottom:14px}
  .field label{display:block; font-size:.88rem; margin-bottom:6px; font-weight:800}
  .field input,.field textarea,.field select{
    width:100%; background:#fff; border:1.5px solid var(--sand); color:var(--navy);
    padding:.7em .85em; border-radius:12px; font-family:inherit; font-size:1rem}
  .field input:focus,.field textarea:focus{border-color:var(--orange); outline:none}
  .field input::placeholder,.field textarea::placeholder{color:#A7AABB}
  .two{display:grid; grid-template-columns:1fr 1fr; gap:14px}
  .err{color:var(--red); font-weight:700; font-size:.9rem; margin-top:8px; min-height:0}

  .book-side{position:sticky; top:84px}
  .summary{background:var(--navy); color:#fff; border-radius:var(--r); padding:24px}
  .summary h3{font-size:1.25rem; margin-bottom:12px; color:var(--gold)}
  .sumrow{display:flex; justify-content:space-between; gap:10px; padding:8px 0; border-bottom:1px dashed rgba(255,255,255,.18); font-size:.95rem}
  .sumrow span:last-child{white-space:nowrap}
  .sumrow.total{border:none; padding-top:14px; font-family:"Fredoka"; font-weight:700; font-size:1.6rem}
  .sumrow.mode{color:#D7DCF0; font-weight:700}
  .summary .hint{font-size:.82rem; color:#C3C9E3; margin:12px 0 0; line-height:1.45}
  .summary .btn{width:100%}
  .summary .btn.ghost{color:#fff; box-shadow:inset 0 0 0 2px rgba(255,255,255,.55)}
  .summary .btn.ghost:hover{background:rgba(255,255,255,.1)}
  .paybtns{display:flex; flex-direction:column; gap:10px; margin-top:16px}
  .phone-help{margin:0 0 18px; font-size:.92rem; color:#3E4667; background:var(--cream); border:1px dashed #E2C89A; border-radius:14px; padding:12px 16px}

  /* résultat */
  .result{padding:32px; text-align:center}
  .result .check{width:68px;height:68px;border-radius:50%;background:var(--green);display:grid;place-items:center;margin:0 auto 14px;font-size:2rem;color:#fff}
  .result h3{font-size:1.6rem}
  .pay-box{background:var(--navy); color:#fff; border-radius:16px; padding:18px 20px; margin:20px 0; text-align:left}
  .pay-box .l{display:block; font-size:.72rem; color:var(--gold); text-transform:uppercase; letter-spacing:.1em; font-weight:800; margin-top:10px}
  .pay-box .v{font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:1.05rem; font-weight:700; word-break:break-all; margin:2px 0 0}
  .copybtn{background:rgba(255,255,255,.14); color:#fff; font-size:.78rem; padding:.3em .7em; border-radius:8px; margin-left:8px}
  .result-lines{display:inline-block; text-align:left; margin:10px auto 0; background:var(--cream); border:1px solid var(--sand); border-radius:14px; padding:12px 18px; font-size:.95rem}

  /* ====================  APRÈS / CAUSE  ==================== */
  .after-grid{display:grid; grid-template-columns:1.1fr .9fr; gap:36px; align-items:center}
  .after h2{font-size:clamp(1.7rem,3.6vw,2.3rem); margin-bottom:12px}
  .after p{color:#3E4667}
  .vfl{display:flex; flex-direction:column; align-items:center; gap:14px; padding:30px; text-align:center}
  .vfl img{width:100%; max-width:300px; height:auto}
  .vfl small{color:var(--muted); font-weight:700; letter-spacing:.1em; text-transform:uppercase; font-size:.75rem}

  footer{padding:36px 0; text-align:center; color:var(--muted); font-size:.88rem; border-top:1px solid var(--sand)}
  #statusBanner > div{position:sticky; top:66px; z-index:25}

  /* ====================  ADMIN  ==================== */
  .admin-link{position:fixed; bottom:14px; right:14px; z-index:40; font-size:.8rem; font-weight:700; color:var(--navy);
    background:#fff; padding:.5em .9em; border-radius:999px; border:1px solid var(--sand); cursor:pointer; box-shadow:var(--shadow)}
  .modal{position:fixed; inset:0; background:rgba(20,29,66,.6); backdrop-filter:blur(3px); z-index:50;
    display:none; align-items:flex-start; justify-content:center; padding:20px; overflow:auto}
  .modal.open{display:flex}
  .panel{background:var(--cream); border-radius:20px; width:min(1040px,100%); padding:24px; margin:auto}
  .panel-head{display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:18px; flex-wrap:wrap}
  .panel-head h3{font-size:1.4rem}
  .stats{display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:18px}
  .stat{background:#fff; border:1px solid var(--sand); border-radius:14px; padding:14px}
  .stat .n{font-family:"Fredoka"; font-weight:700; font-size:1.6rem}
  .stat .k{font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; font-weight:800}
  .gauges{display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:18px}
  .gauge{background:#fff; border:1px solid var(--sand); border-radius:14px; padding:14px}
  .gauge .top{display:flex; justify-content:space-between; font-size:.9rem; margin-bottom:8px; font-weight:700}
  .gauge .bar{height:10px; background:var(--sand); border-radius:999px; overflow:hidden}
  .gauge .bar > i{display:block; height:100%; background:var(--orange)}
  table{width:100%; border-collapse:collapse; font-size:.88rem; background:#fff}
  th,td{text-align:left; padding:10px 8px; border-bottom:1px solid var(--sand); vertical-align:top}
  th{font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); font-weight:800; background:var(--cream); position:sticky; top:0}
  td.num,th.num{text-align:center}
  .pill{display:inline-block; font-size:.72rem; padding:.25em .65em; border-radius:999px; font-weight:800; white-space:nowrap}
  .pill.paid{background:#DDF3E8; color:#1E6E48}
  .pill.pending{background:#FFF0C7; color:#8A5B00}
  .pill.cancel{background:#FDE0DA; color:#9C2A12}
  .pill.dine{background:#E3E7F7; color:var(--navy)} .pill.take{background:#FFE6D3; color:#9A3F05}
  .tbtns button{font-size:.74rem; font-weight:700; padding:.4em .65em; border-radius:8px; margin:2px 4px 2px 0; background:var(--cream); border:1px solid var(--sand)}
  .tbtns button:hover{background:var(--sand)}
  .toolbar{display:flex; gap:10px; flex-wrap:wrap; margin-bottom:14px}
  .toolbar input,.toolbar select{background:#fff; border:1.5px solid var(--sand); color:var(--navy); padding:.55em .75em; border-radius:10px; font-family:inherit; font-size:.95rem}
  .tabs{display:flex; gap:6px; margin-bottom:18px; flex-wrap:wrap}
  .tabs button{padding:.55em 1.1em; border-radius:999px; background:#fff; border:1px solid var(--sand); font-weight:700; font-size:.9rem}
  .tabs button.on{background:var(--navy); color:#fff; border-color:var(--navy)}
  .table-scroll{overflow:auto; max-height:52vh; border:1px solid var(--sand); border-radius:12px; background:#fff}
  .prep-list{columns:2; column-gap:24px; font-size:.92rem; padding-left:18px}
  .prep-list li{break-inside:avoid; margin-bottom:6px}
  .cfg-grid{display:grid; grid-template-columns:1fr 1fr; gap:14px}
  .cfg-grid .field{margin-bottom:0}
  .cfg-grid .full{grid-column:1 / -1}
  hr.sep{border:none; border-top:1px solid var(--sand); margin:20px 0}
  .menu-editor{display:flex; flex-direction:column; gap:10px}
  .mrow{display:grid; grid-template-columns:54px 1.4fr 1.8fr 90px 90px 70px 110px; gap:8px; align-items:center; background:#fff; border:1px solid var(--sand); border-radius:12px; padding:8px}
  .mrow input{width:100%; border:1.5px solid var(--sand); border-radius:8px; padding:.45em .55em; font-family:inherit; font-size:.92rem; color:var(--navy)}
  .mrow input[type=checkbox]{width:auto; margin:0 auto; display:block; transform:scale(1.3)}
  .mrow .acts{display:flex; gap:4px; justify-content:flex-end}
  .mrow .acts button{width:30px; height:30px; border-radius:8px; background:var(--cream); border:1px solid var(--sand); font-weight:800}
  .mrow.inactive{opacity:.55}
  .mhead{display:grid; grid-template-columns:54px 1.4fr 1.8fr 90px 90px 70px 110px; gap:8px; padding:0 8px; font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); font-weight:800}
  .saved{margin-left:12px; color:var(--green); font-weight:800; font-size:.88rem}

  /* ====================  RESPONSIVE  ==================== */
  @media (max-width:900px){
    .hero-grid,.book-grid,.after-grid{grid-template-columns:1fr}
    .hero-logo{order:-1} .hero-logo img{max-width:260px}
    .steps,.dishes{grid-template-columns:1fr}
    .book-side{position:static}
    .stats{grid-template-columns:1fr 1fr} .gauges,.cfg-grid{grid-template-columns:1fr} .cfg-grid .full{grid-column:auto}
    .prep-list{columns:1}
    .nav{display:none}
    .mrow,.mhead{grid-template-columns:44px 1fr 1fr 70px 70px 50px 100px; font-size:.85rem}
    .mhead{display:none}
  }
  @media (max-width:640px){
    body{font-size:16px}
    .qrow{grid-template-columns:1fr; gap:8px}
    .qhead{display:none}
    .modes{grid-template-columns:1fr}
    .two{grid-template-columns:1fr}
    .stepbox,.summary,.result{padding:18px}
    .mrow{grid-template-columns:1fr 1fr; }
    .mrow .acts{grid-column:1 / -1}
  }
  @media (prefers-reduced-motion:reduce){
    *{animation:none!important; transition:none!important; scroll-behavior:auto!important}
  }
</style>
</head>
<body>

<div class="topbar">
  <div class="wrap">
    <a class="brand" href="#top"><img src="logo-soiree.png" alt=""><span id="brandName">À table pour Viva for Life</span></a>
    <nav class="nav">
      <a href="#menu">Le menu</a>
      <a href="#reserver">Réserver</a>
      <a href="#infos">Infos pratiques</a>
    </nav>
    <a href="#reserver" class="btn sm">Réserver mon repas</a>
  </div>
</div>
<div id="statusBanner"></div>

<!-- =========================================================
     HERO
========================================================== -->
<header class="hero" id="top">
  <div class="wrap">
    <div class="hero-grid">
      <div>
        <div class="eyebrow">Soirée caritative · Au profit de <span id="heroCause">Viva for Life</span></div>
        <h1>Un bon plat de pâtes,<br>un <em>grand cœur</em>.</h1>
        <p class="lead">Réserve ton repas à l'avance, sur place ou à emporter. Chaque assiette servie aide <b id="leadCause">Viva for Life</b>. Après le repas, la soirée continue en accès libre !</p>
        <div class="meta-row">
          <span class="chip">📅 <span id="mDate">…</span></span>
          <span class="chip">🕕 <span id="mTime">…</span></span>
          <span class="chip">📍 <span id="mPlace">…</span></span>
        </div>
        <div class="hero-cta">
          <a href="#reserver" class="btn">🍝 Réserver mon repas</a>
          <a href="#menu" class="btn ghost">Voir le menu</a>
        </div>
      </div>
      <div class="hero-logo">
        <img src="logo-soiree.png" alt="À table pour Viva for Life — Soirée repas & DJ caritative">
      </div>
    </div>

    <div class="steps">
      <div class="step"><div class="n">1</div><div><b>Choisis tes plats</b><span>Bolo, 4 fromages ou carbo · adulte ou enfant</span></div></div>
      <div class="step"><div class="n">2</div><div><b>Sur place ou à emporter</b><span>Les places sont limitées dans les deux cas</span></div></div>
      <div class="step"><div class="n">3</div><div><b>Paie et c'est réservé</b><span>Par carte en ligne ou par virement</span></div></div>
    </div>
  </div>
</header>

<!-- =========================================================
     MENU
========================================================== -->
<section id="menu">
  <div class="wrap">
    <div class="sec-head">
      <div class="eyebrow">Le menu</div>
      <h2>Qu'est-ce qu'on mange ?</h2>
      <p>Des pâtes, en version adulte ou enfant (plus petite portion, petit prix).</p>
    </div>
    <div class="dishes" id="dishCards"><!-- injecté --></div>
    <div class="included" id="includedBox" style="display:none">
      <div class="ico">🥂🍮</div>
      <div><b>Inclus pour tout le monde</b><span id="includedText"></span></div>
    </div>
    <div class="avail-row" id="availRow"></div>
  </div>
</section>

<!-- =========================================================
     RÉSERVATION
========================================================== -->
<section class="band" id="reserver">
  <div class="wrap">
    <div class="sec-head">
      <div class="eyebrow">Réservation</div>
      <h2>Je réserve mon repas</h2>
      <p>Trois petites étapes et c'est dans la poche.</p>
    </div>

    <div id="bookingArea">
      <div class="book-grid">
        <div>
          <!-- Étape 1 -->
          <div class="card stepbox">
            <h3><span class="n">1</span> Sur place ou à emporter ?</h3>
            <div class="modes" id="modeBox"><!-- injecté --></div>
            <p class="take-note" id="takeNote" style="display:none"></p>
          </div>

          <!-- Étape 2 -->
          <div class="card stepbox">
            <h3><span class="n">2</span> Combien de plats ?</h3>
            <div class="qhead"><span>Plat</span><span>Adulte</span><span>Enfant</span></div>
            <div class="qty-table" id="qtyTable"><!-- injecté --></div>
          </div>
          <p class="phone-help" id="phoneHelp" style="display:none"></p>
        </div>

        <div class="book-side">
          <!-- Étape 3 -->
          <div class="card stepbox">
            <h3><span class="n">3</span> Tes coordonnées</h3>
            <div class="field">
              <label for="fName">Nom et prénom</label>
              <input id="fName" type="text" placeholder="Ex. Marie Dupont" autocomplete="name">
            </div>
            <div class="two" style="margin-bottom:0">
              <div class="field" style="margin-bottom:0">
                <label for="fEmail">E-mail</label>
                <input id="fEmail" type="email" placeholder="toi@mail.be" autocomplete="email">
              </div>
              <div class="field" style="margin-bottom:0">
                <label for="fPhone">Téléphone</label>
                <input id="fPhone" type="tel" placeholder="04xx xx xx xx" autocomplete="tel">
              </div>
            </div>
            <div class="err" id="formErr"></div>
          </div>

        <div class="summary">
          <h3>Mon récap</h3>
          <div id="sumRows"></div>
          <div class="sumrow total"><span>Total</span><span id="sumTotal">0 €</span></div>
          <div class="paybtns">
            <button class="btn" id="payCard">💳 Payer par carte</button>
            <button class="btn ghost" id="payTransfer">🏦 Réserver et payer par virement</button>
          </div>
          <p class="hint">Par carte : paiement sécurisé (Stripe), réservation confirmée tout de suite.<br>Par virement : tu reçois l'IBAN et une communication structurée par e-mail, confirmée à réception.</p>
        </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =========================================================
     ET APRÈS / LA CAUSE
========================================================== -->
<section id="infos">
  <div class="wrap after">
    <div class="after-grid">
      <div>
        <div class="eyebrow">Et après le repas ?</div>
        <h2>La soirée continue 🎧</h2>
        <p id="afterText"></p>
        <p id="placeLine"></p>
        <p id="phoneHelp2" class="phone-help" style="display:none; margin-top:18px"></p>
      </div>
      <div class="card vfl">
        <img src="VFL_logo.png" alt="Viva for Life">
        <small>Tous les bénéfices sont reversés à <span id="vflCause">Viva for Life</span></small>
      </div>
    </div>
  </div>
</section>

<footer>
  <div class="wrap">
    <b id="footName">À table pour Viva for Life</b> · Réservation et paiement anticipés · Merci pour votre soutien 🧡
  </div>
</footer>

<!-- =========================================================
     ADMIN
========================================================== -->
<div class="admin-link" id="adminOpen">⚙ Espace organisateur</div>

<div class="modal" id="adminModal">
  <div class="panel" id="adminPanel">
    <!-- gate -->
    <div id="adminGate">
      <div class="panel-head">
        <h3>Espace organisateur</h3>
        <button class="btn ghost sm" data-close>Fermer</button>
      </div>
      <p style="color:var(--muted)">Entre le code d'accès pour gérer les réservations, la cuisine, le menu et les réglages.</p>
      <div class="field" style="max-width:280px">
        <input id="adminPass" type="password" placeholder="Code d'accès" autocomplete="current-password">
      </div>
      <button class="btn" id="adminLogin">Entrer</button>
      <div class="err" id="adminErr"></div>
    </div>

    <!-- dashboard -->
    <div id="adminDash" style="display:none">
      <div class="panel-head">
        <h3>Tableau de bord</h3>
        <div style="display:flex; gap:8px">
          <button class="btn ghost sm" id="refreshBtn">↻ Rafraîchir</button>
          <button class="btn ghost sm" data-close>Fermer</button>
        </div>
      </div>

      <div class="tabs">
        <button class="on" data-tab="resa">Réservations</button>
        <button data-tab="encoder">＋ Encoder une résa</button>
        <button data-tab="cuisine">Cuisine</button>
        <button data-tab="menu">Menu & prix</button>
        <button data-tab="reglages">Réglages</button>
      </div>

      <!-- TAB réservations -->
      <div data-pane="resa">
        <div class="stats" id="statBoxes"></div>
        <div class="gauges" id="gaugeBoxes"></div>
        <div class="toolbar">
          <input id="search" placeholder="🔎 Nom, e-mail, réf…" style="flex:1; min-width:180px">
          <select id="filterMode">
            <option value="all">Sur place + emporter</option>
            <option value="dine">Sur place</option>
            <option value="take">À emporter</option>
          </select>
          <select id="filterStatus">
            <option value="all">Tous statuts</option>
            <option value="pending">À payer</option>
            <option value="paid">Payé</option>
            <option value="cancelled">Annulé</option>
          </select>
          <button class="btn ghost sm" id="csvBtn">⬇ CSV complet</button>
          <button class="btn ghost sm" id="csvDineBtn">🍽️ CSV jour J (sur place)</button>
          <button class="btn ghost sm" id="csvTakeBtn">🥡 CSV à emporter</button>
        </div>
        <div class="table-scroll">
          <table>
            <thead><tr>
              <th>Réf</th><th>Nom</th><th>Contact</th><th>Mode</th><th>Plats</th><th>Total</th><th>Statut</th><th>Actions</th>
            </tr></thead>
            <tbody id="resaBody"></tbody>
          </table>
        </div>
      </div>

      <!-- TAB encoder (réservation prise par téléphone) -->
      <div data-pane="encoder" style="display:none">
        <p style="color:var(--muted); margin-top:0">Pour une réservation prise par téléphone. La communication structurée s'affiche à la fin : dicte-la à la personne pour son virement (elle la reçoit aussi par e-mail si tu en indiques un).</p>
        <div id="encArea">
          <div class="cfg-grid">
            <div class="field"><label>Nom et prénom *</label><input id="encName" placeholder="Ex. Marie Dupont"></div>
            <div class="field"><label>Téléphone</label><input id="encPhone" type="tel" placeholder="04xx xx xx xx"></div>
            <div class="field"><label>E-mail (facultatif, pour l'envoi des infos de paiement)</label><input id="encEmail" type="email"></div>
            <div class="field"><label>Où ?</label>
              <select id="encMode"><option value="dine">🍽️ Sur place</option><option value="take">🥡 À emporter</option></select></div>
          </div>
          <div class="qhead" style="margin-top:16px"><span>Plat</span><span>Adulte</span><span>Enfant</span></div>
          <div class="qty-table" id="encQty"></div>
          <div class="cfg-grid" style="margin-top:14px">
            <div class="field"><label>Remarque</label><input id="encNotes" placeholder="Allergie, heure de retrait…"></div>
            <div class="field"><label>Paiement</label>
              <select id="encPaid"><option value="0">À payer par virement (communication structurée)</option><option value="1">Déjà payé (cash / reçu)</option></select></div>
          </div>
          <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-top:14px">
            <b id="encTotal" style="font-family:'Fredoka'; font-size:1.3rem">Total : 0 €</b>
            <span id="encRem" style="color:var(--muted); font-size:.9rem"></span>
            <button class="btn" id="encSubmit" style="margin-left:auto">Enregistrer la réservation</button>
          </div>
          <div class="err" id="encErr"></div>
        </div>
      </div>

      <!-- TAB cuisine -->
      <div data-pane="cuisine" style="display:none">
        <div class="stats" id="kitchenStats"></div>
        <h4 style="font-size:1.05rem; margin:0 0 10px">Plats à préparer <span style="font-weight:400; color:var(--muted); font-size:.85rem">(payés / total réservé)</span></h4>
        <div class="table-scroll" style="max-height:none">
          <table id="kitchenTable"></table>
        </div>
        <h4 style="font-size:1.05rem; margin:22px 0 10px">🍽️ Sur place</h4>
        <ul class="prep-list" id="listDine"></ul>
        <h4 style="font-size:1.05rem; margin:22px 0 10px">🥡 À emporter</h4>
        <ul class="prep-list" id="listTake"></ul>
        <p style="color:var(--muted); font-size:.85rem">✅ = payé · ⏳ = en attente de paiement. Les réservations annulées ne sont pas comptées.</p>
      </div>

      <!-- TAB menu -->
      <div data-pane="menu" style="display:none">
        <p style="color:var(--muted); margin-top:0">Ajoute, modifie ou retire des plats. Les prix sont en euros. Un plat désactivé n'est plus proposé mais reste dans l'historique des réservations.</p>
        <div class="mhead"><span>Emoji</span><span>Nom du plat</span><span>Description</span><span>Adulte €</span><span>Enfant €</span><span>Actif</span><span></span></div>
        <div class="menu-editor" id="menuEditor"></div>
        <div style="display:flex; gap:10px; align-items:center; margin-top:14px; flex-wrap:wrap">
          <button class="btn ghost sm" id="addDish">＋ Ajouter un plat</button>
          <button class="btn sm" id="saveMenu">Enregistrer le menu</button>
          <span class="saved" id="menuSaved"></span>
        </div>
      </div>

      <!-- TAB réglages -->
      <div data-pane="reglages" style="display:none">
        <p style="color:var(--muted); margin-top:0">Tout ce qui s'affiche sur la page se règle ici.</p>
        <div class="cfg-grid">
          <div class="field"><label>Nom de l'événement</label><input data-cfg="eventName"></div>
          <div class="field"><label>Au profit de</label><input data-cfg="cause"></div>
          <div class="field"><label>Date</label><input data-cfg="date"></div>
          <div class="field"><label>Heure</label><input data-cfg="time"></div>
          <div class="field"><label>Lieu</label><input data-cfg="place"></div>
          <div class="field"><label>Objectif (€)</label><input data-cfg="goal" type="number" min="0"></div>
        </div>
        <hr class="sep">
        <div class="cfg-grid">
          <div class="field"><label>Places sur place (repas servis en salle)</label><input data-cfg="capDine" type="number" min="0"></div>
          <div class="field"><label>Repas à emporter (maximum)</label><input data-cfg="capTake" type="number" min="0"></div>
          <div class="field full"><label>Inclus pour tous (apéritif, dessert…)</label><input data-cfg="includedText"></div>
          <div class="field full"><label>Info retrait à emporter</label><input data-cfg="takeText"></div>
          <div class="field full"><label>Téléphone pour réserver sans paiement en ligne (vide = mention masquée)</label><input data-cfg="helpPhone" placeholder="04xx xx xx xx"></div>
          <div class="field full"><label>Texte « Et après le repas ? »</label><textarea data-cfg="afterText" rows="2"></textarea></div>
        </div>
        <hr class="sep">
        <div class="cfg-grid">
          <div class="field"><label>IBAN (virement)</label><input data-cfg="iban"></div>
          <div class="field"><label>Titulaire du compte</label><input data-cfg="accountName"></div>
        </div>
        <p style="color:var(--muted); font-size:.85rem">Le code d'accès organisateur se modifie dans le fichier <code>config.php</code> sur le serveur.</p>
        <button class="btn" id="saveCfg">Enregistrer les réglages</button>
        <span class="saved" id="cfgSaved"></span>
        <hr class="sep">
        <button class="btn ghost sm" id="wipeBtn" style="color:var(--red); box-shadow:inset 0 0 0 2px var(--red)">⚠ Effacer toutes les réservations</button>
      </div>
    </div>
  </div>
</div>

<script>
/* ============================================================
   Front-end — branché sur api.php (PHP/MySQL/Stripe/Brevo)
============================================================ */
const API = "api.php";
async function api(action, {method="GET", body=null} = {}){
  const opt = { method, credentials:"same-origin", headers:{} };
  if(body){ opt.headers["Content-Type"]="application/json"; opt.body=JSON.stringify(body); }
  const res = await fetch(API + "?action=" + action, opt);
  let data = {}; try { data = await res.json(); } catch(e){}
  return { ok: res.ok, status: res.status, data };
}
const esc = s => String(s ?? "").replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
const euro  = n => (Math.round(n*100)/100).toLocaleString("fr-BE",{minimumFractionDigits:0, maximumFractionDigits:2}) + " €";
const euroC = c => (Math.round(c)/100).toLocaleString("fr-BE",{minimumFractionDigits:2}) + " €";
const plural = (n, s, p) => n + " " + (n>1 ? p : s);
const VARIANTS = [{key:"adult", label:"Adulte", price:"priceAdult"}, {key:"child", label:"Enfant", price:"priceChild"}];
const MODES = {
  dine:{ ico:"🍽️", label:"Sur place", sub:"Servi à table dans la salle" },
  take:{ ico:"🥡", label:"À emporter", sub:"Tu viens chercher tes repas" },
};

let CFG = {}, MENU = [], REM = {dine:0, take:0}, STRIPE = false;
let mode = "dine";
let cart = {};                    // "dishId:variant" -> qty

/* ---------- état public ---------- */
async function loadState(){
  const { data } = await api("state");
  if(!data || !data.config) return;
  CFG = data.config; MENU = data.menu || []; REM = data.remaining || {dine:0,take:0};
  STRIPE = !!data.stripeEnabled;
  if(REM[mode] <= 0 && REM[mode==="dine"?"take":"dine"] > 0) mode = mode==="dine" ? "take" : "dine";
  applyConfigToDOM(); renderPublic();
}

function applyConfigToDOM(){
  const set = (id, v) => { const el=document.getElementById(id); if(el) el.textContent = v; };
  set("brandName", CFG.eventName); set("footName", CFG.eventName);
  set("heroCause", CFG.cause); set("leadCause", CFG.cause); set("vflCause", CFG.cause);
  set("mDate", CFG.date); set("mTime", CFG.time); set("mPlace", CFG.place);
  set("afterText", CFG.afterText || "");
  set("placeLine", `📍 ${CFG.place} · ${CFG.date} · ${CFG.time}`);
  document.title = CFG.eventName + " · Réservation du repas";
  const ph = CFG.helpPhone ? `📞 <b>Pas à l'aise avec le paiement en ligne ?</b> Réserve par téléphone au <a href="tel:${esc(CFG.helpPhone.replace(/\s+/g,""))}"><b>${esc(CFG.helpPhone)}</b></a> et paie sur le compte <b>${esc(CFG.iban)}</b>${CFG.accountName?` (${esc(CFG.accountName)})`:""}.` : "";
  for(const id of ["phoneHelp","phoneHelp2"]){ const el=document.getElementById(id); if(el){ el.style.display = ph ? "" : "none"; el.innerHTML = ph; } }
  const inc = document.getElementById("includedBox");
  if(CFG.includedText){ inc.style.display="flex"; set("includedText", CFG.includedText); } else inc.style.display="none";
  const pc = document.getElementById("payCard");
  if(pc) pc.style.display = STRIPE ? "" : "none";
}

/* ---------- menu public ---------- */
function renderDishes(){
  const box = document.getElementById("dishCards");
  if(!MENU.length){ box.innerHTML = `<div class="card dish"><p>Le menu arrive bientôt…</p></div>`; return; }
  box.innerHTML = MENU.map(d => `
    <div class="card dish">
      <div class="emoji">${esc(d.emoji || "🍝")}</div>
      <h3>${esc(d.name)}</h3>
      <p>${esc(d.description)}</p>
      <div class="prices">
        <div class="pr"><small>Adulte</small><b>${euro(d.priceAdult)}</b></div>
        <div class="pr child"><small>Enfant</small><b>${euro(d.priceChild)}</b></div>
      </div>
    </div>`).join("");
  document.getElementById("availRow").innerHTML = ["dine","take"].map(m => {
    const r = REM[m], cls = r<=0 ? "full" : (r<=10 ? "low" : "");
    return `<span class="avail ${cls}"><i></i>${MODES[m].ico} ${MODES[m].label} : ${r<=0 ? "complet" : plural(r, "place restante", "places restantes")}</span>`;
  }).join("");
}

/* ---------- réservation ---------- */
const cartTotal = () => Object.values(cart).reduce((s,q)=>s+q,0);
const cartAmount = () => {
  let t=0;
  for(const [k,q] of Object.entries(cart)){ const [id,v]=k.split(":"); const d=MENU.find(x=>x.id===+id); if(d) t += q*(v==="child"?d.priceChild:d.priceAdult); }
  return t;
};

function renderModes(){
  const box = document.getElementById("modeBox"); if(!box) return;
  box.innerHTML = ["dine","take"].map(m => {
    const r = REM[m], off = r<=0;
    return `<label class="mode ${mode===m?"on":""} ${off?"off":""}">
      <input type="radio" name="mode" value="${m}" ${mode===m?"checked":""} ${off?"disabled":""}>
      <span class="tick"></span>
      <div class="ico">${MODES[m].ico}</div>
      <b>${MODES[m].label}</b>
      <span>${off ? "Complet, désolé !" : MODES[m].sub + " · " + plural(r, "place", "places")}</span>
    </label>`;
  }).join("");
  const tn = document.getElementById("takeNote");
  if(mode==="take" && CFG.takeText){ tn.style.display=""; tn.textContent = "ℹ️ " + CFG.takeText; } else tn.style.display="none";
}

function renderQty(){
  const box = document.getElementById("qtyTable"); if(!box) return;
  const free = Math.max(0, REM[mode] - cartTotal());
  box.innerHTML = MENU.map(d => `
    <div class="qrow">
      <div class="dn"><span class="e">${esc(d.emoji||"🍝")}</span>${esc(d.name)}</div>
      ${VARIANTS.map(v => { const k=`${d.id}:${v.key}`, q=cart[k]||0; return `
      <div class="qcell">
        <div class="lab"><b>${v.label}</b><span>${euro(d[v.price])}</span></div>
        <div class="stepper">
          <button type="button" data-step="${k}" data-d="-1" aria-label="moins" ${q<=0?"disabled":""}>–</button>
          <input data-qty="${k}" value="${q}" inputmode="numeric" aria-label="${esc(d.name)} ${v.label} quantité">
          <button type="button" data-step="${k}" data-d="1" aria-label="plus" ${free<=0?"disabled":""}>+</button>
        </div>
      </div>`; }).join("")}
    </div>`).join("") || `<p style="color:var(--muted)">Aucun plat disponible pour le moment.</p>`;
}

function renderSummary(){
  const rowsEl = document.getElementById("sumRows"); if(!rowsEl) return;
  let rows = `<div class="sumrow mode"><span>${MODES[mode].ico} ${MODES[mode].label}</span><span></span></div>`;
  let any=false;
  for(const d of MENU) for(const v of VARIANTS){
    const q = cart[`${d.id}:${v.key}`]||0; if(q<=0) continue; any=true;
    rows += `<div class="sumrow"><span>${q} × ${esc(d.name)} <em style="color:#C3C9E3">(${v.label.toLowerCase()})</em></span><span>${euro(q*d[v.price])}</span></div>`;
  }
  if(!any) rows += `<div class="sumrow"><span style="color:#C3C9E3">Aucun plat choisi pour l'instant</span><span>—</span></div>`;
  rowsEl.innerHTML = rows;
  document.getElementById("sumTotal").textContent = euro(cartAmount());
  const pc=document.getElementById("payCard"), pt=document.getElementById("payTransfer");
  if(pc) pc.disabled=!any; if(pt) pt.disabled=!any;
}

function renderPublic(){ renderDishes(); renderModes(); renderQty(); renderSummary(); }

function setQty(key, val){
  let v = parseInt(val||0,10); if(isNaN(v)||v<0) v=0;
  const others = cartTotal() - (cart[key]||0);
  v = Math.min(v, Math.max(0, REM[mode] - others));
  if(v>0) cart[key]=v; else delete cart[key];
  renderQty(); renderSummary();
}
function setMode(m){
  if(REM[m]<=0) return;
  mode = m;
  // si on dépasse la capacité du nouveau mode, on réduit les quantités
  let over = cartTotal() - REM[mode];
  for(const k of Object.keys(cart).reverse()){ if(over<=0) break; const cut=Math.min(over, cart[k]); cart[k]-=cut; over-=cut; if(cart[k]<=0) delete cart[k]; }
  renderModes(); renderQty(); renderSummary();
}

async function book(method){
  const name = document.getElementById("fName").value.trim();
  const email= document.getElementById("fEmail").value.trim();
  const phone= document.getElementById("fPhone").value.trim();
  const notes= "";
  const err  = document.getElementById("formErr"); err.textContent="";

  if(cartTotal()<=0){ err.textContent="Choisis au moins un plat (étape 2)."; return; }
  if(!name){ err.textContent="Indique ton nom."; document.getElementById("fName").focus(); return; }
  if(!email && !phone){ err.textContent="Laisse un e-mail ou un numéro de téléphone."; return; }
  if(method==="stripe" && !email){ err.textContent="Un e-mail est requis pour le paiement par carte."; return; }

  const btns=[document.getElementById("payCard"),document.getElementById("payTransfer")];
  btns.forEach(b=>{ if(b) b.disabled=true; });
  err.style.color="var(--muted)"; err.textContent="Un instant, on enregistre ta réservation…";

  const items = Object.entries(cart).map(([k,q])=>{ const [id,v]=k.split(":"); return {dish:+id, variant:v, qty:q}; });
  const { data, status } = await api("book", { method:"POST", body:{ name, email, phone, notes, mode, items, method }});
  err.style.color="";

  if(status===409 && data.error==="sold"){
    err.textContent="Oups, des places viennent de partir ! Les disponibilités ont été mises à jour.";
    await loadState(); return;
  }
  if(!data.ok){
    const msg = {name:"Nom manquant.", contact:"Contact manquant.", empty:"Aucun plat choisi.", email_required:"E-mail requis pour la carte."}[data.error] || "Une erreur est survenue, réessaie dans un instant.";
    err.textContent=msg; renderSummary(); return;
  }
  if(data.mode==="stripe"){ window.location.href = data.url; return; }
  showTicket(data.booking, data.warning);
  cart={};
  await loadState();
}

function itemsHtml(b){
  const items = (b.items||[]).map(it => `<div>${it.qty} × ${esc(it.name)} <span style="color:var(--muted)">(${it.variant==="child"?"enfant":"adulte"})</span></div>`).join("");
  const m = MODES[b.mode] || MODES.dine;
  return `<div class="result-lines"><b>${m.ico} ${m.label}</b>${b.mode==="take"&&CFG.takeText?`<div style="color:var(--muted);font-size:.85rem">${esc(CFG.takeText)}</div>`:""}${items}</div>`;
}

function showTicket(b, warning){
  const first = esc((b.name||"").split(" ")[0]);
  const mailNote = b.email ? "Ces infos t'ont aussi été envoyées par e-mail." : "";
  document.getElementById("bookingArea").innerHTML = `
    <div class="card result">
      <div class="check">✓</div>
      <h3>C'est noté, ${first} !</h3>
      <p style="color:var(--muted); margin:6px 0 0">Il ne reste plus qu'à faire le virement pour confirmer.</p>
      ${itemsHtml(b)}
      ${warning?`<p style="color:#8A5B00; font-size:.9rem; margin-top:10px">${esc(warning)}</p>`:""}
      <div class="pay-box">
        <span class="l" style="margin-top:0">Montant à virer</span>
        <p class="v" style="font-size:1.6rem">${euroC(b.amount)}</p>
        <span class="l">IBAN</span>
        <p class="v">${esc(CFG.iban)} <button class="copybtn" data-copy="${esc(CFG.iban)}">copier</button></p>
        <span class="l">Communication structurée</span>
        <p class="v">${esc(b.ref)} <button class="copybtn" data-copy="${esc(b.ref)}">copier</button></p>
        <span class="l">Bénéficiaire</span>
        <p class="v" style="font-size:.95rem">${esc(CFG.accountName)}</p>
      </div>
      <p style="font-size:.92rem; color:#3E4667">Indique <b>exactement cette communication</b> dans ton virement. Ta réservation est confirmée dès réception. ${mailNote}</p>
      <button class="btn ghost" onclick="location.href='index.php'" style="margin-top:12px">Faire une autre réservation</button>
    </div>`;
  document.getElementById("reserver").scrollIntoView({behavior:"smooth"});
}

function showConfirmed(b){
  const first = esc((b.name||"").split(" ")[0]);
  document.getElementById("bookingArea").innerHTML = `
    <div class="card result">
      <div class="check">✓</div>
      <h3>Merci ${first}, c'est confirmé !</h3>
      <p style="color:var(--muted); margin:6px 0 0">Paiement reçu. Un e-mail de confirmation t'a été envoyé.</p>
      ${itemsHtml(b)}
      <p style="margin-top:14px"><b>${esc(CFG.date)}</b> · ${esc(CFG.time)} · ${esc(CFG.place)}</p>
      <p style="font-size:.85rem; color:var(--muted)">Référence : ${esc(b.ref)}</p>
      <button class="btn ghost" onclick="location.href='index.php'" style="margin-top:8px">Faire une autre réservation</button>
    </div>`;
}

async function handleReturn(){
  const p = new URLSearchParams(location.search);
  const st = p.get("status"), ref = p.get("ref");
  if(!st) return;
  const banner = document.getElementById("statusBanner");
  const wrap = (bg,txt)=>`<div style="background:${bg};color:#fff;text-align:center;padding:12px 18px;font-weight:700">${txt}</div>`;
  if(st==="cancel"){
    banner.innerHTML = wrap("var(--gold)","Paiement annulé — les places ont été libérées. Tu peux réessayer ci-dessous.");
    if(ref){ try{ await api("cancel_pending", {method:"POST", body:{ref}}); }catch(e){} }
    loadState();
    return;
  }
  if(st==="success" && ref){
    banner.innerHTML = wrap("var(--green)","Merci ! On vérifie ton paiement…");
    document.getElementById("reserver").scrollIntoView();
    for(let i=0;i<6;i++){
      const { data } = await api("lookup&ref="+encodeURIComponent(ref));
      if(data && data.status==="paid"){
        banner.innerHTML = "";
        showConfirmed(data); loadState(); return;
      }
      await new Promise(r=>setTimeout(r,1500));
    }
    banner.innerHTML = wrap("var(--green)","Paiement reçu — la confirmation arrive par e-mail dans un instant. 🎉");
    loadState();
  }
}

/* ===================== ADMIN ===================== */
let ADMIN = { bookings:[], caps:{}, used:{}, remaining:{}, raisedCents:0, config:{}, menu:[] };
let EDIT_MENU = [];
let activeTab = "resa";
function openAdmin(){ document.getElementById("adminModal").classList.add("open"); }
function closeAdmin(){ document.getElementById("adminModal").classList.remove("open"); }

async function adminLogin(){
  const pass = document.getElementById("adminPass").value;
  const { data } = await api("admin_login", { method:"POST", body:{ password:pass } });
  if(data.ok){
    document.getElementById("adminGate").style.display="none";
    document.getElementById("adminDash").style.display="block";
    await loadAdmin();
  } else {
    document.getElementById("adminErr").textContent="Code incorrect.";
  }
}

async function loadAdmin(){
  const { data, status } = await api("admin_state");
  if(status===401){
    document.getElementById("adminGate").style.display="block";
    document.getElementById("adminDash").style.display="none";
    return;
  }
  ADMIN = data;
  EDIT_MENU = (ADMIN.menu||[]).map(d=>({...d}));
  renderAdmin();
}

const badge = s => s==="paid" ? '<span class="pill paid">Payé</span>' : s==="cancelled" ? '<span class="pill cancel">Annulé</span>' : '<span class="pill pending">À payer</span>';
const modePill = m => m==="take" ? '<span class="pill take">🥡 Emporter</span>' : '<span class="pill dine">🍽️ Sur place</span>';
const itemsShort = b => (b.items||[]).map(it=>`${it.qty} ${esc(it.name)} <span style="color:var(--muted)">${it.variant==="child"?"enf.":"ad."}</span>`).join(", ");

function renderAdmin(){
  const B = ADMIN.bookings||[];
  const used = ADMIN.used||{dine:0,take:0};
  const caps = ADMIN.caps||{};
  const live = B.filter(b=>b.status!=="cancelled");
  const potential = live.reduce((s,b)=>s+b.amount,0);
  const meals = live.reduce((s,b)=>s+b.meals,0);
  document.getElementById("statBoxes").innerHTML = `
    <div class="stat"><div class="n">${euroC(ADMIN.raisedCents)}</div><div class="k">Encaissé</div></div>
    <div class="stat"><div class="n">${euroC(potential-ADMIN.raisedCents)}</div><div class="k">En attente</div></div>
    <div class="stat"><div class="n">${live.length}</div><div class="k">Réservations</div></div>
    <div class="stat"><div class="n">${meals}</div><div class="k">Repas à prévoir</div></div>`;
  const g = [
    {lbl:"🍽️ Sur place", used:used.dine, cap:caps.capDine},
    {lbl:"🥡 À emporter", used:used.take, cap:caps.capTake},
  ];
  document.getElementById("gaugeBoxes").innerHTML = g.map(x=>{
    const pct = x.cap>0?Math.min(100,Math.round(x.used/x.cap*100)):0;
    return `<div class="gauge"><div class="top"><span>${x.lbl}</span><b>${x.used} / ${x.cap}</b></div>
      <div class="bar"><i style="width:${pct}%"></i></div></div>`;
  }).join("");
  renderResaTable();
  renderKitchen();
  renderMenuEditor();
}

function renderResaTable(){
  const q = (document.getElementById("search").value||"").toLowerCase();
  const fs = document.getElementById("filterStatus").value;
  const fm = document.getElementById("filterMode").value;
  const rows = (ADMIN.bookings||[]).filter(b=>{
    if(fs!=="all" && b.status!==fs) return false;
    if(fm!=="all" && b.mode!==fm) return false;
    if(q){ const hay=((b.name||"")+" "+(b.email||"")+" "+(b.phone||"")+" "+b.ref).toLowerCase(); if(!hay.includes(q)) return false; }
    return true;
  });
  document.getElementById("resaBody").innerHTML = rows.length ? rows.map(b=>{
    const tag = b.method==="stripe"?'<span style="font-size:.7rem;color:var(--muted)"> · carte</span>':'';
    return `<tr>
      <td style="font-family:monospace; font-size:.76rem">${esc(b.ref)}</td>
      <td><b>${esc(b.name)}</b>${tag}${b.notes?`<br><span style="color:var(--muted); font-size:.78rem">📝 ${esc(b.notes)}</span>`:''}</td>
      <td style="font-size:.8rem">${esc(b.email||'')}${b.email&&b.phone?'<br>':''}${esc(b.phone||'')}</td>
      <td>${modePill(b.mode)}</td>
      <td style="font-size:.82rem">${itemsShort(b)}</td>
      <td><b>${euroC(b.amount)}</b></td>
      <td>${badge(b.status)}</td>
      <td class="tbtns">
        ${b.status!=="paid"?`<button data-act="paid" data-id="${b.id}">✓ Payé</button>`:''}
        ${b.status==="paid"?`<button data-act="pending" data-id="${b.id}">↩ Non payé</button>`:''}
        ${b.status!=="cancelled"?`<button data-act="cancelled" data-id="${b.id}">✕ Annuler</button>`:`<button data-act="pending" data-id="${b.id}">↩ Rétablir</button><button data-del2="${b.id}" style="color:var(--red)">🗑 Supprimer</button>`}
      </td>
    </tr>`;
  }).join("") : `<tr><td colspan="8" style="text-align:center; color:var(--muted); padding:30px">Aucune réservation.</td></tr>`;
}

function renderKitchen(){
  const live = (ADMIN.bookings||[]).filter(b=>b.status!=="cancelled");
  const paid = b=>b.status==="paid";
  const sum = (arr, m) => arr.filter(b=>b.mode===m).reduce((s,b)=>s+b.meals,0);
  const dineAll=sum(live,"dine"), takeAll=sum(live,"take"), dinePaid=sum(live.filter(paid),"dine"), takePaid=sum(live.filter(paid),"take");
  document.getElementById("kitchenStats").innerHTML = `
    <div class="stat"><div class="n">${dinePaid}<span style="font-size:.9rem; color:var(--muted)"> / ${dineAll}</span></div><div class="k">Sur place (payés / total)</div></div>
    <div class="stat"><div class="n">${takePaid}<span style="font-size:.9rem; color:var(--muted)"> / ${takeAll}</span></div><div class="k">À emporter (payés / total)</div></div>
    <div class="stat"><div class="n">${dinePaid+takePaid}</div><div class="k">Repas confirmés</div></div>
    <div class="stat"><div class="n">${dineAll+takeAll}</div><div class="k">Repas réservés</div></div>`;

  // matrice plat × (mode, variante)
  const agg = {};   // name -> {dine:{adult:[paid,total],child:[..]}, take:{...}}
  for(const b of live) for(const it of (b.items||[])){
    const a = agg[it.name] ??= {dine:{adult:[0,0],child:[0,0]}, take:{adult:[0,0],child:[0,0]}};
    const cell = a[b.mode][it.variant]; cell[1]+=it.qty; if(paid(b)) cell[0]+=it.qty;
  }
  const cell = c => `<td class="num"><b>${c[0]}</b> <span style="color:var(--muted)">/ ${c[1]}</span></td>`;
  const names = Object.keys(agg);
  const tot = {dine:{adult:[0,0],child:[0,0]}, take:{adult:[0,0],child:[0,0]}};
  for(const n of names) for(const m of ["dine","take"]) for(const v of ["adult","child"]){ tot[m][v][0]+=agg[n][m][v][0]; tot[m][v][1]+=agg[n][m][v][1]; }
  document.getElementById("kitchenTable").innerHTML = names.length ? `
    <thead><tr><th>Plat</th><th class="num">🍽️ Adulte</th><th class="num">🍽️ Enfant</th><th class="num">🥡 Adulte</th><th class="num">🥡 Enfant</th><th class="num">Total</th></tr></thead>
    <tbody>${names.map(n=>{ const a=agg[n]; const t=[a.dine.adult,a.dine.child,a.take.adult,a.take.child].reduce((s,c)=>[s[0]+c[0],s[1]+c[1]],[0,0]);
      return `<tr><td><b>${esc(n)}</b></td>${cell(a.dine.adult)}${cell(a.dine.child)}${cell(a.take.adult)}${cell(a.take.child)}${cell(t)}</tr>`; }).join("")}
      <tr style="background:var(--cream)"><td><b>Total</b></td>${cell(tot.dine.adult)}${cell(tot.dine.child)}${cell(tot.take.adult)}${cell(tot.take.child)}<td class="num"><b>${dinePaid+takePaid}</b> <span style="color:var(--muted)">/ ${dineAll+takeAll}</span></td></tr>
    </tbody>` : `<tbody><tr><td style="color:var(--muted); text-align:center; padding:20px">Rien à préparer pour l'instant.</td></tr></tbody>`;

  const li = (m)=>live.filter(b=>b.mode===m).map(b=>`<li>${paid(b)?'✅':'⏳'} <b>${esc(b.name)}</b> — ${itemsShort(b)}${b.notes?` <span style="color:var(--muted)">(${esc(b.notes)})</span>`:''}</li>`).join("");
  document.getElementById("listDine").innerHTML = li("dine") || '<li style="color:var(--muted)">Personne pour l\'instant.</li>';
  document.getElementById("listTake").innerHTML = li("take") || '<li style="color:var(--muted)">Personne pour l\'instant.</li>';
}

/* ---------- encodage d'une réservation par téléphone ---------- */
let encCart = {};
const encTotal = () => Object.values(encCart).reduce((s,q)=>s+q,0);
function renderEnc(){
  const box = document.getElementById("encQty"); if(!box) return;
  const menu = (ADMIN.menu||[]).filter(d=>d.active);
  const m = document.getElementById("encMode").value;
  const rem = (ADMIN.remaining||{})[m] ?? 0;
  const free = Math.max(0, rem - encTotal());
  box.innerHTML = menu.map(d => `
    <div class="qrow">
      <div class="dn"><span class="e">${esc(d.emoji||"🍝")}</span>${esc(d.name)}</div>
      ${VARIANTS.map(v => { const k=`${d.id}:${v.key}`, q=encCart[k]||0; return `
      <div class="qcell">
        <div class="lab"><b>${v.label}</b><span>${euro(d[v.price])}</span></div>
        <div class="stepper">
          <button type="button" data-estep="${k}" data-d="-1" ${q<=0?"disabled":""}>–</button>
          <input data-eqty="${k}" value="${q}" inputmode="numeric">
          <button type="button" data-estep="${k}" data-d="1" ${free<=0?"disabled":""}>+</button>
        </div>
      </div>`; }).join("")}
    </div>`).join("") || `<p style="color:var(--muted)">Aucun plat actif au menu.</p>`;
  let total=0;
  for(const [k,q] of Object.entries(encCart)){ const [id,v]=k.split(":"); const d=menu.find(x=>x.id===+id); if(d) total += q*(v==="child"?d.priceChild:d.priceAdult); }
  document.getElementById("encTotal").textContent = "Total : " + euro(total);
  document.getElementById("encRem").textContent = `${MODES[m].label} : ${plural(rem,"place restante","places restantes")}`;
}
function setEncQty(key, val){
  let v = parseInt(val||0,10); if(isNaN(v)||v<0) v=0;
  const m = document.getElementById("encMode").value;
  const others = encTotal() - (encCart[key]||0);
  v = Math.min(v, Math.max(0, ((ADMIN.remaining||{})[m] ?? 0) - others));
  if(v>0) encCart[key]=v; else delete encCart[key];
  renderEnc();
}
async function encSubmit(){
  const err = document.getElementById("encErr"); err.textContent="";
  const name = document.getElementById("encName").value.trim();
  if(!name){ err.textContent="Le nom est obligatoire."; return; }
  if(encTotal()<=0){ err.textContent="Choisis au moins un plat."; return; }
  const items = Object.entries(encCart).map(([k,q])=>{ const [id,v]=k.split(":"); return {dish:+id, variant:v, qty:q}; });
  const body = { name, phone:document.getElementById("encPhone").value.trim(), email:document.getElementById("encEmail").value.trim(),
    mode:document.getElementById("encMode").value, notes:document.getElementById("encNotes").value.trim(),
    paid:document.getElementById("encPaid").value==="1", items };
  const btn = document.getElementById("encSubmit"); btn.disabled=true;
  const { data, status } = await api("admin_book", { method:"POST", body });
  btn.disabled=false;
  if(status===409){ err.textContent="Plus assez de places pour ce mode."; await loadAdmin(); renderEnc(); return; }
  if(!data.ok){ err.textContent="Erreur : " + (data.error||"réessaie."); return; }
  const b = data.booking;
  document.getElementById("encArea").innerHTML = `
    <div class="card result" style="box-shadow:none">
      <div class="check">✓</div>
      <h3>Réservation enregistrée</h3>
      <p style="color:var(--muted); margin:6px 0 0"><b>${esc(b.name)}</b> · ${MODES[b.mode].label} · ${b.meals} repas · ${b.status==="paid"?"payée":"à payer"}</p>
      ${itemsHtml(b)}
      ${b.status!=="paid" ? `<div class="pay-box">
        <span class="l" style="margin-top:0">Montant</span><p class="v" style="font-size:1.5rem">${euroC(b.amount)}</p>
        <span class="l">Communication structurée à dicter</span><p class="v" style="font-size:1.4rem">${esc(b.ref)} <button class="copybtn" data-copy="${esc(b.ref)}">copier</button></p>
        <span class="l">IBAN</span><p class="v">${esc(CFG.iban)}</p>
      </div>` : `<p style="margin-top:14px">Montant : <b>${euroC(b.amount)}</b> · Réf ${esc(b.ref)}</p>`}
      <button class="btn" id="encAgain" style="margin-top:12px">＋ Encoder une autre réservation</button>
    </div>`;
  await loadAdmin(); loadState();
}
const ENC_FORM_HTML = document.getElementById("encArea").innerHTML;
function resetEnc(){
  document.getElementById("encArea").innerHTML = ENC_FORM_HTML; encCart = {}; renderEnc();
  document.getElementById("encName").focus();
}

/* ---------- éditeur de menu ---------- */
function renderMenuEditor(){
  const box = document.getElementById("menuEditor"); if(!box) return;
  box.innerHTML = EDIT_MENU.map((d,i)=>`
    <div class="mrow ${d.active?'':'inactive'}">
      <input data-m="emoji" data-i="${i}" value="${esc(d.emoji)}" placeholder="🍝" aria-label="Emoji">
      <input data-m="name" data-i="${i}" value="${esc(d.name)}" placeholder="Nom du plat" aria-label="Nom">
      <input data-m="description" data-i="${i}" value="${esc(d.description)}" placeholder="Description courte" aria-label="Description">
      <input data-m="priceAdult" data-i="${i}" type="number" min="0" step="0.5" value="${d.priceAdult}" aria-label="Prix adulte">
      <input data-m="priceChild" data-i="${i}" type="number" min="0" step="0.5" value="${d.priceChild}" aria-label="Prix enfant">
      <input data-m="active" data-i="${i}" type="checkbox" ${d.active?'checked':''} aria-label="Actif">
      <div class="acts">
        <button type="button" data-mv="-1" data-i="${i}" title="Monter" ${i===0?'disabled':''}>↑</button>
        <button type="button" data-mv="1" data-i="${i}" title="Descendre" ${i===EDIT_MENU.length-1?'disabled':''}>↓</button>
        <button type="button" data-del="${i}" title="Supprimer" style="color:var(--red)">✕</button>
      </div>
    </div>`).join("") || `<p style="color:var(--muted)">Aucun plat. Clique sur « Ajouter un plat ».</p>`;
}
async function saveMenu(){
  const dishes = EDIT_MENU.filter(d=>(d.name||"").trim());
  const { data } = await api("admin_menu", { method:"POST", body:{ dishes } });
  if(data.ok){
    EDIT_MENU = (data.menu||[]).map(d=>({...d})); renderMenuEditor();
    const s=document.getElementById("menuSaved"); s.textContent="✓ Menu enregistré"; setTimeout(()=>s.textContent="",2500);
    await loadState();
  }
}

async function deleteBooking(id){
  const b = (ADMIN.bookings||[]).find(x=>x.id===Number(id));
  if(!b || !confirm(`Supprimer définitivement la réservation annulée de « ${b.name} » ?`)) return;
  await api("admin_delete", { method:"POST", body:{ id:Number(id) } });
  await loadAdmin(); loadState();
}

async function adminAction(id, status){
  await api("admin_update", { method:"POST", body:{ id:Number(id), status } });
  await loadAdmin(); loadState();
}

function downloadCSV(name, head, rows){
  const csv = "\uFEFF"+[head, ...rows].map(r=>r.map(v=>`"${String(v).replace(/"/g,'""')}"`).join(";")).join("\n");
  const blob = new Blob([csv],{type:"text/csv;charset=utf-8"});
  const a=document.createElement("a"); a.href=URL.createObjectURL(blob);
  a.download=name+"_"+(CFG.eventName||"event").replace(/\s+/g,"_")+".csv"; a.click();
}
/* Liste pour le jour J : une ligne par réservation (non annulée), 2 colonnes par plat (adulte / enfant) */
function exportDayCSV(mode){
  const dishes = (ADMIN.menu||[]).map(d=>d.name);
  for(const b of ADMIN.bookings||[]) for(const it of (b.items||[])) if(!dishes.includes(it.name)) dishes.push(it.name);
  const head = ["Nom", "Nb repas", ...dishes.flatMap(n=>[n+" adulte", n+" enfant"]), "Paye", "Telephone", "Remarque"];
  const rows = (ADMIN.bookings||[])
    .filter(b=>b.status!=="cancelled" && b.mode===mode)
    .sort((a,b)=>a.name.localeCompare(b.name,"fr",{sensitivity:"base"}))
    .map(b=>{
      const q = {}; for(const it of (b.items||[])) q[it.name+"|"+it.variant] = (q[it.name+"|"+it.variant]||0) + it.qty;
      return [b.name, b.meals, ...dishes.flatMap(n=>[q[n+"|adult"]||0, q[n+"|child"]||0]),
              b.status==="paid"?"oui":"NON", b.phone||"", (b.notes||"").replace(/[\n;]/g," ")];
    });
  downloadCSV(mode==="take"?"emporter":"jourJ_sur_place", head, rows);
}
function exportCSV(){
  const head = ["Reference","Nom","Email","Telephone","Mode","Plats","Nb_repas","Montant_EUR","Statut","Methode","Date","Notes"];
  const sl = {paid:"Paye", pending:"A payer", cancelled:"Annule"};
  const ml = {stripe:"Carte", transfer:"Virement"};
  const rows = (ADMIN.bookings||[]).map(b=>[
    b.ref,b.name,b.email||"",b.phone||"", b.mode==="take"?"A emporter":"Sur place",
    (b.items||[]).map(it=>`${it.qty} x ${it.name} (${it.variant==="child"?"enfant":"adulte"})`).join(" | "),
    b.meals,(b.amount/100).toFixed(2),sl[b.status],ml[b.method],b.created,(b.notes||"").replace(/[\n;]/g," ")
  ].map(v=>`"${String(v).replace(/"/g,'""')}"`).join(";"));
  const csv = "﻿"+[head.join(";"),...rows].join("\n");
  const blob = new Blob([csv],{type:"text/csv;charset=utf-8"});
  const a=document.createElement("a"); a.href=URL.createObjectURL(blob);
  a.download="reservations_"+(CFG.eventName||"event").replace(/\s+/g,"_")+".csv"; a.click();
}

function fillCfgForm(){
  document.querySelectorAll("[data-cfg]").forEach(inp=>{
    const k=inp.dataset.cfg; inp.value = (ADMIN.config && ADMIN.config[k]!=null) ? ADMIN.config[k] : "";
  });
}
async function saveCfg(){
  const numKeys=["goal","capDine","capTake"];
  const body={};
  document.querySelectorAll("[data-cfg]").forEach(inp=>{
    const k=inp.dataset.cfg;
    body[k] = numKeys.includes(k) ? (parseFloat(inp.value)||0) : inp.value;
  });
  await api("admin_config", { method:"POST", body });
  await loadAdmin(); await loadState();
  const s=document.getElementById("cfgSaved"); s.textContent="✓ Enregistré"; setTimeout(()=>s.textContent="",2500);
}
async function wipeAll(){
  if(!confirm("Effacer définitivement TOUTES les réservations ?")) return;
  await api("admin_wipe", { method:"POST", body:{} });
  await loadAdmin(); loadState();
}

/* ===================== ÉVÉNEMENTS ===================== */
document.addEventListener("click", (e)=>{
  if(e.target.id==="adminModal"){ closeAdmin(); return; }   // clic sur le fond uniquement
  const t=e.target.closest("button, [data-copy], [data-close]");
  if(!t) return;
  if(t.dataset.step){ const k=t.dataset.step; setQty(k, (cart[k]||0)+parseInt(t.dataset.d,10)); }
  if(t.dataset.copy){ navigator.clipboard?.writeText(t.dataset.copy); t.textContent="copié ✓"; setTimeout(()=>t.textContent="copier",1500); }
  if(t.dataset.act && t.dataset.id){ adminAction(t.dataset.id, t.dataset.act); }
  if(t.hasAttribute("data-close")){ closeAdmin(); }
  if(t.dataset.del2){ deleteBooking(t.dataset.del2); }
  if(t.dataset.tab){
    activeTab=t.dataset.tab;
    document.querySelectorAll(".tabs button").forEach(b=>b.classList.toggle("on", b.dataset.tab===activeTab));
    document.querySelectorAll("[data-pane]").forEach(p=>p.style.display = p.dataset.pane===activeTab?"block":"none");
    if(activeTab==="reglages") fillCfgForm();
    if(activeTab==="encoder"){ renderEnc(); const n=document.getElementById("encName"); if(n) n.focus(); }
  }
  if(t.dataset.estep){ const k=t.dataset.estep; setEncQty(k, (encCart[k]||0)+parseInt(t.dataset.d,10)); }
  if(t.id==="encSubmit") encSubmit();
  if(t.id==="encAgain") resetEnc();
  if(t.dataset.mv){ const i=+t.dataset.i, j=i+parseInt(t.dataset.mv,10); if(j>=0&&j<EDIT_MENU.length){ [EDIT_MENU[i],EDIT_MENU[j]]=[EDIT_MENU[j],EDIT_MENU[i]]; renderMenuEditor(); } }
  if(t.dataset.del!==undefined){ const d=EDIT_MENU[+t.dataset.del]; if(!d.name || confirm(`Supprimer « ${d.name} » du menu ?`)){ EDIT_MENU.splice(+t.dataset.del,1); renderMenuEditor(); } }
});
document.addEventListener("change", e=>{
  const t=e.target;
  if(t.name==="mode") setMode(t.value);
  if(t.id==="encMode"){ encCart={}; renderEnc(); }
  if(t.dataset.m){ const d=EDIT_MENU[+t.dataset.i]; if(!d) return;
    if(t.type==="checkbox") d[t.dataset.m]=t.checked; else if(t.type==="number") d[t.dataset.m]=parseFloat(t.value)||0; else d[t.dataset.m]=t.value;
    if(t.type==="checkbox") renderMenuEditor(); }
});
document.addEventListener("input", e=>{
  const t=e.target;
  if(t.dataset.qty) setQty(t.dataset.qty, t.value);
  if(t.dataset.eqty) setEncQty(t.dataset.eqty, t.value);
  if(t.dataset.m && t.type!=="checkbox"){ const d=EDIT_MENU[+t.dataset.i]; if(d) d[t.dataset.m] = t.type==="number" ? (parseFloat(t.value)||0) : t.value; }
});
document.getElementById("payCard").addEventListener("click", ()=>book("stripe"));
document.getElementById("payTransfer").addEventListener("click", ()=>book("transfer"));
document.getElementById("adminOpen").addEventListener("click", openAdmin);
document.getElementById("adminLogin").addEventListener("click", adminLogin);
document.getElementById("adminPass").addEventListener("keydown", e=>{ if(e.key==="Enter") adminLogin(); });
document.getElementById("refreshBtn").addEventListener("click", loadAdmin);
document.getElementById("search").addEventListener("input", renderResaTable);
document.getElementById("filterStatus").addEventListener("change", renderResaTable);
document.getElementById("filterMode").addEventListener("change", renderResaTable);
document.getElementById("csvBtn").addEventListener("click", exportCSV);
document.getElementById("csvDineBtn").addEventListener("click", ()=>exportDayCSV("dine"));
document.getElementById("csvTakeBtn").addEventListener("click", ()=>exportDayCSV("take"));
document.getElementById("saveCfg").addEventListener("click", saveCfg);
document.getElementById("saveMenu").addEventListener("click", saveMenu);
document.getElementById("addDish").addEventListener("click", ()=>{ EDIT_MENU.push({id:0, name:"", description:"", emoji:"🍝", priceAdult:14, priceChild:8, active:true}); renderMenuEditor(); });
document.getElementById("wipeBtn").addEventListener("click", wipeAll);

(async function init(){
  await loadState();
  handleReturn();
})();
</script>
</body>
</html>
